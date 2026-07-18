<?php

use App\Enums\DunningLevel;
use App\Models\Client;
use App\Models\Invoice;
use App\Services\DunningService;

it('starts at the free Zahlungserinnerung when no reminder has been sent yet', function () {
    $invoice = Invoice::factory()->overdue()->create();

    expect((new DunningService)->nextLevel($invoice))->toBe(DunningLevel::Erinnerung);
});

it('advances one dunning level per prior reminder, capped at the 2nd Mahnung', function () {
    $invoice = Invoice::factory()->overdue()->create();
    $service = new DunningService;

    $invoice->paymentReminders()->create(['level' => DunningLevel::Erinnerung, 'subject' => 's', 'body' => 'b', 'sent_at' => now()->subDays(10)]);
    expect($service->nextLevel($invoice))->toBe(DunningLevel::Mahnung1);

    $invoice->paymentReminders()->create(['level' => DunningLevel::Mahnung1, 'subject' => 's', 'body' => 'b', 'sent_at' => now()->subDays(3)]);
    expect($service->nextLevel($invoice))->toBe(DunningLevel::Mahnung2);

    $invoice->paymentReminders()->create(['level' => DunningLevel::Mahnung2, 'subject' => 's', 'body' => 'b', 'sent_at' => now()]);
    expect($service->nextLevel($invoice))->toBe(DunningLevel::Mahnung2);
});

it('only charges interest and a collection fee from the 2nd Mahnung onward', function () {
    $invoice = Invoice::factory()->overdue()->create();
    $invoice->lineItems()->create(['description' => 'Erstberatung', 'quantity' => 1, 'unit_price' => 100]);
    $service = new DunningService;

    $draft = $service->draft($invoice);
    expect($draft['level'])->toBe(DunningLevel::Erinnerung)
        ->and($draft['interest'])->toBe(0.0)
        ->and($draft['fee'])->toBe(0.0);

    $invoice->paymentReminders()->create(['level' => DunningLevel::Erinnerung, 'subject' => 's', 'body' => 'b', 'sent_at' => now()->subDays(10)]);
    $invoice->paymentReminders()->create(['level' => DunningLevel::Mahnung1, 'subject' => 's', 'body' => 'b', 'sent_at' => now()->subDays(3)]);

    $draft = $service->draft($invoice->fresh());
    expect($draft['level'])->toBe(DunningLevel::Mahnung2)
        ->and($draft['interest'])->toBeGreaterThan(0.0)
        ->and($draft['fee'])->toBe(40.0);
});

it('drafts a subject and body that mention the client, invoice number and amount', function () {
    $client = Client::factory()->create(['owner_name' => 'Maria Weber']);
    $invoice = Invoice::factory()->for($client)->overdue()->create(['number' => 'R-0038']);
    $invoice->lineItems()->create(['description' => 'Folgetermin', 'quantity' => 1, 'unit_price' => 60]);

    $draft = (new DunningService)->draft($invoice);

    expect($draft['subject'])->toContain('R-0038')
        ->and($draft['body'])->toContain('Maria Weber')
        ->and($draft['body'])->toContain('R-0038')
        ->and($draft['body'])->toContain('60,00 €');
});
