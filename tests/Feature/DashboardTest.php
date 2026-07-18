<?php

use App\Models\Appointment;
use App\Models\Client;
use Illuminate\Auth\Middleware\Authenticate;

it('shows todays appointments and the active client count', function () {
    $client = Client::factory()->create(['dog_name' => 'Rocky']);
    Appointment::factory()->for($client)->create(['starts_at' => today()->setTime(11, 0)]);
    Appointment::factory()->for(Client::factory())->create(['starts_at' => now()->addWeek()]);

    $this->withoutMiddleware(Authenticate::class)
        ->get(route('admin.dashboard'))
        ->assertOk()
        ->assertSee('Rocky')
        ->assertSee('11:00')
        ->assertSeeInOrder(['Termine heute', '1']);
});
