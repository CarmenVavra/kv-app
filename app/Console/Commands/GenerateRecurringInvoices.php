<?php

namespace App\Console\Commands;

use App\Models\Invoice;
use App\Models\RecurringInvoiceTemplate;
use App\Services\InvoiceNumberGenerator;
use App\Services\InvoicePdfGenerator;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class GenerateRecurringInvoices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invoices:generate-recurring';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Erzeugt faellige Rechnungen aus aktiven wiederkehrenden Vorlagen und rueckt next_run_date vor';

    public function handle(InvoiceNumberGenerator $numberGenerator, InvoicePdfGenerator $pdfGenerator): void
    {
        $templates = RecurringInvoiceTemplate::query()
            ->where('active', true)
            ->where('next_run_date', '<=', today())
            ->get();

        foreach ($templates as $template) {
            $issueDate = Carbon::today();

            $invoice = Invoice::create([
                'number' => $numberGenerator->next(),
                'client_id' => $template->client_id,
                'issue_date' => $issueDate,
                'due_date' => $issueDate->copy()->addDays(7),
            ]);

            $invoice->lineItems()->create([
                'description' => $template->description,
                'quantity' => 1,
                'unit_price' => $template->amount,
            ]);

            $pdfGenerator->generate($invoice);

            // Muss vor dem naechsten Schleifendurchlauf passieren, nicht erst am Ende: sichert
            // Idempotenz bei doppeltem Lauf am selben Tag, da next_run_date dann nicht mehr <= today() ist.
            $template->advanceNextRunDate();
        }

        $this->info("{$templates->count()} wiederkehrende Rechnung(en) erzeugt.");
    }
}
