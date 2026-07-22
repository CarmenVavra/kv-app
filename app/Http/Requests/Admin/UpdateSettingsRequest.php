<?php

namespace App\Http\Requests\Admin;

use App\Models\Setting;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSettingsRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'praxisname' => ['nullable', 'string', 'max:255'],
            'photo' => ['nullable', 'file', 'mimes:jpg,jpeg,png', 'max:5120'],
            'notify_new_inquiries' => ['boolean'],
            'notify_appointment_reminders' => ['boolean'],
            'notify_weekly_summary' => ['boolean'],
            'default_appointment_duration' => ['required', 'integer', Rule::in([30, 45, 60, 90])],
            'working_days' => ['array'],
            'working_days.*' => [Rule::in(Setting::WEEKDAYS)],
            'kilometergeld_pro_km' => ['required', 'numeric', 'min:0.01', 'max:9.99'],
            'kurzparkschein_tarif_pro_halbe_stunde' => ['required', 'numeric', 'min:0.01', 'max:99.99'],
            'stornogebuehr' => ['required', 'numeric', 'min:0.01', 'max:9999.99'],
        ];
    }
}
