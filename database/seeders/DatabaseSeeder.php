<?php

namespace Database\Seeders;

use App\Models\Ticket;
use App\Models\TicketComment;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $admin = User::factory()->superAdmin()->create([
            'name' => 'Development Super Admin',
            'email' => 'admin@example.com',
        ]);

        $users = User::factory(8)->create();

        $users->each(function (User $user) use ($admin): void {
            Ticket::factory(3)->create([
                'user_id' => $user->id,
                'assigned_to' => fake()->boolean(60) ? $admin->id : null,
            ])->each(function (Ticket $ticket) use ($user, $admin): void {
                TicketComment::factory()->create([
                    'ticket_id' => $ticket->id,
                    'user_id' => $user->id,
                    'message' => 'I am seeing this issue and need help from IT.',
                ]);

                if ($ticket->assigned_to) {
                    TicketComment::factory()->create([
                        'ticket_id' => $ticket->id,
                        'user_id' => $admin->id,
                        'message' => 'Thanks, we are reviewing this ticket.',
                    ]);
                }
            });
        });
    }
}
