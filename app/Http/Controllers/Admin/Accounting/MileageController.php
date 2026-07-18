<?php

namespace App\Http\Controllers\Admin\Accounting;

use App\Enums\ExpenseCategory;
use App\Enums\ExpenseStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreMileageEntryRequest;
use App\Models\Expense;
use App\Models\MileageEntry;
use App\Services\MileageCalculator;
use Illuminate\Http\RedirectResponse;

class MileageController extends Controller
{
    public function store(StoreMileageEntryRequest $request, MileageCalculator $calculator): RedirectResponse
    {
        $km = (float) $request->validated('km');
        $amount = $calculator->calculate($km);

        $expense = Expense::create([
            'date' => today(),
            'supplier' => 'Kilometergeld (Fahrtkosten)',
            'category' => ExpenseCategory::Fahrzeugkosten,
            'amount' => $amount,
            'status' => ExpenseStatus::Bezahlt,
        ]);

        MileageEntry::create([
            'date' => today(),
            'km' => $km,
            'rate_per_km' => $calculator->ratePerKm(),
            'amount' => $amount,
            'expense_id' => $expense->id,
        ]);

        return redirect()->route('admin.accounting.index', ['tab' => 'fahrtkosten']);
    }
}
