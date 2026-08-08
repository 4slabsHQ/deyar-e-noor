<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAirportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $airportId = $this->route('airport')->id;

        return [
            'name' => ['required', 'string', 'max:255'],
            'code' => [
                'required',
                'string',
                'max:10',
                Rule::unique('airports', 'code')
                    ->where(fn ($query) => $query->where('city_id', $this->input('city_id')))
                    ->ignore($airportId),
            ],
            'city_id' => ['required', 'exists:cities,id'],
            'is_active' => ['boolean'],
        ];
    }
}
