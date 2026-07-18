<?php

namespace App\Http\Controllers\Admin\Accounting;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Services\InvoicePdfGenerator;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class InvoicePdfController extends Controller
{
    public function download(Invoice $invoice, InvoicePdfGenerator $generator): StreamedResponse
    {
        $path = $generator->ensureExists($invoice);

        return Storage::disk('local')->download($path, "Rechnung-{$invoice->number}.pdf");
    }
}
