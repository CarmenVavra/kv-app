<?php

use App\Enums\DunningLevel;
use App\Enums\RecurringFrequency;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\MileageEntry;
use App\Models\PaymentReminder;
use App\Models\RecurringInvoiceTemplate;
use App\Services\InvoicePdfGenerator;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

// Nur die Authenticate-Middleware wird ausgeschaltet (nicht die ganze "web"-Gruppe) - sonst
// fehlen Session/CSRF/$errors, die die Views brauchen.

it('shows overdue invoices on the Mahnwesen tab', function () {
    $client = Client::factory()->create(['owner_name' => 'Frank Ostermann']);
    Invoice::factory()->for($client)->overdue()->create(['number' => 'R-0038']);

    $this->withoutMiddleware(Authenticate::class)
        ->get(route('admin.accounting.index', ['tab' => 'mahnwesen']))
        ->assertOk()
        ->assertSee('R-0038')
        ->assertSee('Frank Ostermann')
        ->assertSee('Zahlungserinnerung senden');
});

it('shows the current Mahnstufe per invoice on the Mahnwesen tab', function () {
    $client = Client::factory()->create();
    Invoice::factory()->for($client)->create(['number' => 'R-0050', 'due_date' => now()->subDays(7), 'paid_at' => null]);
    Invoice::factory()->for($client)->create(['number' => 'R-0051', 'due_date' => now()->subDays(12), 'paid_at' => null]);
    Invoice::factory()->for($client)->create(['number' => 'R-0052', 'due_date' => now()->subDays(22), 'paid_at' => null]);

    $this->withoutMiddleware(Authenticate::class)
        ->get(route('admin.accounting.index', ['tab' => 'mahnwesen']))
        ->assertOk()
        ->assertSee('Mahnstufe')
        ->assertSee('Erinnerung')
        ->assertSee('1. Mahnung')
        ->assertSee('2. Mahnung');
});

it('sends a payment reminder using exactly the edited subject and body, not the template', function () {
    Mail::fake();

    $client = Client::factory()->create(['email' => 'frank@beispiel.at']);
    $invoice = Invoice::factory()->for($client)->overdue()->create();
    $invoice->lineItems()->create(['description' => 'Folgetermin', 'quantity' => 1, 'unit_price' => 60]);

    $this->withoutMiddleware(Authenticate::class)
        ->post(route('admin.accounting.dunning.send', $invoice), [
            'subject' => 'Individuell angepasster Betreff',
            'body' => 'Individuell angepasster Text.',
        ])
        ->assertRedirect(route('admin.accounting.index', ['tab' => 'mahnwesen']));

    $reminder = PaymentReminder::sole();
    expect($reminder->subject)->toBe('Individuell angepasster Betreff')
        ->and($reminder->body)->toBe('Individuell angepasster Text.')
        ->and($reminder->level)->toBe(DunningLevel::Erinnerung)
        ->and((float) $reminder->interest_amount)->toBe(0.0)
        ->and((float) $reminder->collection_fee)->toBe(0.0);

    Mail::assertQueued(App\Mail\PaymentReminderMail::class, fn ($mail) => $mail->reminder->is($reminder));
});

it('charges interest and a collection fee only once the 2nd Mahnung is sent', function () {
    Mail::fake();

    $client = Client::factory()->create(['email' => 'anna@beispiel.at']);
    $invoice = Invoice::factory()->for($client)->overdue()->create();
    $invoice->lineItems()->create(['description' => 'Erstberatung', 'quantity' => 1, 'unit_price' => 95]);
    $invoice->paymentReminders()->create(['level' => DunningLevel::Erinnerung, 'subject' => 's', 'body' => 'b', 'sent_at' => now()->subDays(10)]);
    $invoice->paymentReminders()->create(['level' => DunningLevel::Mahnung1, 'subject' => 's', 'body' => 'b', 'sent_at' => now()->subDays(3)]);

    $this->withoutMiddleware(Authenticate::class)
        ->post(route('admin.accounting.dunning.send', $invoice), [
            'subject' => '2. Mahnung',
            'body' => 'Bitte zahlen Sie.',
        ]);

    $reminder = PaymentReminder::where('level', DunningLevel::Mahnung2)->sole();
    expect((float) $reminder->collection_fee)->toBe(40.0)
        ->and((float) $reminder->interest_amount)->toBeGreaterThan(0.0);
});

