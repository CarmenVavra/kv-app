<?php

namespace Database\Factories;

use App\Models\Invoice;
use App\Models\InvoiceLineItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<InvoiceLineItem>
 */
class InvoiceLineItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'invoice_id' => Invoice::factory(),
            'description' => fake()->randomElement(['Erstberatung', 'Folgetermin', 'Hausbesuch', 'Video-Check-in']),
            'quantity' => 1,
            'unit_price' => fake()->randomElement([45, 60, 80, 95]),
        ];
    }
}
