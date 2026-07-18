<?php

namespace Database\Factories;

use App\Models\MileageEntry;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MileageEntry>
 */
class MileageEntryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $km = fake()->randomFloat(1, 5, 60);

        return [
            'date' => fake()->dateTimeBetween('-1 month', 'now'),
            'km' => $km,
            'rate_per_km' => 0.50,
            'amount' => round($km * 0.50, 2),
        ];
    }
}
