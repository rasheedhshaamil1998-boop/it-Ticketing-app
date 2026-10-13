<?php

namespace Tests\Feature;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class TicketWorkflowTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_authenticated_user_can_create_ticket_with_attachment(): void
    {
        Storage::fake('local');
        $user = User::factory()->create(['department' => 'Operations']);

        $response = $this->actingAs($user)->post(route('tickets.store'), [
            'subject' => 'Laptop cannot connect to WiFi',
            'category' => 'Network Issue',
            'location' => 'Head Office',
            'department' => 'Operations',
            'priority' => 'High',
            'description' => 'The laptop disconnects every few minutes.',
            'attachment' => UploadedFile::fake()->create('wifi.pdf', 100, 'application/pdf'),
        ]);

        $ticket = Ticket::first();

        $response->assertRedirect(route('tickets.show', $ticket));
        $this->assertSame('IT-'.now()->year.'-000001', $ticket->ticket_number);
        $this->assertDatabaseHas('ticket_attachments', [
            'ticket_id' => $ticket->id,
            'uploaded_by' => $user->id,
            'original_filename' => 'wifi.pdf',
        ]);
    }

    public function test_user_only_sees_their_own_tickets(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $ownTicket = Ticket::factory()->create(['user_id' => $user->id, 'subject' => 'Own issue']);
        $otherTicket = Ticket::factory()->create(['user_id' => $otherUser->id, 'subject' => 'Other issue']);

        $response = $this->actingAs($user)->get(route('tickets.index'));

        $response->assertOk();
        $response->assertSee($ownTicket->ticket_number);
        $response->assertDontSee($otherTicket->ticket_number);
    }

    public function test_user_cannot_view_another_users_ticket(): void
    {
        $user = User::factory()->create();
        $ticket = Ticket::factory()->create();

        $response = $this->actingAs($user)->get(route('tickets.show', $ticket));

        $response->assertForbidden();
    }

    public function test_user_can_comment_on_own_ticket(): void
    {
        $user = User::factory()->create();
        $ticket = Ticket::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->post(route('tickets.comments.store', $ticket), [
            'message' => 'Any update on this request?',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('ticket_comments', [
            'ticket_id' => $ticket->id,
            'user_id' => $user->id,
            'message' => 'Any update on this request?',
        ]);
    }

    public function test_rejects_invalid_attachment_type(): void
    {
        Storage::fake('local');
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('tickets.store'), [
            'subject' => 'Bad attachment',
            'category' => 'Other',
            'location' => 'Remote',
            'department' => $user->department,
            'priority' => 'Low',
            'description' => 'Testing invalid upload.',
            'attachment' => UploadedFile::fake()->create('script.exe', 10, 'application/x-msdownload'),
        ]);

        $response->assertSessionHasErrors('attachment');
        $this->assertDatabaseCount('tickets', 0);
    }
}
