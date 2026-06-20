<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAirlineRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // gated by route middleware
    }

    public function rules(): array
    {
        return [
            'name'       => ['required', 'string', 'max:255'],
            'code'       => ['required', 'string', 'max:255', Rule::unique('airlines', 'code')],
            'iata_code'  => ['nullable', 'string', 'max:10'],
            'icao_code'  => ['nullable', 'string', 'max:10'],
            'logo'       => ['nullable', 'image', 'max:2048'],
            'country_id' => ['nullable', 'exists:countries,id'],
            'is_active'  => ['boolean'],
        ];
    }
}