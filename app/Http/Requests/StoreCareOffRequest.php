<?php

namespace App\Http\Requests;

use App\Support\SeasonValidation;
use Illuminate\Foundation\Http\FormRequest;

class StoreCareOffRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', SeasonValidation::unique('care_offs', 'name')],
            'is_active' => ['boolean'],
        ];
    }
}
