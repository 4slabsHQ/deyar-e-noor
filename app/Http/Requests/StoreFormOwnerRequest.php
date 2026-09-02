<?php

namespace App\Http\Requests;

use App\Support\SeasonValidation;
use Illuminate\Foundation\Http\FormRequest;

class StoreFormOwnerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', SeasonValidation::unique('form_owners', 'name')],
            'limit' => ['nullable', 'integer', 'min:1'],
            'is_active' => ['boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('limit') && $this->input('limit') === '') {
            $this->merge(['limit' => null]);
        }

        $this->merge(['is_active' => $this->boolean('is_active')]);
    }
}
