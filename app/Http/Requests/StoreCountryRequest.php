<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCountryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // gated by route middleware
    }

    public function rules(): array
    {
        return [
            'name'       => ['required', 'string', 'max:150'],
            'iso2'       => ['required', 'string', 'size:2', 'unique:countries,iso2'],
            'iso3'       => ['nullable', 'string', 'size:3'],
            'phone_code' => ['nullable', 'string', 'max:10'],
            'flag'       => ['nullable', 'string', 'max:10'],
            'is_active'  => ['boolean'],
        ];
    }
}