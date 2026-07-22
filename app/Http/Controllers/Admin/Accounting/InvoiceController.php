<?php

namespace App\Http\Controllers\Admin\Accounting;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreInvoiceRequest;
use App\Models\Appointment;
use App\Models\Invoice;
use App\Services\InvoiceNumberGenerator;
use App\Services\InvoicePdfGenerator;
use App\Services\MileageCalculator;
use App\Services\ParkingCalculator;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Carbon;

class InvoiceController extends Controller
{
    public function store(
        StoreInvoiceRequest $request,
        InvoiceNumberGenerator $numberGenerator,
        InvoicePdfGenerator $pdfGenerator,
        MileageCalculator $mileageCalculator,
        ParkingCalculator $parkingCalculator,
    ): RedirectResponse {
        $issueDate = Carbon::today();

        $invoice = Invoice::create([
            'number' => $numberGenerator->next(),
            'client_id' => $request->validated('client_id'),
            'issue_date' => $issueDate,
            'due_date' => $issueDate->copy()->addDays(7),
        ]);

        // Nur Termine des gewaehlten Klienten, die tatsaechlich noch offen (abgeschlossen, aber
        // unverrechnet) sind - schuetzt vor einer manipulierten appointments[]-Liste im Request.
        $appointments = Appointment::query()
            ->whereIn('id', $request->input('appointments', []))
            ->where('client_id', $request->validated('client_id'))
            ->unbilled()
            ->get();

        foreach ($appointments as $appointment) {
            $invoice->lineItems()->create([
                'appointment_id' => $appointment->id,
                'description' => $appointment->billingLabel().' ('.$appointment->starts_at->format('d.m.Y').')',
                'quantity' => 1,
                'unit_price' => $request->input(
                    "appointment_amounts.{$appointment->id}",
                    $appointment->type->defaultInvoiceAmount()
                ),
            ]);
        }

        if ($request->boolean('sonstiges')) {
            $invoice->lineItems()->create([
                'description' => $request->validated('sonstiges_description'),
                'quantity' => 1,
                'unit_price' => $request->validated('sonstiges_amount'),
            ]);
        }

        if ($request->boolean('charge_mileage')) {
            $km = (float) $request->validated('mileage_km');

            $invoice->lineItems()->create([
                'description' => 'Fahrtkosten ('.number_format($km, 1, ',', '.').' km)',
                'quantity' => 1,
                'unit_price' => $mileageCalculator->calculate($km),
            ]);
        }

        if ($request->boolean('charge_parking')) {
            $hours = (float) $request->validated('parking_hours');

            $invoice->lineItems()->create([
                'description' => 'Kurzparkschein ('.number_format($hours, 2, ',', '.').' Std.)',
                'quantity' => 1,
                'unit_price' => $parkingCalculator->calculate($hours),
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
        $invoice->load(['client', 'lineItems.appointment']);

        return view('admin.accounting.invoices.print', ['invoice' => $invoice]);
    }
}
