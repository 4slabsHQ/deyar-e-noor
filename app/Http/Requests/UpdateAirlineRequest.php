<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAirlineRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // gated by route middleware
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:255', Rule::unique('airlines', 'code')->ignore($this->route('airline')->id)],
            'iata_code' => ['nullable', 'string', 'max:10'],
            'icao_code' => ['nullable', 'string', 'max:10'],
            'logo' => ['nullable', 'image', 'max:2048'],
            'remove_logo' => ['nullable', 'boolean'],
            'country_id' => ['nullable', 'exists:countries,id'],
            'is_active' => ['boolean'],
        ];
    }
}
