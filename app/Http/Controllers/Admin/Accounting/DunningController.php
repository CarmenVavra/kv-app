<?php

namespace App\Http\Controllers\Admin\Accounting;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SendPaymentReminderRequest;
use App\Mail\PaymentReminderMail;
use App\Models\Invoice;
use App\Services\DunningService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Mail;

class DunningController extends Controller
{
    public function send(SendPaymentReminderRequest $request, Invoice $invoice, DunningService $dunningService): RedirectResponse
    {
        if (! $invoice->client->email) {
            return back()->withErrors(['email' => 'Für diesen Klienten ist keine E-Mail-Adresse hinterlegt.']);
        }

        $level = $dunningService->nextLevel($invoice);
        $chargesFees = $level->chargesInterestAndFee();

        $reminder = $invoice->paymentReminders()->create([
            'level' => $level,
            'subject' => $request->validated('subject'),
            'body' => $request->validated('body'),
            'interest_amount' => $chargesFees ? $dunningService->interestAmount($invoice) : 0,
            'collection_fee' => $chargesFees ? $dunningService->collectionFee() : 0,
            'sent_at' => now(),
        ]);

        Mail::to($invoice->client->email)->send(new PaymentReminderMail($reminder));

        return redirect()->route('admin.accounting.index', ['tab' => 'mahnwesen']);
    }
}
