<?php

namespace Database\Factories;

use App\Models\Setting;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Setting>
 */
class SettingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => 'Carmen Brandt',
            'email' => fake()->safeEmail(),
            'phone' => fake()->phoneNumber(),
            'praxisname' => 'Mein Hund der Rowdy',
            'notify_new_inquiries' => true,
            'notify_appointment_reminders' => true,
            'notify_weekly_summary' => false,
            'default_appointment_duration' => 60,
            'working_days' => ['Fr', 'Sa', 'So'],
        ];
    }
}
