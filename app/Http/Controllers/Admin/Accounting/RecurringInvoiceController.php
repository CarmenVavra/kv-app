<?php

namespace App\Http\Controllers\Admin\Accounting;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreRecurringInvoiceTemplateRequest;
use App\Models\RecurringInvoiceTemplate;
use Illuminate\Http\RedirectResponse;

class RecurringInvoiceController extends Controller
{
    public function store(StoreRecurringInvoiceTemplateRequest $request): RedirectResponse
    {
        RecurringInvoiceTemplate::create($request->validated() + ['active' => true]);

        return redirect()->route('admin.accounting.index', ['tab' => 'wiederkehrend']);
    }

    public function toggleActive(RecurringInvoiceTemplate $recurringInvoiceTemplate): RedirectResponse
    {
        $recurringInvoiceTemplate->update(['active' => ! $recurringInvoiceTemplate->active]);

        return redirect()->route('admin.accounting.index', ['tab' => 'wiederkehrend']);
    }
}
