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

    protected function prepareForValidation(): void
    {
        if ($this->has('quota') && $this->input('quota') === '') {
            $this->merge(['quota' => null]);
        }
    }

    public function rules(): array
    {
        $id = $this->route('company')->id;

        return [
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:20', 'alpha_num', Rule::unique('companies')->ignore($id)],
            'enr_number' => ['nullable', 'string', 'max:100'],
            'munazzam_code' => ['nullable', 'string', 'max:100'],
            'quota' => ['nullable', 'integer', 'min:1'],
            'legal_name' => ['nullable', 'string', 'max:255'],
            'registration_number' => ['nullable', 'string', 'max:100', Rule::unique('companies')->ignore($id)],
            'tax_number' => ['nullable', 'string', 'max:100', Rule::unique('companies')->ignore($id)],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'remove_logo' => ['nullable', 'boolean'],
            'address' => ['nullable', 'string'],
            'is_active' => ['boolean'],
        ];
    }
}
