<?php

namespace App\Models;

use App\Enums\InvoiceStatus;
use Carbon\Carbon;
use Database\Factories\InvoiceFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    /** @use HasFactory<InvoiceFactory> */
    use HasFactory;

    protected $fillable = [
        'number',
        'client_id',
        'appointment_id',
        'issue_date',
        'due_date',
        'paid_at',
        'pdf_path',
    ];

    protected function casts(): array
    {
        return [
            'issue_date' => 'date',
            'due_date' => 'date',
            'paid_at' => 'datetime',
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    public function lineItems(): HasMany
    {
        return $this->hasMany(InvoiceLineItem::class);
    }

    public function paymentReminders(): HasMany
    {
        return $this->hasMany(PaymentReminder::class);
    }

    protected function totalAmount(): Attribute
    {
        return Attribute::get(
            fn () => $this->lineItems->sum(fn (InvoiceLineItem $item) => $item->amount)
        );
    }

    protected function status(): Attribute
    {
        return Attribute::get(function () {
            if ($this->paid_at !== null) {
                return InvoiceStatus::Bezahlt;
            }

            return $this->due_date->isPast() ? InvoiceStatus::Ueberfaellig : InvoiceStatus::Offen;
        });
    }

    /**
     * Das Leistungsdatum ist das Datum der tatsaechlich erbrachten Leistung(en), nicht das
     * Rechnungsdatum - beide koennen auseinanderfallen, wenn ein Termin erst spaeter verrechnet
     * wird. Bei mehreren Terminen mit unterschiedlichem Datum wird der Zeitraum ausgewiesen; ohne
     * terminverknuepfte Position (z.B. reine Sonstige-Leistung) faellt es auf das Rechnungsdatum
     * zurueck, mangels eines anderen belastbaren Datums.
     */
    protected function serviceDateLabel(): Attribute
    {
        return Attribute::get(function () {
            $dates = $this->lineItems
                ->pluck('appointment.starts_at')
                ->filter()
                ->unique(fn (Carbon $date) => $date->toDateString())
                ->sort()
                ->values();

            if ($dates->isEmpty()) {
                return $this->issue_date->format('d.m.Y');
            }

            $first = $dates->first()->format('d.m.Y');
            $last = $dates->last()->format('d.m.Y');

            return $first === $last ? $first : "{$first} – {$last}";
        });
    }

    public function markPaid(): void
    {
        $this->update(['paid_at' => Carbon::now()]);
    }
}
