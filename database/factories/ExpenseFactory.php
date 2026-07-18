<?php

namespace Database\Factories;

use App\Enums\ExpenseCategory;
use App\Enums\ExpenseStatus;
use App\Models\Expense;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Expense>
 */
class ExpenseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'date' => fake()->dateTimeBetween('-2 months', 'now'),
            'supplier' => fake()->company(),
            'category' => fake()->randomElement(ExpenseCategory::cases()),
            'amount' => fake()->randomFloat(2, 10, 250),
            'status' => fake()->randomElement(ExpenseStatus::cases()),
        ];
    }
}
