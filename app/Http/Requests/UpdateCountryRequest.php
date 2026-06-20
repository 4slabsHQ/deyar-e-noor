<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCountryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $countryId = $this->route('country')->id;

        return [
            'name'       => ['required', 'string', 'max:150'],
            'iso2'       => ['required', 'string', 'size:2', 'unique:countries,iso2,' . $countryId],
            'iso3'       => ['nullable', 'string', 'size:3'],
            'phone_code' => ['nullable', 'string', 'max:10'],
            'flag'       => ['nullable', 'string', 'max:10'],
            'is_active'  => ['boolean'],
        ];
    }
}