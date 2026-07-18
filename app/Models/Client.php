<?php

namespace App\Models;

use App\Enums\BehaviorTopic;
use App\Enums\ClientStatus;
use Database\Factories\ClientFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    /** @use HasFactory<ClientFactory> */
    use HasFactory, SoftDeletes;

    /**
     * Avatar-Farbpalette laut Design-Tokens (siehe UI-Mockups).
     */
    public const AVATAR_PALETTE = ['#b5651d', '#6b8e5a', '#e0793c', '#8a7b6a', '#3a2f27'];

    protected $fillable = [
        'dog_name',
        'breed',
        'age_text',
        'behavior_topic',
        'dog_notes',
        'color',
        'owner_name',
        'email',
        'phone',
        'address_line',
        'postal_code',
        'city',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'behavior_topic' => BehaviorTopic::class,
            'status' => ClientStatus::class,
        ];
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }
}