it('rejects sending a reminder when the client has no email on file', function () {
    $client = Client::factory()->create(['email' => null]);
    $invoice = Invoice::factory()->for($client)->overdue()->create();

    $this->withoutMiddleware(Authenticate::class)
        ->post(route('admin.accounting.dunning.send', $invoice), [
            'subject' => 'Betreff',
            'body' => 'Text',
        ])
        ->assertSessionHasErrors(['email']);

    expect(PaymentReminder::count())->toBe(0);
});

it('creates a recurring invoice template', function () {
    $client = Client::factory()->create();

    $this->withoutMiddleware(Authenticate::class)
        ->post(route('admin.accounting.recurring.store'), [
            'title' => 'Trainingspaket Rocky',
            'client_id' => $client->id,
            'description' => 'Trainingspaket',
            'amount' => '280.00',
            'frequency' => RecurringFrequency::Monatlich->value,
            'next_run_date' => today()->addMonth()->toDateString(),
        ])
        ->assertRedirect(route('admin.accounting.index', ['tab' => 'wiederkehrend']));

    $template = RecurringInvoiceTemplate::sole();
    expect($template->title)->toBe('Trainingspaket Rocky')
        ->and($template->active)->toBeTrue();
});

it('toggles a recurring template between active and paused', function () {
    $template = RecurringInvoiceTemplate::factory()->create(['active' => true]);

    $this->withoutMiddleware(Authenticate::class)
        ->patch(route('admin.accounting.recurring.toggle-active', $template))
        ->assertRedirect(route('admin.accounting.index', ['tab' => 'wiederkehrend']));

    expect($template->fresh()->active)->toBeFalse();
});

it('records a mileage entry as a linked, already-paid expense', function () {
    $this->withoutMiddleware(Authenticate::class)
        ->post(route('admin.accounting.mileage.store'), ['km' => '24'])
        ->assertRedirect(route('admin.accounting.index', ['tab' => 'fahrtkosten']));

    $entry = MileageEntry::sole();
    expect((float) $entry->km)->toBe(24.0)
        ->and((float) $entry->rate_per_km)->toBe(0.5)
        ->and((float) $entry->amount)->toBe(12.0)
        ->and($entry->expense)->not->toBeNull()
        ->and($entry->expense->status->value)->toBe('bezahlt');
});

it('generates an invoice pdf automatically when an invoice is created', function () {
    Storage::fake('local');
    $client = Client::factory()->create();

    $this->withoutMiddleware(Authenticate::class)
        ->post(route('admin.accounting.invoices.store'), [
            'client_id' => $client->id,
            'sonstiges' => '1',
            'sonstiges_description' => 'Erstberatung',
            'sonstiges_amount' => '80.00',
        ]);

    $invoice = Invoice::sole();
    expect($invoice->pdf_path)->not->toBeNull();
    Storage::disk('local')->assertExists($invoice->pdf_path);
});

it('regenerates a missing pdf on demand when downloading', function () {
    Storage::fake('local');
    $invoice = Invoice::factory()->create(['pdf_path' => null]);
    $invoice->lineItems()->create(['description' => 'Erstberatung', 'quantity' => 1, 'unit_price' => 80]);

    $this->withoutMiddleware(Authenticate::class)
        ->get(route('admin.accounting.invoices.pdf', $invoice))
        ->assertOk();

    expect($invoice->fresh()->pdf_path)->not->toBeNull();
});

it('does not regenerate the pdf file if it already exists on disk', function () {
    Storage::fake('local');
    $invoice = Invoice::factory()->create();
    $invoice->lineItems()->create(['description' => 'Erstberatung', 'quantity' => 1, 'unit_price' => 80]);
    $path = (new InvoicePdfGenerator)->generate($invoice);
    $originalModifiedAt = Storage::disk('local')->lastModified($path);

    $this->withoutMiddleware(Authenticate::class)
        ->get(route('admin.accounting.invoices.pdf', $invoice->fresh()))
        ->assertOk();

    expect(Storage::disk('local')->lastModified($path))->toBe($originalModifiedAt);
});

it('shows the fahrtkosten and euer and jahr tabs', function () {
    $this->withoutMiddleware(Authenticate::class)
        ->get(route('admin.accounting.index', ['tab' => 'fahrtkosten']))
        ->assertOk()
        ->assertSee('Fahrtkosten-Rechner');

    $this->withoutMiddleware(Authenticate::class)
        ->get(route('admin.accounting.index', ['tab' => 'euer']))
        ->assertOk()
        ->assertSee('Einnahmen-Ausgaben-Rechnung');

    $this->withoutMiddleware(Authenticate::class)
        ->get(route('admin.accounting.index', ['tab' => 'jahr']))
        ->assertOk()
        ->assertSee('Q3');
});
