<?php

namespace App\Http\Requests\Admin;

use App\Models\Appointment;

class UpdateAppointmentRequest extends StoreAppointmentRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return parent::rules() + [
            'status_option' => ['required', 'in:'.implode(',', array_keys(Appointment::statusOptions()))],
        ];
    }
}
