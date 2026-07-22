<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\Validator as ValidatorContract;
use Illuminate\Foundation\Http\FormRequest;

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
            'appointments' => ['nullable', 'array'],
            'appointments.*' => ['integer', 'exists:appointments,id'],
            'appointment_amounts' => ['array'],
            'appointment_amounts.*' => ['nullable', 'numeric', 'min:0.01', 'max:999999.99'],
            'sonstiges' => ['nullable', 'boolean'],
            'sonstiges_description' => ['required_if:sonstiges,1', 'nullable', 'string', 'max:255'],
            'sonstiges_amount' => ['required_if:sonstiges,1', 'nullable', 'numeric', 'min:0.01', 'max:999999.99'],
            'charge_mileage' => ['nullable', 'boolean'],
            'mileage_km' => ['required_if:charge_mileage,1', 'nullable', 'numeric', 'min:0.1', 'max:999'],
            'charge_parking' => ['nullable', 'boolean'],
            'parking_hours' => ['required_if:charge_parking,1', 'nullable', 'numeric', 'min:0.5', 'max:24'],
        ];
    }

    public function withValidator(ValidatorContract $validator): void
    {
        $validator->after(function (ValidatorContract $validator): void {
            $hasAppointments = filled($this->input('appointments', []));
            $hasSonstiges = $this->boolean('sonstiges');
            $hasMileage = $this->boolean('charge_mileage');
            $hasParking = $this->boolean('charge_parking');

            if (! $hasAppointments && ! $hasSonstiges && ! $hasMileage && ! $hasParking) {
                $validator->errors()->add('appointments', 'Bitte mindestens eine Leistung, Fahrtkosten oder einen Kurzparkschein auswählen.');
            }
        });
    }
}
