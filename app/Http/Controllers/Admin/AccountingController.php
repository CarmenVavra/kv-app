<?php

namespace App\Http\Controllers\Admin;

use App\Enums\InvoiceStatus;
use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Expense;
use App\Models\Invoice;
use App\Models\RecurringInvoiceTemplate;
use App\Services\DunningService;
use App\Services\EuerCalculator;
use App\Services\MileageCalculator;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AccountingController extends Controller
{
    private const TABS = ['out', 'in', 'mahnwesen', 'wiederkehrend', 'fahrtkosten', 'euer', 'jahr'];

    public function index(
        Request $request,
        DunningService $dunningService,
        EuerCalculator $euerCalculator,
        MileageCalculator $mileageCalculator,
    ): View {
        $tab = in_array($request->query('tab'), self::TABS, true) ? $request->query('tab') : 'out';

        $invoices = Invoice::query()->with(['client', 'lineItems'])->orderByDesc('issue_date')->get();
        $expenses = Expense::query()->orderByDesc('date')->get();

        return view('admin.accounting.index', [
            'tab' => $tab,
            'invoices' => $invoices,
            'expenses' => $expenses,
            'clients' => Client::query()->orderBy('dog_name')->get(),
            'stats' => $this->stats($invoices, $expenses),
            ...$this->tabData($tab, $invoices, $dunningService, $euerCalculator, $mileageCalculator),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function tabData(
        string $tab,
        Collection $invoices,
        DunningService $dunningService,
        EuerCalculator $euerCalculator,
        MileageCalculator $mileageCalculator,
    ): array {
        return match ($tab) {
            'mahnwesen' => [
                'overdue' => $invoices
                    ->filter(fn (Invoice $invoice) => $invoice->status === InvoiceStatus::Ueberfaellig)
                    ->map(fn (Invoice $invoice) => ['invoice' => $invoice, 'draft' => $dunningService->draft($invoice)]),
            ],
            'wiederkehrend' => [
                'templates' => RecurringInvoiceTemplate::query()->with('client')->orderBy('title')->get(),
            ],
            'fahrtkosten' => [
                'ratePerKm' => $mileageCalculator->ratePerKm(),
            ],
            'euer' => [
                'euer' => $euerCalculator->summarize(today()->startOfMonth()->toImmutable(), today()->endOfMonth()->toImmutable()),
            ],
            'jahr' => (function () use ($euerCalculator) {
                $quarters = $euerCalculator->quartersForYear((int) today()->year);

                return ['quarters' => $quarters, 'yearlyProfit' => collect($quarters)->sum('profit')];
            })(),
            default => [],
        };
    }

    public function export(Request $request): StreamedResponse
    {
        $tab = $request->query('tab') === 'in' ? 'in' : 'out';

        if ($tab === 'in') {
            return $this->exportExpenses();
        }

        return $this->exportInvoices();
    }

    /**
     * @return array<string, mixed>
     */
    private function stats(Collection $invoices, Collection $expenses): array
    {
        $income = $invoices
            ->filter(fn (Invoice $invoice) => $invoice->paid_at?->isCurrentMonth())
            ->sum(fn (Invoice $invoice) => $invoice->total_amount);

        $expensesThisMonth = $expenses->filter(fn (Expense $expense) => $expense->date->isCurrentMonth());
        $spending = $expensesThisMonth->sum(fn (Expense $expense) => (float) $expense->amount);

        $openInvoices = $invoices->filter(fn (Invoice $invoice) => $invoice->status !== InvoiceStatus::Bezahlt);

        return [
            'income' => $income,
            'expenses' => $spending,
            'profit' => $income - $spending,
            'openCount' => $openInvoices->count(),
            'openSum' => $openInvoices->sum(fn (Invoice $invoice) => $invoice->total_amount),
        ];
    }

    private function exportInvoices(): StreamedResponse
    {
        $rows = Invoice::query()->with(['client', 'lineItems'])->orderByDesc('issue_date')->get();

        $callback = function () use ($rows): void {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Datum', 'Rechnungsnummer', 'Klient', 'Leistung', 'Betrag', 'Status']);

            foreach ($rows as $invoice) {
                fputcsv($handle, [
                    $invoice->issue_date->format('d.m.Y'),
                    $invoice->number,
                    $invoice->client->dog_name.' ('.$invoice->client->owner_name.')',
                    $invoice->lineItems->pluck('description')->implode(', '),
                    number_format((float) $invoice->total_amount, 2, ',', '.').' €',
                    $invoice->status->label(),
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="ausgangsrechnungen.csv"',
        ]);
    }

    private function exportExpenses(): StreamedResponse
    {
        $rows = Expense::query()->orderByDesc('date')->get();

        $callback = function () use ($rows): void {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Datum', 'Lieferant', 'Kategorie', 'Betrag', 'Status']);

            foreach ($rows as $expense) {
                fputcsv($handle, [
                    $expense->date->format('d.m.Y'),
                    $expense->supplier,
                    $expense->category->label(),
                    number_format((float) $expense->amount, 2, ',', '.').' €',
                    $expense->status->label(),
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="eingangsrechnungen.csv"',
        ]);
    }
}
