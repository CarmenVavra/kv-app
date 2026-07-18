<?php

namespace App\Mail;

use App\Models\PaymentReminder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PaymentReminderMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public PaymentReminder $reminder)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->reminder->subject,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.payment-reminder',
        );
    }
}
