<?php

namespace Tests\Feature;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class DashboardRedesignTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_only_super_administrators_see_user_totals_and_management_links(): void
    {
        $admin = User::factory()->admin()->create();
        $superAdmin = User::factory()->superAdmin()->create();

        $this->actingAs($admin)->get(route('admin.dashboard'))
            ->assertOk()->assertViewHas('totalUsers', null)
            ->assertDontSee('Total Users')->assertDontSee(route('admin.users.index'));
        $this->actingAs($superAdmin)->get(route('admin.dashboard'))
            ->assertOk()->assertViewHas('totalUsers', 2)
            ->assertSee('Total Users')->assertSee(route('admin.users.index'));
    }

    public function test_monthly_chart_fills_missing_months_across_year_boundaries(): void
    {
        $this->travelTo(now()->setDate(2026, 2, 10)->setTime(12, 0));
        $admin = User::factory()->admin()->create();
        foreach (['2025-08-31', '2025-09-01', '2025-12-20', '2026-02-10', '2026-02-11'] as $date) {
            Ticket::factory()->for($admin)->create(['created_at' => $date.' 00:00:00']);
        }

        $this->actingAs($admin)->get(route('admin.dashboard'))->assertOk()
            ->assertViewHas('chartData', fn (array $data): bool => $data['monthly']['labels'] === ['Sep 2025', 'Oct 2025', 'Nov 2025', 'Dec 2025', 'Jan 2026', 'Feb 2026']
                && $data['monthly']['values'] === [1, 0, 0, 1, 0, 1]);
    }

    public function test_dashboard_counts_and_recent_tickets_come_from_saved_records(): void
    {
        $admin = User::factory()->admin()->create();
        Ticket::factory()->for($admin)->count(11)->create(['status' => 'Open', 'priority' => 'High']);
        $latest = Ticket::factory()->for($admin)->create(['status' => 'Resolved', 'priority' => 'Low', 'assigned_to' => $admin->id, 'created_at' => now()->addMinute()]);

        $this->actingAs($admin)->get(route('admin.dashboard'))->assertOk()
            ->assertViewHas('statistics', fn (array $statistics): bool => $statistics['Total Tickets'] === 12 && $statistics['Open Tickets'] === 11 && $statistics['High Priority'] === 11 && $statistics['Resolved Tickets'] === 1)
            ->assertViewHas('recentTickets', fn ($tickets): bool => $tickets->count() === 10 && $tickets->first()->is($latest) && $tickets->every(fn (Ticket $ticket): bool => $ticket->relationLoaded('user') && $ticket->relationLoaded('assignee')))
            ->assertViewHas('assignedStatistics', fn ($counts): bool => $counts['Resolved'] === 1);
    }

    public function test_assigned_queue_filters_to_the_authenticated_administrator_and_keeps_other_filters(): void
    {
        $admin = User::factory()->admin()->create();
        $other = User::factory()->admin()->create();
        $mine = Ticket::factory()->for($other)->open()->create(['assigned_to' => $admin->id]);
        Ticket::factory()->for($other)->open()->create(['assigned_to' => $other->id]);
        Ticket::factory()->for($other)->create(['assigned_to' => $admin->id, 'status' => 'Closed']);

        $this->actingAs($admin)->get(route('admin.tickets.index', ['assigned' => 'me', 'status' => 'Open']))
            ->assertOk()->assertViewHas('tickets', fn ($tickets): bool => $tickets->count() === 1 && $tickets->first()->is($mine))
            ->assertSee('name="assigned" value="me"', false);
    }

    public function test_employee_dashboard_does_not_expose_other_users_tickets_or_admin_navigation(): void
    {
        $employee = User::factory()->create();
        $mine = Ticket::factory()->for($employee)->open()->create();
        $private = Ticket::factory()->create(['subject' => 'Confidential other employee request']);

        $this->actingAs($employee)->get(route('dashboard'))->assertOk()
            ->assertSee($mine->ticket_number)->assertDontSee($private->subject)
            ->assertDontSee(route('admin.reports.index'))->assertDontSee('Total Users')
            ->assertViewHas('statistics', fn (array $statistics): bool => $statistics['Total Tickets'] === 1);
        $this->get(route('admin.dashboard'))->assertForbidden();
    }
}
