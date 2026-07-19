<?php

namespace App\Http\Requests\Public;

use Illuminate\Foundation\Http\FormRequest;

class StoreInquiryRequest extends FormRequest
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
            'email' => ['required', 'email', 'max:255'],
            'dog_name' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:2000'],
            'interested_package' => ['nullable', 'string', 'max:255'],
        ];
    }

    protected function getRedirectUrl(): string
    {
        return route('landing').'#kontakt';
    }
}
