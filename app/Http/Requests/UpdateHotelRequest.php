<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateHotelRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // gated by route middleware
    }

    public function rules(): array
    {
        return [
            'name'            => ['required', 'string', 'max:255'],
            'code'            => ['nullable', 'string', 'max:255', Rule::unique('hotels', 'code')->ignore($this->route('hotel')->id)],
            'star_rating'     => ['nullable', Rule::in(['1', '2', '3', '4', '5'])],
            'address'         => ['nullable', 'string'],
            'country_id'      => ['nullable', 'exists:countries,id'],
            'city_id'         => ['nullable', 'exists:cities,id'],
            'contact_person'  => ['nullable', 'string', 'max:255'],
            'phone'           => ['nullable', 'string', 'max:255'],
            'email'           => ['nullable', 'email', 'max:255'],
            'website'         => ['nullable', 'url', 'max:255'],
            'is_active'       => ['boolean'],
            'notes'           => ['nullable', 'string'],
        ];
    }
}