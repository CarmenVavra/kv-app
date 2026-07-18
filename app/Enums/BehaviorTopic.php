<?php

namespace App\Enums;

enum BehaviorTopic: string
{
    case Leinenaggression = 'leinenaggression';
    case Stress = 'stress';
    case Mantrailing = 'mantrailing';
    case Impulskontrolle = 'impulskontrolle';
    case Welpentraining = 'welpentraining';
    case Sonstiges = 'sonstiges';

    public function label(): string
    {
        return match ($this) {
            self::Leinenaggression => 'Leinenaggression',
            self::Stress => 'Stress',
            self::Mantrailing => 'Mantrailing',
            self::Impulskontrolle => 'Impulskontrolle',
            self::Welpentraining => 'Welpentraining',
            self::Sonstiges => 'Sonstiges',
        };
    }
}
