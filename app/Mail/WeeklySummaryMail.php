<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WeeklySummaryMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * @param array{appointmentsThisWeek: int, newInquiriesLastWeek: int, openInvoicesCount: int, openInvoicesSum: float} $stats
     */
    public function __construct(public array $stats)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Wöchentliche Zusammenfassung — '.now()->translatedFormat('d. F Y'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.weekly-summary',
        );
    }
}
