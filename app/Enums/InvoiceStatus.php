<?php

namespace App\Enums;

enum InvoiceStatus: string
{
    case Offen = 'offen';
    case Bezahlt = 'bezahlt';
    case Ueberfaellig = 'ueberfaellig';

    public function label(): string
    {
        return match ($this) {
            self::Offen => 'Offen',
            self::Bezahlt => 'Bezahlt',
            self::Ueberfaellig => 'Überfällig',
        };
    }

    public function badgeBg(): string
    {
        return match ($this) {
            self::Offen => 'var(--color-warning-bg)',
            self::Bezahlt => 'var(--color-success-bg)',
            self::Ueberfaellig => 'var(--color-error-bg)',
        };
    }

    public function badgeText(): string
    {
        return match ($this) {
            self::Offen => 'var(--color-warning-text)',
            self::Bezahlt => 'var(--color-success-text)',
            self::Ueberfaellig => 'var(--color-error-text)',
        };
    }
}
