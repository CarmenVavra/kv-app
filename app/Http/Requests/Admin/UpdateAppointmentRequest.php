<?php

namespace App\Http\Requests\Admin;

use App\Enums\AppointmentStatus;
use Illuminate\Validation\Rule;

class UpdateAppointmentRequest extends StoreAppointmentRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return parent::rules() + [
            'status' => ['required', Rule::enum(AppointmentStatus::class)],
        ];
    }
}
