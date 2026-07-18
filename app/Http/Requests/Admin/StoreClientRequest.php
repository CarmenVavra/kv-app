<?php

namespace App\Http\Requests\Admin;

use App\Enums\BehaviorTopic;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreClientRequest extends FormRequest
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
            'dog_name' => ['required', 'string', 'max:255'],
            'breed' => ['nullable', 'string', 'max:255'],
            'age_text' => ['nullable', 'string', 'max:255'],
            'behavior_topic' => ['required', Rule::enum(BehaviorTopic::class)],
            'dog_notes' => ['nullable', 'string'],
            'owner_name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'address_line' => ['nullable', 'string', 'max:255'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'city' => ['nullable', 'string', 'max:255'],
        ];
    }
}
