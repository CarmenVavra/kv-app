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

    public function markPaid(): void
    {
        $this->update(['paid_at' => Carbon::now()]);
    }
}
