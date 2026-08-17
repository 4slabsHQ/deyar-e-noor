<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreCompanyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('quota') && $this->input('quota') === '') {
            $this->merge(['quota' => null]);
        }
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:20', 'alpha_num', 'unique:companies,code'],
            'enr_number' => ['nullable', 'string', 'max:100'],
            'munazzam_code' => ['nullable', 'string', 'max:100'],
            'quota' => ['nullable', 'integer', 'min:1'],
            'legal_name' => ['nullable', 'string', 'max:255'],
            'registration_number' => ['nullable', 'string', 'max:100', 'unique:companies'],
            'tax_number' => ['nullable', 'string', 'max:100', 'unique:companies'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'address' => ['nullable', 'string'],
            'is_active' => ['boolean'],
        ];
    }
}
