<?php

namespace App\Models;

use App\Enums\InquiryStatus;
use Database\Factories\InquiryFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inquiry extends Model
{
    /** @use HasFactory<InquiryFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'dog_name',
        'message',
        'interested_package',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'status' => InquiryStatus::class,
        ];
    }
}
