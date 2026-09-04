<?php

namespace App\Http\Requests;

use App\Enums\AccommodationPlanType;
use App\Support\SeasonValidation;
use Illuminate\Validation\Rule;

class UpdateAccommodationPlanRequest extends StoreAccommodationPlanRequest
{
    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', SeasonValidation::unique('accommodation_plans', 'name', $this->route('accommodation_plan'))],
            'type' => ['required', Rule::enum(AccommodationPlanType::class)],
            'is_active' => ['boolean'],
            'slots' => ['required', 'array'],
            'slots.*.property_id' => ['nullable', 'integer'],
            'slots.*.property_akad_id' => ['nullable', 'integer', 'exists:property_akads,id'],
        ];
    }
}
