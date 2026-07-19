<?php

use App\Enums\InquiryStatus;
use App\Models\Inquiry;

it('creates an inquiry from the contact form', function () {
    $this->post(route('contact.store'), [
        'name' => 'Maria Weber',
        'email' => 'maria@beispiel.at',
        'dog_name' => 'Rocky',
        'message' => 'Rocky reagiert stark auf andere Hunde an der Leine.',
    ])->assertRedirect(route('landing').'#kontakt');

    $inquiry = Inquiry::sole();
    expect($inquiry->name)->toBe('Maria Weber')
        ->and($inquiry->email)->toBe('maria@beispiel.at')
        ->and($inquiry->dog_name)->toBe('Rocky')
        ->and($inquiry->status)->toBe(InquiryStatus::Neu)
        ->and($inquiry->interested_package)->toBeNull();
});

it('stores the interested package when arriving from the pricing page', function () {
    $this->post(route('contact.store'), [
        'name' => 'Frank Ostermann',
        'email' => 'frank@beispiel.at',
        'dog_name' => 'Bella',
        'message' => 'Ich interessiere mich für: Trainingspaket 4er',
        'interested_package' => 'Trainingspaket 4er',
    ])->assertRedirect(route('landing').'#kontakt');

    expect(Inquiry::sole()->interested_package)->toBe('Trainingspaket 4er');
});

it('rejects an incomplete contact form and redirects back to the kontakt anchor', function () {
    $this->post(route('contact.store'), [])
        ->assertRedirect(route('landing').'#kontakt')
        ->assertSessionHasErrors(['name', 'email', 'dog_name', 'message']);

    expect(Inquiry::count())->toBe(0);
});
