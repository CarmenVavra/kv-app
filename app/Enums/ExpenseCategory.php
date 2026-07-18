<?php

namespace App\Enums;

enum ExpenseCategory: string
{
    case Buerobedarf = 'buerobedarf';
    case Trainingsmaterial = 'trainingsmaterial';
    case Weiterbildung = 'weiterbildung';
    case Fahrzeugkosten = 'fahrzeugkosten';
    case TelefonUndInternet = 'telefon_und_internet';
    case Sonstiges = 'sonstiges';

    public function label(): string
    {
        return match ($this) {
            self::Buerobedarf => 'Bürobedarf',
            self::Trainingsmaterial => 'Trainingsmaterial',
            self::Weiterbildung => 'Weiterbildung',
            self::Fahrzeugkosten => 'Fahrzeugkosten',
            self::TelefonUndInternet => 'Telefon & Internet',
            self::Sonstiges => 'Sonstiges',
        };
    }
}
