<?php

namespace Database\Factories;

use App\Models\Ticket;
use App\Models\TicketAttachment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TicketAttachment>
 */
class TicketAttachmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'ticket_id' => Ticket::factory(),
            'uploaded_by' => User::factory(),
            'original_filename' => 'screenshot.png',
            'stored_path' => 'tickets/demo/screenshot.png',
            'mime_type' => 'image/png',
            'file_size' => 1024,
        ];
    }
}
