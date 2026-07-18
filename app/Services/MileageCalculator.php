<?php

namespace App\Services;

class MileageCalculator
{
    public function ratePerKm(): float
    {
        return (float) config('accounting.kilometergeld_pro_km');
    }

    public function calculate(float $km): float
    {
        return round($km * $this->ratePerKm(), 2);
    }
}
