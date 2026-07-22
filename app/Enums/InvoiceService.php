<?php

namespace App\Enums;

enum InvoiceService: string
{
    case Erstberatung = 'erstberatung';
    case Leinenaggression = 'leinenaggression';
    case Stress = 'stress';
    case Impulskontrolle = 'impulskontrolle';
    case Mantrailing = 'mantrailing';
    case Sonstiges = 'sonstiges';

    public function label(): string
    {
        return match ($this) {
            self::Erstberatung => 'Erstberatung',
            self::Leinenaggression => 'Leinenaggression',
            self::Stress => 'Stress',
            self::Impulskontrolle => 'Impulskontrolle',
            self::Mantrailing => 'Mantrailing',
            self::Sonstiges => 'Sonstiges',
        };
    }

    /**
     * Erstberatung 90 EUR, jede weitere Sitzung pauschal 60 EUR.
     */
    public function defaultAmount(): float
    {
        return match ($this) {
            self::Erstberatung => 90.00,
            default => 60.00,
        };
    }
}
