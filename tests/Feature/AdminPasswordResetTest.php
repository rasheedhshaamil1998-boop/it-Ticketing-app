<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\TestWith;
use Tests\TestCase;

class AdminPasswordResetTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_superadmin_can_view_password_reset_form(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();
        $user = User::factory()->create();

        $response = $this->actingAs($superAdmin)->get(route('admin.users.show', $user));

        $response->assertOk();
        $response->assertSee('Reset Password');
        $response->assertSee(route('admin.users.password.update', $user));
    }

    public function test_superadmin_can_reset_password_without_changing_account_access(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();
        $user = User::factory()->create();
        $oldRememberToken = $user->remember_token;

        $response = $this->actingAs($superAdmin)->put(route('admin.users.password.update', $user), [
            'password' => 'NewSecurePassword123!',
            'password_confirmation' => 'NewSecurePassword123!',
            'role' => 'superadmin',
            'is_active' => false,
        ]);

        $response->assertRedirect(route('admin.users.show', $user));
        $response->assertSessionHas('status', 'Password reset successfully. Share the new password with the user securely.');
        $user->refresh();
        $this->assertTrue(Hash::check('NewSecurePassword123!', $user->password));
        $this->assertFalse(Hash::check('password', $user->password));
        $this->assertNotSame($oldRememberToken, $user->remember_token);
        $this->assertSame('user', $user->role);
        $this->assertTrue($user->is_active);
        $this->assertSame($superAdmin->id, auth()->id());
    }

    #[TestWith(['user'])]
    #[TestWith(['admin'])]
    public function test_unprivileged_roles_cannot_reset_passwords(string $role): void
    {
        $actor = User::factory()->create(['role' => $role]);
        $user = User::factory()->create();
        $oldPasswordHash = $user->password;

        $response = $this->actingAs($actor)->put(route('admin.users.password.update', $user), [
            'password' => 'NewSecurePassword123!',
            'password_confirmation' => 'NewSecurePassword123!',
        ]);

        $response->assertForbidden();
        $this->assertSame($oldPasswordHash, $user->fresh()->password);
    }

    public function test_guests_cannot_reset_passwords(): void
    {
        $user = User::factory()->create();
        $oldPasswordHash = $user->password;

        $response = $this->put(route('admin.users.password.update', $user), [
            'password' => 'NewSecurePassword123!',
            'password_confirmation' => 'NewSecurePassword123!',
        ]);

        $response->assertRedirect(route('login'));
        $this->assertSame($oldPasswordHash, $user->fresh()->password);
    }

    #[TestWith(['', '', 'The password field is required.'])]
    #[TestWith(['short', 'short', 'The password field must be at least 8 characters.'])]
    #[TestWith(['NewSecurePassword123!', 'DifferentPassword123!', 'The password field confirmation does not match.'])]
    public function test_invalid_passwords_do_not_change_the_existing_password(string $password, string $confirmation, string $message): void
    {
        $superAdmin = User::factory()->superAdmin()->create();
        $user = User::factory()->create();
        $oldPasswordHash = $user->password;
        $oldRememberToken = $user->remember_token;

        $response = $this->actingAs($superAdmin)
            ->from(route('admin.users.show', $user))
            ->put(route('admin.users.password.update', $user), [
                'password' => $password,
                'password_confirmation' => $confirmation,
            ]);

        $response->assertRedirect(route('admin.users.show', $user));
        $response->assertSessionHasErrors(['password' => $message]);
        $this->assertSame($oldPasswordHash, $user->fresh()->password);
        $this->assertSame($oldRememberToken, $user->remember_token);
    }
}
