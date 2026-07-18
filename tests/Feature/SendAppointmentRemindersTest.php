<?php

use App\Mail\AppointmentReminderMail;
use App\Models\Appointment;
use App\Models\Client;
use Illuminate\Support\Facades\Mail;

it('sends a reminder for an appointment starting within the next hour', function () {
    Mail::fake();

    $client = Client::factory()->create(['email' => 'maria@beispiel.de']);
    $appointment = Appointment::factory()->for($client)->create(['starts_at' => now()->addMinutes(30)]);

    $this->artisan('appointments:send-reminders');

    Mail::assertQueued(AppointmentReminderMail::class, fn ($mail) => $mail->appointment->is($appointment));
    expect($appointment->fresh()->reminder_sent_at)->not->toBeNull();
});

it('does not send a reminder twice', function () {
    Mail::fake();

    $client = Client::factory()->create(['email' => 'maria@beispiel.de']);
    Appointment::factory()->for($client)->create([
        'starts_at' => now()->addMinutes(30),
        'reminder_sent_at' => now()->subMinutes(5),
    ]);

    $this->artisan('appointments:send-reminders');

    Mail::assertNotQueued(AppointmentReminderMail::class);
});

it('does not send a reminder for appointments outside the next hour', function () {
    Mail::fake();

    $client = Client::factory()->create(['email' => 'maria@beispiel.de']);
    Appointment::factory()->for($client)->create(['starts_at' => now()->addHours(3)]);

    $this->artisan('appointments:send-reminders');

    Mail::assertNotQueued(AppointmentReminderMail::class);
});

it('skips clients without an email address', function () {
    Mail::fake();

    $client = Client::factory()->create(['email' => null]);
    $appointment = Appointment::factory()->for($client)->create(['starts_at' => now()->addMinutes(30)]);

    $this->artisan('appointments:send-reminders');

    Mail::assertNotQueued(AppointmentReminderMail::class);
    expect($appointment->fresh()->reminder_sent_at)->toBeNull();
});
