<?php

namespace App\Http\Requests;

use App\Enums\RoutePointType;
use App\Support\SeasonValidation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRouteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', SeasonValidation::unique('routes', 'name', $this->route('route'))],
            'is_active' => ['boolean'],
            'steps' => ['required', 'array', 'min:2'],
            'steps.*.point_type' => ['required', Rule::enum(RoutePointType::class)],
            'steps.*.location' => ['required', 'string', 'max:255'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_active' => $this->boolean('is_active'),
        ]);
    }
}
