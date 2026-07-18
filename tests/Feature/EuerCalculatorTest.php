<?php

use App\Models\Expense;
use App\Models\Invoice;
use App\Services\EuerCalculator;
use Carbon\CarbonImmutable;

it('sums paid invoices and dated expenses within a period, cash-basis', function () {
    $periodStart = CarbonImmutable::create(2026, 7, 1)->startOfDay();
    $periodEnd = CarbonImmutable::create(2026, 7, 31)->endOfDay();

    $paidInPeriod = Invoice::factory()->create(['issue_date' => '2026-07-01', 'due_date' => '2026-07-15', 'paid_at' => '2026-07-10']);
    $paidInPeriod->lineItems()->create(['description' => 'Erstberatung', 'quantity' => 1, 'unit_price' => 100]);

    $paidOutsidePeriod = Invoice::factory()->create(['issue_date' => '2026-06-01', 'due_date' => '2026-06-15', 'paid_at' => '2026-06-10']);
    $paidOutsidePeriod->lineItems()->create(['description' => 'Folgetermin', 'quantity' => 1, 'unit_price' => 60]);

    $unpaidInPeriod = Invoice::factory()->create(['issue_date' => '2026-07-05', 'due_date' => '2026-07-19', 'paid_at' => null]);
    $unpaidInPeriod->lineItems()->create(['description' => 'Hausbesuch', 'quantity' => 1, 'unit_price' => 90]);

    Expense::factory()->create(['date' => '2026-07-14', 'amount' => 30]);
    Expense::factory()->create(['date' => '2026-06-28', 'amount' => 999]);

    $summary = (new EuerCalculator)->summarize($periodStart, $periodEnd);

    expect($summary['income'])->toBe(100.0)
        ->and($summary['expenses'])->toBe(30.0)
        ->and($summary['profit'])->toBe(70.0)
        ->and($summary['taxReserve'])->toBe(14.0);
});

it('breaks the year into quarters and marks the current one as still in progress', function () {
    $quarters = (new EuerCalculator)->quartersForYear((int) today()->year);

    $currentQuarterNumber = (int) ceil(today()->month / 3);
    expect($quarters)->toHaveCount($currentQuarterNumber)
        ->and(end($quarters)['label'])->toContain('(bisher)');
});
