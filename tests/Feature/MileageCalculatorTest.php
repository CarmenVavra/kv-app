<?php

use App\Services\MileageCalculator;

it('calculates the reimbursement as km times the official rate', function () {
    $calculator = new MileageCalculator;

    expect($calculator->ratePerKm())->toBe(0.5)
        ->and($calculator->calculate(24))->toBe(12.0)
        ->and($calculator->calculate(0.1))->toBe(0.05);
});
