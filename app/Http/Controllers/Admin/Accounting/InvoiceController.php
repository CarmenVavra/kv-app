<?php

namespace App\Http\Controllers\Admin\Accounting;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreInvoiceRequest;
use App\Models\Invoice;
use App\Services\InvoiceNumberGenerator;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Carbon;

class InvoiceController extends Controller
{
    public function store(StoreInvoiceRequest $request, InvoiceNumberGenerator $numberGenerator): RedirectResponse
    {
        $issueDate = Carbon::today();

        $invoice = Invoice::create([
            'number' => $numberGenerator->next(),
            'client_id' => $request->validated('client_id'),
            'issue_date' => $issueDate,
            'due_date' => $issueDate->copy()->addDays(14),
        ]);

        $invoice->lineItems()->create([
            'description' => $request->validated('service'),
            'quantity' => 1,
            'unit_price' => $request->validated('amount'),
        ]);

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
