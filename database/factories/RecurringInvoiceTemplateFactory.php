<?php

namespace Database\Factories;

use App\Enums\RecurringFrequency;
use App\Models\Client;
use App\Models\RecurringInvoiceTemplate;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<RecurringInvoiceTemplate>
 */
class RecurringInvoiceTemplateFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => 'Trainingspaket ('.fake()->randomElement(['4er-Karte', '8er-Karte']).')',
            'client_id' => Client::factory(),
            'description' => 'Trainingspaket',
            'amount' => fake()->randomElement([120, 280, 320]),
            'frequency' => fake()->randomElement(RecurringFrequency::cases()),
            'next_run_date' => now()->addWeek(),
            'active' => true,
        ];
    }
}
