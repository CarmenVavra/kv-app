<?php

namespace App\Services;

use App\Models\Setting;

class ParkingCalculator
{
    public function ratePerHalfHour(): float
    {
        return (float) Setting::current()->kurzparkschein_tarif_pro_halbe_stunde;
    }

    /**
     * Der Wiener Kurzparkschein-Tarif gilt pro halbe Stunde - die eingegebenen Stunden werden
     * daher zuerst in Halbstunden-Bloecke umgerechnet (Stunden x 2), bevor der Tarif angewendet wird.
     */
    public function calculate(float $hours): float
    {
        return round($hours * 2 * $this->ratePerHalfHour(), 2);
    }
}
