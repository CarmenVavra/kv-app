<?php

use App\Enums\AppointmentStatus;
use App\Models\Appointment;
use App\Models\Client;
use App\Models\Invoice;
use Illuminate\Auth\Middleware\Authenticate;

it('shows past appointments and the next upcoming appointment on the client profile', function () {
    $client = Client::factory()->create();
    $past = Appointment::factory()->for($client)->create([
        'starts_at' => now()->subWeek(),
        'notes' => 'Erste Sitzung war ruhig.',
    ]);
    $next = Appointment::factory()->for($client)->create(['starts_at' => now()->addWeek()]);

    $this->withoutMiddleware(Authenticate::class)
        ->get(route('admin.clients.show', $client))
        ->assertOk()
        ->assertSee('Erste Sitzung war ruhig.')
        ->assertSee($next->type->label());
});

it('shows per-appointment billing state in the training history', function () {
    $client = Client::factory()->create();

    // Nicht verrechneter Termin
    Appointment::factory()->for($client)->create(['starts_at' => now()->subWeek(), 'status' => AppointmentStatus::Abgeschlossen]);

    // Verrechnet & bezahlt
    $paidInvoice = Invoice::factory()->for($client)->paid()->create(['number' => 'R-0091']);
    $paid = Appointment::factory()->for($client)->create(['starts_at' => now()->subWeeks(2), 'status' => AppointmentStatus::Abgeschlossen]);
    $paidInvoice->lineItems()->create(['appointment_id' => $paid->id, 'description' => 'x', 'quantity' => 1, 'unit_price' => 60]);

    // Verrechnet & offen
    $openInvoice = Invoice::factory()->for($client)->create(['number' => 'R-0092', 'due_date' => today()->addDays(7), 'paid_at' => null]);
    $open = Appointment::factory()->for($client)->create(['starts_at' => now()->subWeeks(3), 'status' => AppointmentStatus::Abgeschlossen]);
    $openInvoice->lineItems()->create(['appointment_id' => $open->id, 'description' => 'y', 'quantity' => 1, 'unit_price' => 60]);

    $this->withoutMiddleware(Authenticate::class)
        ->get(route('admin.clients.show', $client))
        ->assertOk()
        ->assertSee('Nicht verrechnet')
        ->assertSee('R-0091 · Bezahlt')
        ->assertSee('R-0092 · Offen');
});
