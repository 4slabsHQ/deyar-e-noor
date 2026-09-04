<?php

namespace App\Http\Requests;

use App\Enums\RoutePointType;
use App\Support\SeasonValidation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreRouteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', SeasonValidation::unique('routes', 'name')],
            'is_active' => ['boolean'],
            'steps' => ['required', 'array', 'min:2'],
            'steps.*.point_type' => ['required', Rule::enum(RoutePointType::class)],
            'steps.*.airport_id' => ['nullable', 'integer', 'exists:airports,id'],
            'steps.*.city_id' => ['nullable', 'integer', 'exists:cities,id'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            foreach ($this->input('steps', []) as $index => $step) {
                $pointType = RoutePointType::tryFrom((string) ($step['point_type'] ?? ''));

                if ($pointType === RoutePointType::Airport && empty($step['airport_id'])) {
                    $validator->errors()->add("steps.$index.airport_id", 'Select an airport.');
                }

                if ($pointType === RoutePointType::City && empty($step['city_id'])) {
                    $validator->errors()->add("steps.$index.city_id", 'Select a city.');
                }
            }
        });
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_active' => $this->boolean('is_active'),
        ]);
    }
}
