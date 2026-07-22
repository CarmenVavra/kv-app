<?php

namespace App\Services;

use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class InvoicePdfGenerator
{
    public function generate(Invoice $invoice): string
    {
        $invoice->loadMissing(['client', 'lineItems.appointment']);

        $pdf = Pdf::loadView('admin.accounting.invoices.pdf', ['invoice' => $invoice]);
        $path = "invoices/{$invoice->number}.pdf";

        Storage::disk('local')->put($path, $pdf->output());
        $invoice->update(['pdf_path' => $path]);

        return $path;
    }

    public function ensureExists(Invoice $invoice): string
    {
        if ($invoice->pdf_path && Storage::disk('local')->exists($invoice->pdf_path)) {
            return $invoice->pdf_path;
        }

        return $this->generate($invoice);
    }
}
