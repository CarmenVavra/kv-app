<?php

namespace App\Enums;

enum AppointmentStatus: string
{
    case Ausstehend = 'ausstehend';
    case Bestaetigt = 'bestaetigt';
    case Abgeschlossen = 'abgeschlossen';
    case Abgesagt = 'abgesagt';

    public function label(): string
    {
        return match ($this) {
            self::Ausstehend => 'Ausstehend',
            self::Bestaetigt => 'Bestätigt',
            self::Abgeschlossen => 'Abgeschlossen',
            self::Abgesagt => 'Abgesagt',
        };
    }
}
