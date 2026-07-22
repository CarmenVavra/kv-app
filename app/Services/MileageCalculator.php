<?php

namespace App\Services;

use App\Models\Setting;

class MileageCalculator
{
    public function ratePerKm(): float
    {
        return (float) Setting::current()->kilometergeld_pro_km;
    }

    public function calculate(float $km): float
    {
        return round($km * $this->ratePerKm(), 2);
    }
}
