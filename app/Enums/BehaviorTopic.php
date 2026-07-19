<?php

namespace App\Enums;

enum BehaviorTopic: string
{
    case Leinenaggression = 'leinenaggression';
    case Stress = 'stress';
    case Impulskontrolle = 'impulskontrolle';
    case Mantrailing = 'mantrailing';
    case Sonstiges = 'sonstiges';

    public function label(): string
    {
        return match ($this) {
            self::Leinenaggression => 'Leinenaggression',
            self::Stress => 'Stress',
            self::Impulskontrolle => 'Impulskontrolle',
            self::Mantrailing => 'Mantrailing',
            self::Sonstiges => 'Sonstiges',
        };
    }
}
