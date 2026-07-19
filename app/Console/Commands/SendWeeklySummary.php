<?php

namespace App\Console\Commands;

use App\Enums\InvoiceStatus;
use App\Mail\WeeklySummaryMail;
use App\Models\Appointment;
use App\Models\Inquiry;
use App\Models\Invoice;
use App\Models\Setting;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendWeeklySummary extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reports:weekly-summary';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verschickt die woechentliche Zusammenfassung, falls in den Einstellungen aktiviert';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $setting = Setting::current();

        if (! $setting->notify_weekly_summary || ! $setting->email) {
            $this->info('Woechentliche Zusammenfassung ist deaktiviert oder es fehlt eine E-Mail-Adresse.');

            return;
        }

        $openInvoices = Invoice::all()->filter(fn (Invoice $invoice) => $invoice->status !== InvoiceStatus::Bezahlt);

        $stats = [
            'appointmentsThisWeek' => Appointment::query()->whereBetween('starts_at', [now(), now()->addWeek()])->count(),
            'newInquiriesLastWeek' => Inquiry::query()->where('created_at', '>=', now()->subWeek())->count(),
            'openInvoicesCount' => $openInvoices->count(),
            'openInvoicesSum' => (float) $openInvoices->sum(fn (Invoice $invoice) => $invoice->total_amount),
        ];

        Mail::to($setting->email)->send(new WeeklySummaryMail($stats));

        $this->info('Woechentliche Zusammenfassung verschickt.');
    }
}
