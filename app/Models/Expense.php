<?php

namespace App\Models;

use App\Enums\ExpenseCategory;
use App\Enums\ExpenseStatus;
use Database\Factories\ExpenseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    /** @use HasFactory<ExpenseFactory> */
    use HasFactory;

    protected $fillable = [
        'date',
        'supplier',
        'category',
        'amount',
        'status',
        'receipt_path',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'category' => ExpenseCategory::class,
            'status' => ExpenseStatus::class,
            'amount' => 'decimal:2',
        ];
    }

    public function markPaid(): void
    {
        $this->update(['status' => ExpenseStatus::Bezahlt]);
    }
}
