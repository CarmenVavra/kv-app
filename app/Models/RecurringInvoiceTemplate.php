<?php

namespace App\Models;

use App\Enums\RecurringFrequency;
use Database\Factories\RecurringInvoiceTemplateFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RecurringInvoiceTemplate extends Model
{
    /** @use HasFactory<RecurringInvoiceTemplateFactory> */
    use HasFactory;

    protected $fillable = [
        'title',
        'client_id',
        'description',
        'amount',
        'frequency',
        'next_run_date',
        'active',
    ];

    protected function casts(): array
    {
        return [
            'frequency' => RecurringFrequency::class,
            'next_run_date' => 'date',
            'amount' => 'decimal:2',
            'active' => 'boolean',
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function advanceNextRunDate(): void
    {
        $this->update([
            'next_run_date' => match ($this->frequency) {
                RecurringFrequency::Monatlich => $this->next_run_date->copy()->addMonth(),
                RecurringFrequency::Woechentlich => $this->next_run_date->copy()->addWeek(),
            },
        ]);
    }
}
