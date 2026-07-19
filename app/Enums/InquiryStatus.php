<?php

namespace App\Enums;

enum InquiryStatus: string
{
    case Neu = 'neu';
    case Kontaktiert = 'kontaktiert';
    case Konvertiert = 'konvertiert';
    case Archiviert = 'archiviert';

    public function label(): string
    {
        return match ($this) {
            self::Neu => 'Neu',
            self::Kontaktiert => 'Kontaktiert',
            self::Konvertiert => 'Konvertiert',
            self::Archiviert => 'Archiviert',
        };
    }

    public function badgeBg(): string
    {
        return match ($this) {
            self::Neu => 'var(--color-warning-bg)',
            self::Kontaktiert => 'var(--color-bg)',
            self::Konvertiert => 'var(--color-success-bg)',
            self::Archiviert => 'var(--color-bg)',
        };
    }

    public function badgeText(): string
    {
        return match ($this) {
            self::Neu => 'var(--color-warning-text)',
            self::Kontaktiert => 'var(--color-text-muted-2)',
            self::Konvertiert => 'var(--color-success-text)',
            self::Archiviert => 'var(--color-text-muted)',
        };
    }
}
