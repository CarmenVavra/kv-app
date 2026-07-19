<?php

use App\Enums\InquiryStatus;
use App\Mail\NewInquiryNotification;
use App\Models\Inquiry;
use App\Models\Setting;
use Illuminate\Support\Facades\Mail;

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

it('notifies the admin by mail when notify_new_inquiries is enabled', function () {
    Mail::fake();
    Setting::current()->update(['notify_new_inquiries' => true, 'email' => 'carmen@gregor.at']);

    $this->post(route('contact.store'), [
        'name' => 'Maria Weber',
        'email' => 'maria@beispiel.at',
        'dog_name' => 'Rocky',
        'message' => 'Rocky reagiert stark auf andere Hunde an der Leine.',
    ]);

    Mail::assertQueued(NewInquiryNotification::class, fn ($mail) => $mail->hasTo('carmen@gregor.at'));
});

it('does not notify the admin when notify_new_inquiries is disabled', function () {
    Mail::fake();
    Setting::current()->update(['notify_new_inquiries' => false, 'email' => 'carmen@gregor.at']);

    $this->post(route('contact.store'), [
        'name' => 'Maria Weber',
        'email' => 'maria@beispiel.at',
        'dog_name' => 'Rocky',
        'message' => 'Rocky reagiert stark auf andere Hunde an der Leine.',
    ]);

    Mail::assertNotQueued(NewInquiryNotification::class);
});
