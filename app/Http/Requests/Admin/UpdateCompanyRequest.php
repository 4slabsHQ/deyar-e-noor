<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCompanyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('company')->id;

        return [
            'name'                => ['required', 'string', 'max:255'],
            'legal_name'          => ['nullable', 'string', 'max:255'],
            'registration_number' => ['nullable', 'string', 'max:100', Rule::unique('companies')->ignore($id)],
            'tax_number'          => ['nullable', 'string', 'max:100', Rule::unique('companies')->ignore($id)],
            'email'               => ['nullable', 'email', 'max:255'],
            'phone'               => ['nullable', 'string', 'max:20'],
            'website'             => ['nullable', 'url', 'max:255'],
            'logo'                => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'address'             => ['nullable', 'string'],
            'city'                => ['nullable', 'string', 'max:100'],
            'state'               => ['nullable', 'string', 'max:100'],
            'country'             => ['nullable', 'string', 'max:100'],
            'postal_code'         => ['nullable', 'string', 'max:20'],
            'currency'            => ['nullable', 'string', 'max:10'],
            'timezone'            => ['nullable', 'string', 'max:100'],
            'is_active'           => ['boolean'],
        ];
    }
}