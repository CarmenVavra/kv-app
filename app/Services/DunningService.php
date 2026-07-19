<?php

namespace App\Services;

use App\Enums\DunningLevel;
use App\Models\Invoice;
use Carbon\Carbon;

class DunningService
{
    public function nextLevel(Invoice $invoice): DunningLevel
    {
        $last = $invoice->paymentReminders()->orderByDesc('sent_at')->first();

        return $last?->level->next() ?? DunningLevel::Erinnerung;
    }

    public function daysOverdue(Invoice $invoice): int
    {
        return (int) $invoice->due_date->diffInDays(now());
    }

    public function interestAmount(Invoice $invoice): float
    {
        $annualRate = (float) config('accounting.basiszinssatz') + (float) config('accounting.verzugszinssatz_aufschlag');

        return round((float) $invoice->total_amount * $annualRate * ($this->daysOverdue($invoice) / 365), 2);
    }

    public function collectionFee(): float
    {
        return (float) config('accounting.betreibungsschaden');
    }

    /**
     * Stellt Betreff/Text/Zinsen fuer die naechste faellige Mahnstufe zusammen, editierbar
     * bevor der tatsaechliche Versand erfolgt.
     *
     * @return array{level: DunningLevel, daysOverdue: int, subject: string, body: string, interest: float, fee: float}
     */
    public function draft(Invoice $invoice): array
    {
        $level = $this->nextLevel($invoice);
        $daysOverdue = $this->daysOverdue($invoice);
        $chargesFees = $level->chargesInterestAndFee();
        $interest = $chargesFees ? $this->interestAmount($invoice) : 0.0;
        $fee = $chargesFees ? $this->collectionFee() : 0.0;

        $lastReminder = $invoice->paymentReminders()->orderByDesc('sent_at')->first();

        return [
            'level' => $level,
            'daysOverdue' => $daysOverdue,
            'subject' => $this->subjectFor($invoice, $level),
            'body' => $this->bodyFor($invoice, $level, $daysOverdue, $lastReminder?->sent_at),
            'interest' => $interest,
            'fee' => $fee,
        ];
    }

    private function subjectFor(Invoice $invoice, DunningLevel $level): string
    {
        return match ($level) {
            DunningLevel::Erinnerung => "Freundliche Zahlungserinnerung zu Rechnung {$invoice->number}",
            DunningLevel::Mahnung1 => "1. Mahnung zu Rechnung {$invoice->number}",
            DunningLevel::Mahnung2 => "2. Mahnung zu Rechnung {$invoice->number} — Zahlungsfrist überschritten",
        };
    }

    private function bodyFor(Invoice $invoice, DunningLevel $level, int $daysOverdue, ?Carbon $lastReminderSentAt): string
    {
        $name = $invoice->client->owner_name;
        $amount = number_format((float) $invoice->total_amount, 2, ',', '.').' €';

        return match ($level) {
            DunningLevel::Erinnerung => <<<TEXT
                Guten Tag {$name},

                bei der Durchsicht unserer offenen Posten ist mir aufgefallen, dass die Rechnung {$invoice->number} über {$amount} noch offen ist. Vermutlich handelt es sich um ein Versehen — ich bitte um Ausgleich in den nächsten Tagen.

                Falls die Zahlung bereits erfolgt ist, betrachten Sie diese Nachricht als gegenstandslos.

                Liebe Grüße
                Carmen Brandt
                TEXT,
            DunningLevel::Mahnung1 => <<<TEXT
                Guten Tag {$name},

                leider ist die Rechnung {$invoice->number} über {$amount} weiterhin offen ({$daysOverdue} Tage überfällig).

                Ich bitte um Zahlung innerhalb von 7 Tagen.

                Liebe Grüße
                Carmen Brandt
                TEXT,
            DunningLevel::Mahnung2 => <<<TEXT
                Guten Tag {$name},

                trotz Zahlungserinnerung vom {$lastReminderSentAt?->format('d.m.')} ist die Rechnung {$invoice->number} über {$amount} weiterhin offen ({$daysOverdue} Tage überfällig).

                Ich bitte um Zahlung innerhalb von 7 Tagen. Andernfalls sind wir berechtigt, gesetzliche Verzugszinsen (9,2 % über Basiszinssatz) sowie einen pauschalen Betreibungsschaden von 40 € zu verrechnen.

                Liebe Grüße
                Carmen Brandt
                TEXT,
        };
    }
}
