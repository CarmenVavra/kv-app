<?php

namespace Database\Factories;

use App\Enums\BehaviorTopic;
use App\Enums\ClientStatus;
use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Client>
 */
class ClientFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'dog_name' => fake()->firstName(),
            'breed' => fake()->randomElement(['Mischling', 'Labrador', 'Border Collie', 'Beagle', 'Pudel', 'Schäferhund']),
            'age_text' => fake()->numberBetween(1, 12).' Jahre',
            'behavior_topic' => fake()->randomElement(BehaviorTopic::cases()),
            'dog_notes' => fake()->optional()->sentence(),
            'color' => fake()->randomElement(Client::AVATAR_PALETTE),
            'owner_name' => fake()->name(),
            'email' => fake()->safeEmail(),
            'phone' => fake()->phoneNumber(),
            'address_line' => fake()->streetAddress(),
            'postal_code' => fake()->postcode(),
            'city' => fake()->city(),
            'status' => ClientStatus::Active,
        ];
    }
}
