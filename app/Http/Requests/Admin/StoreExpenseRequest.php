<?php

namespace App\Http\Requests\Admin;

use App\Enums\ExpenseCategory;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreExpenseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'supplier' => ['required', 'string', 'max:255'],
            'category' => ['required', Rule::enum(ExpenseCategory::class)],
            'date' => ['required', 'date'],
            'amount' => ['required', 'numeric', 'min:0.01', 'max:999999.99'],
            'receipt' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
        ];
    }
}
