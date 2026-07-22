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
        'cancellation_fee',
        'notes',
        'reminder_sent_at',
    ];

    protected function casts(): array
    {
        return [
            'type' => AppointmentType::class,
            'behavior_topic' => BehaviorTopic::class,
            'status' => AppointmentStatus::class,
            'cancellation_fee' => 'boolean',
            'starts_at' => 'datetime',
            'reminder_sent_at' => 'datetime',
        ];
    }

    /**
     * Auswahlwerte fuer die Inline-Status-Steuerung im Trainingsverlauf. "Abgesagt" ist bewusst in
     * zwei Zeilen aufgeteilt (mit/ohne Stornogebuehr), da die Gebuehrenfrage die einzige zusaetzliche
     * Information ist, die eine Absage fuer die Verrechnung braucht - der Controller mappt die beiden
     * abgesagt_*-Werte auf status = Abgesagt + cancellation_fee true/false.
     *
     * @return array<string, string>
     */
    public static function statusOptions(): array
    {
        return [
            AppointmentStatus::Ausstehend->value => AppointmentStatus::Ausstehend->label(),
            AppointmentStatus::Bestaetigt->value => AppointmentStatus::Bestaetigt->label(),
            AppointmentStatus::Abgeschlossen->value => AppointmentStatus::Abgeschlossen->label(),
            'abgesagt_mit_gebuehr' => 'Abgesagt · mit Stornogebühr',
            'abgesagt_ohne_gebuehr' => 'Abgesagt · keine Stornogebühr',
        ];
    }

    /**
     * Der zur aktuellen Kombination aus status + cancellation_fee passende statusOptions()-Schluessel
     * (fuer das <select>-Vorbelegen im Trainingsverlauf).
     */
    public function statusOptionValue(): string
    {
        if ($this->status === AppointmentStatus::Abgesagt) {
            return $this->cancellation_fee ? 'abgesagt_mit_gebuehr' : 'abgesagt_ohne_gebuehr';
        }

        return $this->status->value;
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

    /**
     * Beschriftung auf der Rechnung / im Rechnungsdialog. Eine abgesagte (aber gebuehrenpflichtige)
     * Leistung wird als Stornogebuehr ausgewiesen, eine erbrachte Leistung normal als Terminart · Leistung.
     */
    public function billingLabel(): string
    {
        return $this->status === AppointmentStatus::Abgesagt
            ? 'Stornogebühr · '.$this->type->label()
            : $this->typeAndTopicLabel();
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
     * "Offene Leistung": ein noch nicht verrechneter Termin, dessen Leistung tatsaechlich zu
     * verrechnen ist - entweder weil sie erbracht wurde (status = Abgeschlossen) oder weil eine
     * kurzfristige Absage eine Stornogebuehr ausgeloest hat (status = Abgesagt + cancellation_fee).
     *
     * Die Nutzerin pflegt den Status jetzt aktiv im Trainingsverlauf ("erst nach erbrachter Leistung
     * verrechnen"), deshalb ist die Verrechenbarkeit wieder statusgetrieben statt an das blosse
     * Datum gekoppelt: bestaetigte/ausstehende Termine (noch nicht erbracht) und folgenlos abgesagte
     * Termine sind bewusst nicht verrechenbar.
     */
    public function scopeUnbilled(Builder $query): Builder
    {
        return $query
            ->where(function (Builder $billable): void {
                $billable
                    ->where('status', AppointmentStatus::Abgeschlossen)
                    ->orWhere(function (Builder $cancelledWithFee): void {
                        $cancelledWithFee
                            ->where('status', AppointmentStatus::Abgesagt)
                            ->where('cancellation_fee', true);
                    });
            })
            ->whereDoesntHave('invoiceLineItem');
    }
}
