<?php

namespace App\Models;

use Database\Factories\SettingFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    /** @use HasFactory<SettingFactory> */
    use HasFactory;

    public const WEEKDAYS = ['Mo', 'Di', 'Mi', 'Do', 'Fr', 'Sa', 'So'];

    protected $fillable = [
        'name',
        'email',
        'phone',
        'praxisname',
        'avatar_path',
        'notify_new_inquiries',
        'notify_appointment_reminders',
        'notify_weekly_summary',
        'default_appointment_duration',
        'working_days',
    ];

    protected function casts(): array
    {
        return [
            'notify_new_inquiries' => 'boolean',
            'notify_appointment_reminders' => 'boolean',
            'notify_weekly_summary' => 'boolean',
            'working_days' => 'array',
        ];
    }

    /**
     * Es gibt genau eine Settings-Zeile fuer die einzige Admin-Nutzerin - kein Model::first()
     * an jeder Aufrufstelle, das bei leerer Tabelle null zurueckgeben wuerde.
     *
     * Alle Defaults werden hier explizit gesetzt statt sich auf die DB-Spalten-Defaults zu
     * verlassen: Eloquent laedt nach create() keine DB-seitigen Defaults ins Model zurueck, sonst
     * waeren z.B. notify_appointment_reminders/working_days direkt nach dem ersten Anlegen null.
     */
    public static function current(): self
    {
        return self::query()->firstOrCreate([], [
            'name' => 'Carmen GREGOR',
            'praxisname' => 'Mein Hund der Rowdy',
            'notify_new_inquiries' => true,
            'notify_appointment_reminders' => true,
            'notify_weekly_summary' => false,
            'default_appointment_duration' => 60,
            'working_days' => ['Fr', 'Sa', 'So'],
        ]);
    }
}
