<?php

namespace App\Models;

use App\Enums\DunningLevel;
use Database\Factories\PaymentReminderFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentReminder extends Model
{
    /** @use HasFactory<PaymentReminderFactory> */
    use HasFactory;

    protected $fillable = [
        'invoice_id',
        'level',
        'subject',
        'body',
        'interest_amount',
        'collection_fee',
        'sent_at',
    ];

    protected function casts(): array
    {
        return [
            'level' => DunningLevel::class,
            'interest_amount' => 'decimal:2',
            'collection_fee' => 'decimal:2',
            'sent_at' => 'datetime',
        ];
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }
}
