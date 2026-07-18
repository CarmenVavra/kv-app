<?php

namespace App\Services;

use App\Models\Expense;
use App\Models\Invoice;
use Carbon\CarbonImmutable;

class EuerCalculator
{
    /**
     * Einnahmen-Ausgaben-Rechnung auf Ist-Basis: Einnahmen zaehlen, wenn eine Rechnung tatsaechlich
     * bezahlt wurde (paid_at), nicht wenn sie ausgestellt wurde.
     *
     * @return array{income: float, expenses: float, profit: float, taxReserve: float}
     */
    public function summarize(CarbonImmutable $periodStart, CarbonImmutable $periodEnd): array
    {
        $income = Invoice::query()
            ->whereBetween('paid_at', [$periodStart, $periodEnd])
            ->with('lineItems')
            ->get()
            ->sum(fn (Invoice $invoice) => (float) $invoice->total_amount);

        $expenses = (float) Expense::query()
            ->whereBetween('date', [$periodStart, $periodEnd])
            ->sum('amount');

        $profit = $income - $expenses;

        return [
            'income' => $income,
            'expenses' => $expenses,
            'profit' => $profit,
            'taxReserve' => round($profit * (float) config('accounting.steuerruecklage_quote'), 2),
        ];
    }

    /**
     * @return array<int, array{label: string, income: float, expenses: float, profit: float}>
     */
    public function quartersForYear(int $year): array
    {
        $quarters = [];

        for ($quarter = 1; $quarter <= 4; $quarter++) {
            $start = CarbonImmutable::create($year, ($quarter - 1) * 3 + 1, 1)->startOfDay();
            $end = $start->endOfQuarter();

            if ($start->isFuture()) {
                break;
            }

            $summary = $this->summarize($start, $end);

            $quarters[] = [
                'label' => "Q{$quarter} {$year}".($end->isFuture() ? ' (bisher)' : ''),
                'income' => $summary['income'],
                'expenses' => $summary['expenses'],
                'profit' => $summary['profit'],
            ];
        }

        return $quarters;
    }
}
