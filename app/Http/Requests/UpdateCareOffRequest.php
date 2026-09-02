<?php

namespace App\Http\Requests;

use App\Support\SeasonValidation;
use Illuminate\Foundation\Http\FormRequest;

class UpdateCareOffRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', SeasonValidation::unique('care_offs', 'name', $this->route('care_off'))],
            'is_active' => ['boolean'],
        ];
    }
}
