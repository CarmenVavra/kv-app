<?php

namespace App\Models;

use App\Enums\AppointmentStatus;
use App\Enums\AppointmentType;
use App\Enums\BehaviorTopic;
use Carbon\Carbon;
use Database\Factories\AppointmentFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Appointment extends Model
{
    /** @use HasFactory<AppointmentFactory> */
    use HasFactory;

    protected $fillable = [
        'client_id',
        'type',
        'behavior_topic',
        'starts_at',
        'duration_minutes',
        'status',
        'notes',
        'reminder_sent_at',
    ];

    protected function casts(): array
    {
        return [
            'type' => AppointmentType::class,
            'behavior_topic' => BehaviorTopic::class,
            'status' => AppointmentStatus::class,
            'starts_at' => 'datetime',
            'reminder_sent_at' => 'datetime',
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * "Terminart · Leistung" fuer Anzeigen, in denen beide Facetten eines Termins zusammengehoeren
     * (Kalender-Agenda, Klientenprofil, offene Leistungen im Rechnungsdialog). Faellt auf die reine
     * Terminart zurueck, wenn ein (aelterer) Termin noch keine Leistung hat.
     */
    public function typeAndTopicLabel(): string
    {
        return $this->behavior_topic
            ? $this->type->label().' · '.$this->behavior_topic->label()
            : $this->type->label();
    }

    public function invoiceLineItem(): HasOne
    {
        return $this->hasOne(InvoiceLineItem::class);
    }

    public function scopeOnDay(Builder $query, Carbon $day): Builder
    {
        return $query->whereBetween('starts_at', [$day->copy()->startOfDay(), $day->copy()->endOfDay()]);
    }

    public function scopeUpcoming(Builder $query): Builder
    {
        return $query->where('starts_at', '>=', now())->orderBy('starts_at');
    }

    /**
     * "Offene Leistung": ein abgeschlossener Termin, der noch auf keiner Rechnung als
     * Rechnungsposten aufscheint.
     */
    public function scopeUnbilled(Builder $query): Builder
    {
        return $query->where('status', AppointmentStatus::Abgeschlossen)->whereDoesntHave('invoiceLineItem');
    }
}
