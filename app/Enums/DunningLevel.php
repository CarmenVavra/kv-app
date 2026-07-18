<?php

namespace App\Enums;

enum DunningLevel: string
{
    case Erinnerung = 'erinnerung';
    case Mahnung1 = 'mahnung_1';
    case Mahnung2 = 'mahnung_2';

    public function label(): string
    {
        return match ($this) {
            self::Erinnerung => 'Zahlungserinnerung',
            self::Mahnung1 => '1. Mahnung',
            self::Mahnung2 => '2. Mahnung',
        };
    }

    public function next(): self
    {
        return match ($this) {
            self::Erinnerung => self::Mahnung1,
            self::Mahnung1 => self::Mahnung2,
            self::Mahnung2 => self::Mahnung2,
        };
    }

    public function actionLabel(): string
    {
        return match ($this) {
            self::Erinnerung => 'Zahlungserinnerung senden',
            self::Mahnung1 => '1. Mahnung senden',
            self::Mahnung2 => '2. Mahnung senden (mit Verzugszinsen)',
        };
    }

    /**
     * Verzugszinsen/Betreibungsschaden duerfen im B2B-Verkehr in Oesterreich erst ab der 2. Mahnung
     * verrechnet werden - nicht bei der kostenlosen Erinnerung oder der 1. Mahnung.
     */
    public function chargesInterestAndFee(): bool
    {
        return $this === self::Mahnung2;
    }
}
