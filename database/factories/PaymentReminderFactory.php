<?php

namespace Database\Factories;

use App\Enums\DunningLevel;
use App\Models\Invoice;
use App\Models\PaymentReminder;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PaymentReminder>
 */
class PaymentReminderFactory extends Factory
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
            'level' => DunningLevel::Erinnerung,
            'subject' => 'Freundliche Zahlungserinnerung',
            'body' => 'Guten Tag, die Rechnung ist noch offen.',
            'sent_at' => now(),
        ];
    }
}
