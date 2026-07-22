<?php

namespace App\Http\Requests\Admin;

use App\Enums\InvoiceService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreInvoiceRequest extends FormRequest
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
            'client_id' => ['required', 'exists:clients,id'],
            'service' => ['required', Rule::enum(InvoiceService::class)],
            'amount' => ['required', 'numeric', 'min:0.01', 'max:999999.99'],
            'charge_mileage' => ['nullable', 'boolean'],
            'mileage_km' => ['required_if:charge_mileage,1', 'nullable', 'numeric', 'min:0.1', 'max:999'],
            'charge_parking' => ['nullable', 'boolean'],
            'parking_amount' => ['required_if:charge_parking,1', 'nullable', 'numeric', 'min:0.01', 'max:9999.99'],
        ];
    }
}
