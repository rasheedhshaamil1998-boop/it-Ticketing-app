<?php

namespace Tests\Feature;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminWorkflowTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_user_cannot_access_admin_dashboard(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('admin.dashboard'));

        $response->assertForbidden();
    }

    public function test_admin_can_update_ticket_status_priority_and_assignment(): void
    {
        $admin = User::factory()->admin()->create();
        $ticket = Ticket::factory()->open()->create();

        $response = $this->actingAs($admin)->put(route('admin.tickets.update', $ticket), [
            'status' => 'In Progress',
            'priority' => 'Critical',
            'assigned_to' => $admin->id,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('tickets', [
            'id' => $ticket->id,
            'status' => 'In Progress',
            'priority' => 'Critical',
            'assigned_to' => $admin->id,
        ]);
    }

    public function test_admin_dashboard_does_not_show_roles_and_permissions_matrix(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertOk();
        $response->assertSee('Tickets by Category');
        $response->assertDontSee('Dynamic role permissions and live account counts');
        $response->assertDontSee('Tickets Permissions');
        $response->assertDontSee('Create users, admins, and super admins');
    }

    public function test_admin_can_view_roles_and_permissions_page(): void
    {
        $admin = User::factory()->admin()->create();
        User::factory()->count(2)->create();

        $response = $this->actingAs($admin)->get(route('admin.roles-permissions'));

        $response->assertOk();
        $response->assertSee('Roles and Permissions');
        $response->assertDontSee('Access Groups');
        $response->assertDontSee('Access Control');
        $response->assertSee('Dynamic role permissions and live account counts');
        $response->assertSee('Tickets Permissions');
        $response->assertSee('1 account');
        $response->assertSee('2 accounts');
        $response->assertSee('Create new support tickets');
        $response->assertSee('View and review all tickets');
        $response->assertSee('Create users, admins, and super admins');
    }

    public function test_admin_can_view_paginated_ticket_index(): void
    {
        $admin = User::factory()->admin()->create();
        Ticket::factory()->count(13)->create(['subject' => 'Printer toner low']);

        $response = $this->actingAs($admin)->get(route('admin.tickets.index', ['page' => 1]));

        $response->assertOk();
        $response->assertSee('Review and manage support tickets');
        $response->assertSee('Printer toner low');
        $response->assertSee('pagination');
    }

    public function test_admin_must_provide_resolution_notes_to_resolve_ticket(): void
    {
        $admin = User::factory()->admin()->create();
        $ticket = Ticket::factory()->open()->create();

        $response = $this->actingAs($admin)->post(route('admin.tickets.resolve', $ticket), [
            'resolution_notes' => '',
        ]);

        $response->assertSessionHasErrors('resolution_notes');
    }

    public function test_admin_can_resolve_ticket(): void
    {
        $admin = User::factory()->admin()->create();
        $ticket = Ticket::factory()->open()->create();

        $response = $this->actingAs($admin)->post(route('admin.tickets.resolve', $ticket), [
            'resolution_notes' => 'Reinstalled the network driver.',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('tickets', [
            'id' => $ticket->id,
            'status' => 'Resolved',
            'resolution_notes' => 'Reinstalled the network driver.',
            'resolved_by' => $admin->id,
        ]);
    }

    public function test_admin_cannot_manage_user_access(): void
    {
        $admin = User::factory()->admin()->create();
        $user = User::factory()->create();

        $indexResponse = $this->actingAs($admin)->get(route('admin.users.index'));
        $createResponse = $this->actingAs($admin)->get(route('admin.users.create'));
        $storeResponse = $this->actingAs($admin)->post(route('admin.users.store'), [
            'name' => 'Blocked User',
            'email' => 'blocked@example.com',
            'department' => 'Operations',
            'role' => 'user',
            'is_active' => '1',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);
        $updateResponse = $this->actingAs($admin)->put(route('admin.users.update', $user), [
            'role' => 'admin',
            'is_active' => '1',
        ]);

        $indexResponse->assertForbidden();
        $createResponse->assertForbidden();
        $storeResponse->assertForbidden();
        $updateResponse->assertForbidden();
        $this->assertDatabaseMissing('users', ['email' => 'blocked@example.com']);
    }

    public function test_superadmin_cannot_remove_last_active_superadmin_access(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();

        $response = $this->actingAs($superAdmin)->put(route('admin.users.update', $superAdmin), [
            'record_card_number' => $superAdmin->record_card_number,
            'role' => 'admin',
            'is_active' => '1',
        ]);

        $response->assertSessionHasErrors('role');
        $this->assertTrue($superAdmin->fresh()->isSuperAdmin());
    }

    public function test_superadmin_can_view_create_user_form(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();

        $response = $this->actingAs($superAdmin)->get(route('admin.users.create'));

        $response->assertOk();
        $response->assertSee('Create User');
        $response->assertSee('Email Address');
    }

    public function test_superadmin_can_create_user(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();

        $response = $this->actingAs($superAdmin)->post(route('admin.users.store'), [
            'name' => 'New Helpdesk User',
            'email' => 'new-user@example.com',
            'record_card_number' => 'RC-NEW-001',
            'department' => 'Operations',
            'role' => 'user',
            'is_active' => '1',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $user = User::where('email', 'new-user@example.com')->firstOrFail();

        $response->assertRedirect(route('admin.users.show', $user));
        $this->assertSame('New Helpdesk User', $user->name);
        $this->assertSame('RC-NEW-001', $user->record_card_number);
        $this->assertSame('Operations', $user->department);
        $this->assertSame('user', $user->role);
        $this->assertTrue($user->is_active);
        $this->assertTrue(Hash::check('password', $user->password));
    }

    public function test_superadmin_can_create_admin_and_superadmin_users(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();

        $adminResponse = $this->actingAs($superAdmin)->post(route('admin.users.store'), [
            'name' => 'Ticket Admin',
            'email' => 'ticket-admin@example.com',
            'record_card_number' => 'RC-ADMIN-001',
            'department' => 'IT',
            'role' => 'admin',
            'is_active' => '1',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);
        $superAdminResponse = $this->actingAs($superAdmin)->post(route('admin.users.store'), [
            'name' => 'Access Manager',
            'email' => 'access-manager@example.com',
            'record_card_number' => 'RC-SUPER-001',
            'department' => 'IT',
            'role' => 'superadmin',
            'is_active' => '1',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $adminResponse->assertRedirect();
        $superAdminResponse->assertRedirect();
        $this->assertDatabaseHas('users', ['email' => 'ticket-admin@example.com', 'role' => 'admin']);
        $this->assertDatabaseHas('users', ['email' => 'access-manager@example.com', 'role' => 'superadmin']);
    }

    public function test_superadmin_create_user_requires_valid_data(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();

        $response = $this->actingAs($superAdmin)->post(route('admin.users.store'), [
            'name' => '',
            'email' => 'not-an-email',
            'department' => '',
            'role' => 'owner',
            'is_active' => '1',
            'password' => 'password',
            'password_confirmation' => 'different',
        ]);

        $response->assertSessionHasErrors(['name', 'email', 'department', 'record_card_number', 'role', 'password']);
        $this->assertDatabaseMissing('users', ['email' => 'not-an-email']);
    }

    public function test_admin_can_export_report_csv(): void
    {
        $admin = User::factory()->admin()->create();
        Ticket::factory()->create(['subject' => 'Printer toner low']);

        $response = $this->actingAs($admin)->get(route('admin.reports.export'));

        $response->assertOk();
        $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
        $this->assertStringContainsString('Ticket Number', $response->streamedContent());
        $this->assertStringContainsString('Printer toner low', $response->streamedContent());
    }
}
