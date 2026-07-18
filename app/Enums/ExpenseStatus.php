<?php

namespace App\Enums;

enum ExpenseStatus: string
{
    case Offen = 'offen';
    case Bezahlt = 'bezahlt';

    public function label(): string
    {
        return match ($this) {
            self::Offen => 'Offen',
            self::Bezahlt => 'Bezahlt',
        };
    }

    public function badgeBg(): string
    {
        return match ($this) {
            self::Offen => 'var(--color-warning-bg)',
            self::Bezahlt => 'var(--color-success-bg)',
        };
    }

    public function badgeText(): string
    {
        return match ($this) {
            self::Offen => 'var(--color-warning-text)',
            self::Bezahlt => 'var(--color-success-text)',
        };
    }
}
