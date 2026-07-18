<?php

use App\Models\Appointment;
use App\Models\Client;
use Illuminate\Auth\Middleware\Authenticate;

it('shows the month grid and the selected day agenda', function () {
    $client = Client::factory()->create(['dog_name' => 'Rocky']);
    $appointment = Appointment::factory()->for($client)->create(['starts_at' => today()->setTime(9, 0)]);

    $this->withoutMiddleware(Authenticate::class)
        ->get(route('admin.calendar'))
        ->assertOk()
        ->assertSee('Rocky')
        ->assertSee('09:00');
});

it('navigates to a different day via the tag query parameter', function () {
    $client = Client::factory()->create(['dog_name' => 'Luna']);
    Appointment::factory()->for($client)->create(['starts_at' => today()->addDays(3)->setTime(14, 0)]);

    $this->withoutMiddleware(Authenticate::class)
        ->get(route('admin.calendar', ['tag' => today()->addDays(3)->toDateString()]))
        ->assertOk()
        ->assertSee('Luna')
        ->assertSee('14:00');
});

it('creates an appointment', function () {
    $client = Client::factory()->create();

    $this->withoutMiddleware(Authenticate::class)
        ->post(route('admin.appointments.store'), [
            'client_id' => $client->id,
            'type' => 'erstberatung',
            'starts_at' => today()->addDay()->setTime(10, 0)->format('Y-m-d\TH:i'),
            'duration_minutes' => 60,
        ])
        ->assertRedirect();

    $appointment = Appointment::sole();
    expect($appointment->client_id)->toBe($client->id)
        ->and($appointment->status->value)->toBe('bestaetigt');
});

it('rejects an appointment for a client that does not exist', function () {
    $this->withoutMiddleware(Authenticate::class)
        ->post(route('admin.appointments.store'), [
            'client_id' => 999,
            'type' => 'erstberatung',
            'starts_at' => today()->addDay()->format('Y-m-d\TH:i'),
            'duration_minutes' => 60,
        ])
        ->assertSessionHasErrors(['client_id']);
});
