<?php

namespace Database\Factories;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Ticket>
 */
class TicketFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'ticket_number' => sprintf('IT-%s-%06d', now()->year, fake()->unique()->numberBetween(1, 999999)),
            'user_id' => User::factory(),
            'subject' => fake()->sentence(5),
            'category' => fake()->randomElement(Ticket::CATEGORIES),
            'location' => fake()->randomElement(['Head Office', 'Remote', 'Warehouse', 'Branch Office']),
            'department' => fake()->randomElement(['IT', 'Finance', 'HR', 'Operations', 'Sales']),
            'priority' => fake()->randomElement(Ticket::PRIORITIES),
            'description' => fake()->paragraphs(2, true),
            'status' => fake()->randomElement(Ticket::STATUSES),
        ];
    }

    public function open(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'Open',
        ]);
    }

    public function resolved(?User $admin = null): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'Resolved',
            'resolved_by' => $admin?->id ?? User::factory()->admin(),
            'resolved_at' => now()->subDay(),
            'resolution_notes' => 'Issue resolved during diagnostics.',
        ]);
    }
}
