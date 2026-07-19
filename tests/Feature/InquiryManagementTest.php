<?php

use App\Enums\BehaviorTopic;
use App\Enums\InquiryStatus;
use App\Models\Client;
use App\Models\Inquiry;
use Illuminate\Auth\Middleware\Authenticate;

// Nur die Authenticate-Middleware wird ausgeschaltet (nicht die ganze "web"-Gruppe) - sonst
// fehlen Session/CSRF/$errors, die die Views brauchen.

it('shows the new-inquiries tile and section on the dashboard', function () {
    Inquiry::factory()->create(['name' => 'Maria Weber', 'status' => InquiryStatus::Neu]);
    Inquiry::factory()->create(['status' => InquiryStatus::Archiviert]);

    $this->withoutMiddleware(Authenticate::class)
        ->get(route('admin.dashboard'))
        ->assertOk()
        ->assertSee('Neue Anfragen')
        ->assertSee('Maria Weber')
        ->assertViewHas('newInquiriesCount', 1);
});

it('lists inquiries and filters by status', function () {
    Inquiry::factory()->create(['name' => 'Maria Weber', 'status' => InquiryStatus::Neu]);
    Inquiry::factory()->create(['name' => 'Sophie Krause', 'status' => InquiryStatus::Archiviert]);

    $this->withoutMiddleware(Authenticate::class)
        ->get(route('admin.inquiries.index', ['status' => InquiryStatus::Neu->value]))
        ->assertOk()
        ->assertSee('Maria Weber')
        ->assertDontSee('Sophie Krause');
});

it('marks an inquiry as contacted', function () {
    $inquiry = Inquiry::factory()->create(['status' => InquiryStatus::Neu]);

    $this->withoutMiddleware(Authenticate::class)
        ->patch(route('admin.inquiries.mark-contacted', $inquiry))
        ->assertRedirect();

    expect($inquiry->fresh()->status)->toBe(InquiryStatus::Kontaktiert);
});

it('archives an inquiry', function () {
    $inquiry = Inquiry::factory()->create(['status' => InquiryStatus::Neu]);

    $this->withoutMiddleware(Authenticate::class)
        ->patch(route('admin.inquiries.archive', $inquiry))
        ->assertRedirect();

    expect($inquiry->fresh()->status)->toBe(InquiryStatus::Archiviert);
});

it('converts an inquiry into a client and marks it converted', function () {
    $inquiry = Inquiry::factory()->create([
        'name' => 'Maria Weber',
        'email' => 'maria@beispiel.at',
        'dog_name' => 'Rocky',
        'status' => InquiryStatus::Neu,
    ]);

    $response = $this->withoutMiddleware(Authenticate::class)
        ->post(route('admin.inquiries.convert', $inquiry), [
            'dog_name' => 'Rocky',
            'owner_name' => 'Maria Weber',
            'email' => 'maria@beispiel.at',
            'behavior_topic' => BehaviorTopic::Leinenaggression->value,
        ]);

    $client = Client::sole();
    $response->assertRedirect(route('admin.clients.show', $client));
    expect($client->dog_name)->toBe('Rocky')
        ->and($client->owner_name)->toBe('Maria Weber')
        ->and($inquiry->fresh()->status)->toBe(InquiryStatus::Konvertiert);
});

it('rejects converting an inquiry without the required client fields', function () {
    $inquiry = Inquiry::factory()->create(['status' => InquiryStatus::Neu]);

    $this->withoutMiddleware(Authenticate::class)
        ->post(route('admin.inquiries.convert', $inquiry), [])
        ->assertSessionHasErrors(['dog_name', 'owner_name', 'behavior_topic']);

    expect(Client::count())->toBe(0)
        ->and($inquiry->fresh()->status)->toBe(InquiryStatus::Neu);
});
