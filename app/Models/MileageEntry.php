<?php

namespace App\Models;

use Database\Factories\MileageEntryFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MileageEntry extends Model
{
    /** @use HasFactory<MileageEntryFactory> */
    use HasFactory;

    protected $fillable = [
        'date',
        'km',
        'rate_per_km',
        'amount',
        'expense_id',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'km' => 'decimal:1',
            'rate_per_km' => 'decimal:2',
            'amount' => 'decimal:2',
        ];
    }

    public function expense(): BelongsTo
    {
        return $this->belongsTo(Expense::class);
    }
}
