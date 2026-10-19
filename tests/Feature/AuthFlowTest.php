<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class AuthFlowTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_user_can_register_and_is_not_admin(): void
    {
        $response = $this->post(route('register.store'), [
            'name' => 'Aisha Khan',
            'email' => 'aisha@example.com',
            'department' => 'Finance',
            'record_card_number' => '00001234',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'email' => 'aisha@example.com',
            'department' => 'Finance',
            'record_card_number' => '00001234',
            'role' => 'user',
            'is_active' => true,
        ]);
    }

    public function test_user_can_login(): void
    {
        $user = User::factory()->create(['password' => 'password']);

        $response = $this->post(route('login.store'), [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_inactive_user_cannot_login(): void
    {
        $user = User::factory()->inactive()->create(['password' => 'password']);

        $response = $this->post(route('login.store'), [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }
}
