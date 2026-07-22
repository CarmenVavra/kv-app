<?php

namespace App\Services;

use App\Enums\DunningLevel;
use App\Enums\InvoiceStatus;
use App\Models\Invoice;
use App\Models\PaymentReminder;
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

    /**
     * Aktuelle Mahnstufe einer Rechnung fuer die Listenanzeige: das Hoehere aus der nach
     * Verzugstagen empfohlenen Stufe (config('accounting.mahnstufe_ab_tag')) und der bereits
     * versendeten hoechsten Stufe - so untertreibt die Spalte nie gegenueber dem, was im
     * Mahnwesen tatsaechlich schon gesendet wurde. Null, wenn die Rechnung nicht ueberfaellig
     * ist (bezahlt oder noch innerhalb der Frist) oder erst so kurz ueberfaellig, dass noch
     * keine Stufe empfohlen wird.
     */
    public function currentStage(Invoice $invoice): ?DunningLevel
    {
        if ($invoice->status !== InvoiceStatus::Ueberfaellig) {
            return null;
        }

        $recommended = $this->recommendedStage($this->daysOverdue($invoice));

        $sent = $invoice->paymentReminders
            ->sortByDesc(fn (PaymentReminder $reminder) => $reminder->level->rank())
            ->first()?->level;

        $candidates = array_filter([$recommended, $sent]);

        if ($candidates === []) {
            return null;
        }

        return collect($candidates)->sortByDesc(fn (DunningLevel $level) => $level->rank())->first();
    }

    /**
     * Empfohlene Mahnstufe rein nach Verzugstagen (Schwellen aus config); null bei sehr kurzer
     * Ueberfaelligkeit, in der noch keine Erinnerung faellig ist.
     */
    private function recommendedStage(int $daysOverdue): ?DunningLevel
    {
        $thresholds = config('accounting.mahnstufe_ab_tag');

        return match (true) {
            $daysOverdue >= $thresholds['mahnung_2'] => DunningLevel::Mahnung2,
            $daysOverdue >= $thresholds['mahnung_1'] => DunningLevel::Mahnung1,
            $daysOverdue >= $thresholds['erinnerung'] => DunningLevel::Erinnerung,
            default => null,
        };
    }

    public function interestAmount(Invoice $invoice): float
    {
        $annualRate = (float) config('accounting.verzugszinssatz');

        return round((float) $invoice->total_amount * $annualRate * ($this->daysOverdue($invoice) / 365), 2);
    }

    /**
     * Stellt Betreff/Text/Zinsen fuer die naechste faellige Mahnstufe zusammen, editierbar
     * bevor der tatsaechliche Versand erfolgt. Gegenueber Verbrauchern (B2C) faellt kein
     * pauschaler Betreibungsschaden an, daher gibt es hier nur Verzugszinsen, keine Gebuehr.
     *
     * @return array{level: DunningLevel, daysOverdue: int, subject: string, body: string, interest: float}
     */
    public function draft(Invoice $invoice): array
    {
        $level = $this->nextLevel($invoice);
        $daysOverdue = $this->daysOverdue($invoice);
        $interest = $level->chargesInterest() ? $this->interestAmount($invoice) : 0.0;

        $lastReminder = $invoice->paymentReminders()->orderByDesc('sent_at')->first();

        return [
            'level' => $level,
            'daysOverdue' => $daysOverdue,
            'subject' => $this->subjectFor($invoice, $level),
            'body' => $this->bodyFor($invoice, $level, $daysOverdue, $lastReminder?->sent_at),
            'interest' => $interest,
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
        $amount = number_format((float) $invoice->total_amount, 2, ',', '.') . ' €';

        return match ($level) {
            DunningLevel::Erinnerung => <<<TEXT
                Guten Tag {$name},

                bei der Durchsicht unserer offenen Posten ist mir aufgefallen, dass die Rechnung {$invoice->number} über {$amount} noch offen ist. Vermutlich handelt es sich um ein Versehen — ich bitte um Ausgleich in den nächsten Tagen.

                Falls die Zahlung bereits erfolgt ist, betrachten Sie diese Nachricht als gegenstandslos.

                Liebe Grüße
                Carmen GREGOR
                TEXT,
            DunningLevel::Mahnung1 => <<<TEXT
                Guten Tag {$name},

                leider ist die Rechnung {$invoice->number} über {$amount} weiterhin offen ({$daysOverdue} Tage überfällig).

                Ich bitte um Zahlung innerhalb von 7 Tagen.

                Liebe Grüße
                Carmen GREGOR
                TEXT,
            DunningLevel::Mahnung2 => <<<TEXT
                Guten Tag {$name},

                trotz Zahlungserinnerung vom {$lastReminderSentAt?->format('d.m.')} ist die Rechnung {$invoice->number} über {$amount} weiterhin offen ({$daysOverdue} Tage überfällig).

                Ich bitte um Zahlung innerhalb von 7 Tagen. Andernfalls sind wir berechtigt, die gesetzlichen Verzugszinsen von 4 % p.a. (§ 1000 ABGB) zu verrechnen.

                Liebe Grüße
                Carmen GREGOR
                TEXT,
        };
    }
}
