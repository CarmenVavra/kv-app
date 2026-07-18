<?php

namespace App\Enums;

enum RecurringFrequency: string
{
    case Monatlich = 'monatlich';
    case Woechentlich = 'woechentlich';

    public function label(): string
    {
        return match ($this) {
            self::Monatlich => 'monatlich',
            self::Woechentlich => 'wöchentlich',
        };
    }
}
