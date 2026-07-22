<?php

use App\Models\Appointment;
use App\Models\Client;
use App\Models\Setting;
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
            'behavior_topic' => 'stress',
            'status_option' => 'ausstehend',
            'starts_at' => today()->addDay()->setTime(10, 0)->format('Y-m-d\TH:i'),
            'duration_minutes' => 60,
        ])
        ->assertRedirect();

    $appointment = Appointment::sole();
    expect($appointment->client_id)->toBe($client->id)
        ->and($appointment->status->value)->toBe('ausstehend')
        ->and($appointment->behavior_topic->value)->toBe('stress');
});

it('creates an appointment as abgeschlossen directly, so it is immediately billable', function () {
    $client = Client::factory()->create();

    $this->withoutMiddleware(Authenticate::class)
        ->post(route('admin.appointments.store'), [
            'client_id' => $client->id,
            'type' => 'folgetermin',
            'behavior_topic' => 'stress',
            'status_option' => 'abgeschlossen',
            'starts_at' => today()->subDay()->setTime(10, 0)->format('Y-m-d\TH:i'),
            'duration_minutes' => 60,
        ])
        ->assertRedirect();

    $appointment = Appointment::sole();
    expect($appointment->status->value)->toBe('abgeschlossen')
        ->and(Appointment::unbilled()->whereKey($appointment->id)->exists())->toBeTrue();
});

it('creates a cancelled-with-fee appointment whose Storno is billable', function () {
    $client = Client::factory()->create();

    $this->withoutMiddleware(Authenticate::class)
        ->post(route('admin.appointments.store'), [
            'client_id' => $client->id,
            'type' => 'folgetermin',
            'behavior_topic' => 'stress',
            'status_option' => 'abgesagt_mit_gebuehr',
            'starts_at' => today()->addDay()->setTime(10, 0)->format('Y-m-d\TH:i'),
            'duration_minutes' => 60,
        ])
        ->assertRedirect();

    $appointment = Appointment::sole();
    expect($appointment->status->value)->toBe('abgesagt')
        ->and($appointment->cancellation_fee)->toBeTrue()
        ->and(Appointment::unbilled()->whereKey($appointment->id)->exists())->toBeTrue();
});

it('requires a Leistung when creating an appointment', function () {
    $client = Client::factory()->create();

    $this->withoutMiddleware(Authenticate::class)
        ->post(route('admin.appointments.store'), [
            'client_id' => $client->id,
            'type' => 'erstberatung',
            'starts_at' => today()->addDay()->setTime(10, 0)->format('Y-m-d\TH:i'),
            'duration_minutes' => 60,
        ])
        ->assertSessionHasErrors(['behavior_topic']);

    expect(Appointment::count())->toBe(0);
});

it('preselects the default appointment duration from the settings', function () {
    Setting::current()->update(['default_appointment_duration' => 90]);

    $this->withoutMiddleware(Authenticate::class)
        ->get(route('admin.calendar'))
        ->assertOk()
        ->assertSee('<option value="90" selected>90 Minuten</option>', false);
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

it('updates an appointment, including its status', function () {
    $client = Client::factory()->create();
    $appointment = Appointment::factory()->for($client)->create([
        'starts_at' => today()->addDay()->setTime(10, 0),
        'status' => 'bestaetigt',
    ]);

    $this->withoutMiddleware(Authenticate::class)
        ->put(route('admin.appointments.update', $appointment), [
            'client_id' => $client->id,
            'type' => 'folgetermin',
            'behavior_topic' => 'impulskontrolle',
            'status_option' => 'abgeschlossen',
            'starts_at' => today()->addDay()->setTime(11, 0)->format('Y-m-d\TH:i'),
            'duration_minutes' => 45,
            'notes' => 'Verlief gut.',
        ])
        ->assertRedirect();

    $appointment->refresh();
    expect($appointment->type->value)->toBe('folgetermin')
        ->and($appointment->behavior_topic->value)->toBe('impulskontrolle')
        ->and($appointment->status->value)->toBe('abgeschlossen')
        ->and($appointment->duration_minutes)->toBe(45)
        ->and($appointment->notes)->toBe('Verlief gut.');
});

it('sets an appointment status inline from the training history', function () {
    $appointment = Appointment::factory()->create(['status' => 'bestaetigt']);

    $this->withoutMiddleware(Authenticate::class)
        ->patch(route('admin.appointments.update-status', $appointment), ['status_option' => 'abgeschlossen'])
        ->assertRedirect(route('admin.clients.show', $appointment->client));

    expect($appointment->fresh()->status->value)->toBe('abgeschlossen');
});

it('maps the two Abgesagt options to a cancellation fee flag', function () {
    $withFee = Appointment::factory()->create(['status' => 'bestaetigt']);
    $withoutFee = Appointment::factory()->create(['status' => 'bestaetigt']);

    $this->withoutMiddleware(Authenticate::class)
        ->patch(route('admin.appointments.update-status', $withFee), ['status_option' => 'abgesagt_mit_gebuehr']);
    $this->withoutMiddleware(Authenticate::class)
        ->patch(route('admin.appointments.update-status', $withoutFee), ['status_option' => 'abgesagt_ohne_gebuehr']);

    expect($withFee->fresh()->status->value)->toBe('abgesagt')
        ->and($withFee->fresh()->cancellation_fee)->toBeTrue()
        ->and($withoutFee->fresh()->status->value)->toBe('abgesagt')
        ->and($withoutFee->fresh()->cancellation_fee)->toBeFalse();
});

it('clears the cancellation fee flag when moving away from Abgesagt', function () {
    $appointment = Appointment::factory()->create(['status' => 'abgesagt', 'cancellation_fee' => true]);

    $this->withoutMiddleware(Authenticate::class)
        ->patch(route('admin.appointments.update-status', $appointment), ['status_option' => 'abgeschlossen']);

    expect($appointment->fresh()->cancellation_fee)->toBeNull();
});

it('cancels an appointment with a Stornogebühr via the full edit form', function () {
    $client = Client::factory()->create();
    $appointment = Appointment::factory()->for($client)->create(['status' => 'bestaetigt']);

    $this->withoutMiddleware(Authenticate::class)
        ->put(route('admin.appointments.update', $appointment), [
            'client_id' => $client->id,
            'type' => 'folgetermin',
            'behavior_topic' => 'stress',
            'status_option' => 'abgesagt_mit_gebuehr',
            'starts_at' => today()->addDay()->setTime(11, 0)->format('Y-m-d\TH:i'),
            'duration_minutes' => 60,
        ])
        ->assertRedirect();

    $appointment->refresh();
    expect($appointment->status->value)->toBe('abgesagt')
        ->and($appointment->cancellation_fee)->toBeTrue();
});

it('deletes an appointment', function () {
    $appointment = Appointment::factory()->create();

    $this->withoutMiddleware(Authenticate::class)
        ->delete(route('admin.appointments.destroy', $appointment))
        ->assertRedirect();

    expect(Appointment::find($appointment->id))->toBeNull();
});
