<?php

namespace App\Services;

use App\Models\Invoice;
use Illuminate\Support\Str;

class InvoiceNumberGenerator
{
    public function next(): string
    {
        $highest = Invoice::query()
            ->get(['number'])
            ->map(fn (Invoice $invoice) => (int) Str::after($invoice->number, 'R-'))
            ->max() ?? 0;

        return sprintf('R-%04d', $highest + 1);
    }
}
