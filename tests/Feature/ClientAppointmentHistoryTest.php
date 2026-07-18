<?php

use App\Models\Appointment;
use App\Models\Client;
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
