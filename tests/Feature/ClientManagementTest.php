<?php

use App\Enums\BehaviorTopic;
use App\Models\Client;

// Nur die Authenticate-Middleware wird ausgeschaltet (nicht die ganze "web"-Gruppe) - sonst
// fehlen Session/CSRF/$errors, die die Views brauchen. Der "Gast wird zu /login umgeleitet"-Fall
// ist bereits in AdminAccessTest abgedeckt; hier geht es nur um die CRUD-Logik selbst.

it('lists clients and filters by behavior topic', function () {
    Client::factory()->create(['dog_name' => 'Rocky', 'behavior_topic' => BehaviorTopic::Leinenaggression]);
    Client::factory()->create(['dog_name' => 'Luna', 'behavior_topic' => BehaviorTopic::Welpentraining]);

    $this->withoutMiddleware(\Illuminate\Auth\Middleware\Authenticate::class)
        ->get(route('admin.clients.index', ['thema' => BehaviorTopic::Leinenaggression->value]))
        ->assertOk()
        ->assertSee('Rocky')
        ->assertDontSee('Luna');
});

it('searches clients by dog or owner name', function () {
    Client::factory()->create(['dog_name' => 'Rocky', 'owner_name' => 'Maria Weber']);
    Client::factory()->create(['dog_name' => 'Luna', 'owner_name' => 'Sophie Krause']);

    $this->withoutMiddleware(\Illuminate\Auth\Middleware\Authenticate::class)
        ->get(route('admin.clients.index', ['suche' => 'Weber']))
        ->assertOk()
        ->assertSee('Rocky')
        ->assertDontSee('Luna');
});

it('creates a client with an auto-assigned avatar color', function () {
    $this->withoutMiddleware(\Illuminate\Auth\Middleware\Authenticate::class)
        ->post(route('admin.clients.store'), [
            'dog_name' => 'Rocky',
            'owner_name' => 'Maria Weber',
            'behavior_topic' => BehaviorTopic::Leinenaggression->value,
        ])
        ->assertRedirect(route('admin.clients.index'));

    $client = Client::sole();
    expect($client->dog_name)->toBe('Rocky')
        ->and($client->owner_name)->toBe('Maria Weber')
        ->and($client->behavior_topic)->toBe(BehaviorTopic::Leinenaggression)
        ->and($client->color)->toBeIn(Client::AVATAR_PALETTE);
});

it('rejects creating a client without the required fields', function () {
    $this->withoutMiddleware(\Illuminate\Auth\Middleware\Authenticate::class)
        ->post(route('admin.clients.store'), [])
        ->assertSessionHasErrors(['dog_name', 'owner_name', 'behavior_topic']);

    expect(Client::count())->toBe(0);
});

it('shows a client profile', function () {
    $client = Client::factory()->create(['dog_name' => 'Rocky']);

    $this->withoutMiddleware(\Illuminate\Auth\Middleware\Authenticate::class)
        ->get(route('admin.clients.show', $client))
        ->assertOk()
        ->assertSee('Rocky');
});

it('updates a client', function () {
    $client = Client::factory()->create(['dog_name' => 'Rocky']);

    $this->withoutMiddleware(\Illuminate\Auth\Middleware\Authenticate::class)
        ->put(route('admin.clients.update', $client), [
            'dog_name' => 'Rocky Jr.',
            'owner_name' => $client->owner_name,
            'behavior_topic' => $client->behavior_topic->value,
        ])
        ->assertRedirect(route('admin.clients.show', $client));

    expect($client->fresh()->dog_name)->toBe('Rocky Jr.');
});

it('updates only the notes field without touching other attributes', function () {
    $client = Client::factory()->create(['dog_name' => 'Rocky', 'dog_notes' => 'Alt']);

    $this->withoutMiddleware(\Illuminate\Auth\Middleware\Authenticate::class)
        ->patch(route('admin.clients.update-notes', $client), ['dog_notes' => 'Neue Notiz'])
        ->assertRedirect(route('admin.clients.show', $client));

    $client->refresh();
    expect($client->dog_notes)->toBe('Neue Notiz')
        ->and($client->dog_name)->toBe('Rocky');
});

it('soft-deletes a client', function () {
    $client = Client::factory()->create();

    $this->withoutMiddleware(\Illuminate\Auth\Middleware\Authenticate::class)
        ->delete(route('admin.clients.destroy', $client))
        ->assertRedirect(route('admin.clients.index'));

    expect(Client::find($client->id))->toBeNull()
        ->and(Client::withTrashed()->find($client->id))->not->toBeNull();
});
