<?php

namespace Tests\Feature;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_dashboard_displays_an_empty_queue_without_division_errors(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertOk();
        $response->assertSee('Your queue is clear');
        $response->assertSee('No category data yet.');
        $response->assertViewHas('activity', fn ($activity): bool => $activity->count() === 7 && $activity->sum('total') === 0);
    }

    public function test_dashboard_activity_includes_seven_days_and_fills_missing_days(): void
    {
        $this->travelTo(now()->setDate(2026, 10, 19)->setTime(12, 0));
        $admin = User::factory()->admin()->create();
        Ticket::factory()->for($admin)->count(2)->create(['created_at' => today()]);
        Ticket::factory()->for($admin)->create(['created_at' => today()->subDays(6)]);
        Ticket::factory()->for($admin)->create(['created_at' => today()->subDays(7)]);
        Ticket::factory()->for($admin)->create(['created_at' => today()->addDay()]);

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertOk();
        $response->assertViewHas('activity', fn ($activity): bool => $activity->pluck('total')->all() === [1, 0, 0, 0, 0, 0, 2]);
        $response->assertSee('Ticket activity');
        $response->assertSee(route('admin.tickets.index', ['priority' => 'Critical']));
    }

    public function test_dashboard_escapes_ticket_subjects_and_shows_the_assignee(): void
    {
        $admin = User::factory()->admin()->create();
        Ticket::factory()->for($admin)->create([
            'subject' => '<script>alert(1)</script>',
            'assigned_to' => $admin->id,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertOk();
        $response->assertSee('<script>alert(1)</script>');
        $response->assertDontSee('<script>alert(1)</script>', false);
        $response->assertSee($admin->name);
    }
}
