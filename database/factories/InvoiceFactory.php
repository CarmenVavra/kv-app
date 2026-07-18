<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\Invoice;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Invoice>
 */
class InvoiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $issueDate = fake()->dateTimeBetween('-2 months', 'now');

        return [
            'number' => 'R-'.str_pad((string) fake()->unique()->numberBetween(1, 9999), 4, '0', STR_PAD_LEFT),
            'client_id' => Client::factory(),
            'issue_date' => $issueDate,
            'due_date' => (clone $issueDate)->modify('+14 days'),
            'paid_at' => null,
        ];
    }

    public function paid(): static
    {
        return $this->state(fn (array $attributes) => [
            'paid_at' => fake()->dateTimeBetween($attributes['issue_date'], 'now'),
        ]);
    }

    public function overdue(): static
    {
        return $this->state(fn () => [
            'issue_date' => now()->subMonth(),
            'due_date' => now()->subWeek(),
            'paid_at' => null,
        ]);
    }
}
