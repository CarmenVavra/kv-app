<?php

use App\Enums\RecurringFrequency;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\RecurringInvoiceTemplate;
use Illuminate\Support\Facades\Storage;

it('generates an invoice for a due, active template and advances next_run_date', function () {
    Storage::fake('local');
    $client = Client::factory()->create();
    $template = RecurringInvoiceTemplate::factory()->for($client)->create([
        'title' => 'Trainingspaket Rocky',
        'description' => 'Trainingspaket (4er-Karte)',
        'amount' => 280,
        'frequency' => RecurringFrequency::Monatlich,
        'next_run_date' => today(),
    ]);

    $this->artisan('invoices:generate-recurring');

    $invoice = Invoice::sole();
    expect($invoice->client_id)->toBe($client->id)
        ->and((float) $invoice->total_amount)->toBe(280.0)
        ->and($template->fresh()->next_run_date->toDateString())->toBe(today()->addMonth()->toDateString());
});

it('advances weekly templates by one week', function () {
    Storage::fake('local');
    $template = RecurringInvoiceTemplate::factory()->create([
        'frequency' => RecurringFrequency::Woechentlich,
        'next_run_date' => today(),
    ]);

    $this->artisan('invoices:generate-recurring');

    expect($template->fresh()->next_run_date->toDateString())->toBe(today()->addWeek()->toDateString());
});

it('does not generate invoices for paused templates', function () {
    RecurringInvoiceTemplate::factory()->create(['active' => false, 'next_run_date' => today()]);

    $this->artisan('invoices:generate-recurring');

    expect(Invoice::count())->toBe(0);
});

it('does not generate invoices before the next_run_date', function () {
    RecurringInvoiceTemplate::factory()->create(['active' => true, 'next_run_date' => today()->addWeek()]);

    $this->artisan('invoices:generate-recurring');

    expect(Invoice::count())->toBe(0);
});

it('is idempotent when run twice on the same day', function () {
    Storage::fake('local');
    RecurringInvoiceTemplate::factory()->create(['frequency' => RecurringFrequency::Monatlich, 'next_run_date' => today()]);

    $this->artisan('invoices:generate-recurring');
    $this->artisan('invoices:generate-recurring');

    expect(Invoice::count())->toBe(1);
});
