<?php

namespace App\Http\Requests\Admin;

use App\Enums\AppointmentType;
use App\Enums\BehaviorTopic;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAppointmentRequest extends FormRequest
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
            'type' => ['required', Rule::enum(AppointmentType::class)],
            'behavior_topic' => ['required', Rule::enum(BehaviorTopic::class)],
            'starts_at' => ['required', 'date'],
            'duration_minutes' => ['required', 'integer', 'min:15', 'max:480'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
