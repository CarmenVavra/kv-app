<?php

namespace App\Http\Controllers\Admin\Accounting;

use App\Enums\InvoiceService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreInvoiceRequest;
use App\Models\Invoice;
use App\Services\InvoiceNumberGenerator;
use App\Services\InvoicePdfGenerator;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Carbon;

class InvoiceController extends Controller
{
    public function store(
        StoreInvoiceRequest $request,
        InvoiceNumberGenerator $numberGenerator,
        InvoicePdfGenerator $pdfGenerator,
    ): RedirectResponse {
        $issueDate = Carbon::today();

        $invoice = Invoice::create([
            'number' => $numberGenerator->next(),
            'client_id' => $request->validated('client_id'),
            'issue_date' => $issueDate,
            'due_date' => $issueDate->copy()->addDays(14),
        ]);

        $invoice->lineItems()->create([
            'description' => InvoiceService::from($request->validated('service'))->label(),
            'quantity' => 1,
            'unit_price' => $request->validated('amount'),
        ]);

        if ($request->boolean('charge_mileage')) {
            $roundTripKm = (float) $request->validated('mileage_km') * 2;

            $invoice->lineItems()->create([
                'description' => 'Fahrtkosten ('.number_format($roundTripKm, 1, ',', '.').' km, Hin- und Rückfahrt)',
                'quantity' => 1,
                'unit_price' => round($roundTripKm * (float) config('accounting.kilometergeld_pro_km'), 2),
            ]);
        }

        if ($request->boolean('charge_parking')) {
            $invoice->lineItems()->create([
                'description' => 'Kurzparkscheine',
                'quantity' => 1,
                'unit_price' => $request->validated('parking_amount'),
            ]);
        }

        $pdfGenerator->generate($invoice);

        return redirect()->route('admin.accounting.index', ['tab' => 'out']);
    }

    public function markPaid(Invoice $invoice): RedirectResponse
    {
        $invoice->markPaid();

        return redirect()->route('admin.accounting.index', ['tab' => 'out']);
    }

    public function print(Invoice $invoice): View
    {
        $invoice->load(['client', 'lineItems']);

        return view('admin.accounting.invoices.print', ['invoice' => $invoice]);
    }
}
