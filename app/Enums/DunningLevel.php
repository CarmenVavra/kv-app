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

    /**
     * Kompaktere Variante fuer die Mahnstufe-Spalte in der Rechnungsliste (Pill mit wenig Platz).
     */
    public function shortLabel(): string
    {
        return match ($this) {
            self::Erinnerung => 'Erinnerung',
            self::Mahnung1 => '1. Mahnung',
            self::Mahnung2 => '2. Mahnung',
        };
    }

    /**
     * Fachlicher Kurzkontext (Ton + ueblicher Zeitpunkt) - als title-Tooltip an der Pill.
     */
    public function timingHint(): string
    {
        return match ($this) {
            self::Erinnerung => 'Zahlungserinnerung (ca. 3–5 Tage nach Verzug): höflich, ohne Gebühren, neue Frist 5 Tage.',
            self::Mahnung1 => '1. Mahnung (ca. 10–14 Tage nach Verzug): bestimmterer Ton, noch ohne Zinsen.',
            self::Mahnung2 => '2. und letzte Mahnung (ca. 20–25 Tage nach Verzug): deutliche Aufforderung, Verzugszinsen (4 % p.a.), Androhung von Inkasso oder gerichtlichen Schritten.',
        };
    }

    /**
     * Aufsteigender Rang fuer Vergleiche (Erinnerung < 1. Mahnung < 2. Mahnung).
     */
    public function rank(): int
    {
        return match ($this) {
            self::Erinnerung => 1,
            self::Mahnung1 => 2,
            self::Mahnung2 => 3,
        };
    }

    public function badgeBg(): string
    {
        return match ($this) {
            self::Erinnerung => 'var(--color-warning-bg)',
            self::Mahnung1 => 'var(--color-error-bg)',
            self::Mahnung2 => 'var(--color-error-text)',
        };
    }

    public function badgeText(): string
    {
        return match ($this) {
            self::Erinnerung => 'var(--color-warning-text)',
            self::Mahnung1 => 'var(--color-error-text)',
            self::Mahnung2 => '#ffffff',
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
     * Gesetzliche Verzugszinsen (B2C: 4 % p.a., § 1000 ABGB) werden erst ab der 2. Mahnung
     * verrechnet - nicht bei der kostenlosen Erinnerung oder der 1. Mahnung. Ein pauschaler
     * Betreibungsschaden (40 €, § 1333 Abs. 2 ABGB) faellt gegenueber Verbrauchern gar nicht an.
     */
    public function chargesInterest(): bool
    {
        return $this === self::Mahnung2;
    }
}
