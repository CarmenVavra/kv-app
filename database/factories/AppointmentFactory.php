<?php

namespace Database\Factories;

use App\Enums\AppointmentStatus;
use App\Enums\AppointmentType;
use App\Models\Appointment;
use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Appointment>
 */
class AppointmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'client_id' => Client::factory(),
            'type' => fake()->randomElement(AppointmentType::cases()),
            'starts_at' => fake()->dateTimeBetween('-1 month', '+1 month'),
            'duration_minutes' => fake()->randomElement([30, 45, 60, 90]),
            'status' => fake()->randomElement(AppointmentStatus::cases()),
            'notes' => fake()->optional()->sentence(),
        ];
    }
}
