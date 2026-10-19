<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use PHPUnit\Framework\Attributes\TestWith;
use Tests\TestCase;

class RecordCardNumberTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_registration_form_includes_record_card_number(): void
    {
        $response = $this->get(route('register'));

        $response->assertOk();
        $response->assertSee('Record Card Number');
        $response->assertSee('name="record_card_number"', false);
    }

    #[TestWith([null, 'The record card number field is required.'])]
    #[TestWith([1234, 'The record card number field must be a string.'])]
    #[TestWith(['123456789012345678901234567890123456789012345678901', 'The record card number field must not be greater than 50 characters.'])]
    public function test_registration_requires_a_valid_record_card_number(mixed $number, string $message): void
    {
        $attributes = User::factory()->make()->only(['name', 'email', 'department']);

        $response = $this->post(route('register.store'), [...$attributes,
            'record_card_number' => $number,
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertSessionHasErrors(['record_card_number' => $message]);
        $this->assertDatabaseMissing('users', ['email' => $attributes['email']]);
        $this->assertGuest();
    }

    public function test_registration_cannot_reuse_an_existing_record_card_number(): void
    {
        $existingUser = User::factory()->create(['record_card_number' => '00001234']);
        $attributes = User::factory()->make()->only(['name', 'email', 'department']);

        $response = $this->post(route('register.store'), [...$attributes,
            'record_card_number' => '00001234',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertSessionHasErrors(['record_card_number' => 'The record card number has already been taken.']);
        $this->assertDatabaseMissing('users', ['email' => $attributes['email']]);
        $this->assertSame('00001234', $existingUser->fresh()->record_card_number);
    }

    #[TestWith([null, 'The record card number field is required.'])]
    #[TestWith(['RC-TAKEN', 'The record card number has already been taken.'])]
    public function test_administrator_created_accounts_require_a_unique_record_card_number(?string $number, string $message): void
    {
        $superAdmin = User::factory()->superAdmin()->create(['record_card_number' => 'RC-TAKEN']);
        $attributes = User::factory()->make()->only(['name', 'email', 'department']);

        $response = $this->actingAs($superAdmin)->post(route('admin.users.store'), [...$attributes,
            'record_card_number' => $number,
            'role' => 'user',
            'is_active' => true,
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertSessionHasErrors(['record_card_number' => $message]);
        $this->assertDatabaseMissing('users', ['email' => $attributes['email']]);
    }

    public function test_superadmin_can_enter_a_record_card_number_for_an_existing_user(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();
        $user = User::factory()->create(['record_card_number' => null]);

        $response = $this->actingAs($superAdmin)->put(route('admin.users.update', $user), [
            'record_card_number' => '00005678',
            'role' => 'user',
            'is_active' => true,
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect();
        $this->assertSame('00005678', $user->fresh()->record_card_number);
    }

    public function test_user_can_keep_their_existing_record_card_number_during_an_update(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();
        $user = User::factory()->create();

        $response = $this->actingAs($superAdmin)->put(route('admin.users.update', $user), [
            'record_card_number' => $user->record_card_number,
            'role' => 'admin',
            'is_active' => true,
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect();
        $this->assertSame('admin', $user->fresh()->role);
        $this->assertDatabaseHas('users', ['id' => $user->id, 'record_card_number' => $user->record_card_number]);
    }

    #[TestWith([null, 'The record card number field is required.'])]
    #[TestWith(['RC-TAKEN', 'The record card number has already been taken.'])]
    public function test_user_updates_cannot_remove_or_duplicate_record_card_numbers(?string $number, string $message): void
    {
        $superAdmin = User::factory()->superAdmin()->create(['record_card_number' => 'RC-TAKEN']);
        $user = User::factory()->create();

        $response = $this->actingAs($superAdmin)->put(route('admin.users.update', $user), [
            'record_card_number' => $number,
            'role' => 'admin',
            'is_active' => true,
        ]);

        $response->assertSessionHasErrors(['record_card_number' => $message]);
        $this->assertDatabaseHas('users', ['id' => $user->id, 'record_card_number' => $user->record_card_number, 'role' => 'user']);
    }

    public function test_existing_accounts_without_a_record_card_number_can_still_log_in(): void
    {
        $user = User::factory()->create(['record_card_number' => null]);

        $response = $this->post(route('login.store'), [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($user);
    }
}
