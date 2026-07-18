<?php

namespace App\Http\Controllers\Admin\Accounting;

use App\Enums\ExpenseStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreExpenseRequest;
use App\Models\Expense;
use Illuminate\Http\RedirectResponse;

class ExpenseController extends Controller
{
    public function store(StoreExpenseRequest $request): RedirectResponse
    {
        $receiptPath = $request->file('receipt')?->store('receipts', 'public');

        Expense::create($request->safe()->except('receipt') + [
            'status' => ExpenseStatus::Offen,
            'receipt_path' => $receiptPath,
        ]);

        return redirect()->route('admin.accounting.index', ['tab' => 'in']);
    }

    public function markPaid(Expense $expense): RedirectResponse
    {
        $expense->markPaid();

        return redirect()->route('admin.accounting.index', ['tab' => 'in']);
    }
}
