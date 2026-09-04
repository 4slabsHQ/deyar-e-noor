<?php

namespace App\Http\Requests;

use App\Enums\PropertyCity;
use App\Enums\PropertyType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePropertyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'city' => ['required', Rule::enum(PropertyCity::class)],
            'type' => ['required', Rule::enum(PropertyType::class)],
            'is_active' => ['boolean'],
            'akads' => ['nullable', 'array'],
            'akads.*.id' => ['nullable', 'integer'],
            'akads.*.akad_number' => ['required_with:akads', 'string', 'max:100'],
            'akads.*.label' => ['nullable', 'string', 'max:255'],
            'akads.*.notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_active' => $this->boolean('is_active'),
        ]);
    }
}
