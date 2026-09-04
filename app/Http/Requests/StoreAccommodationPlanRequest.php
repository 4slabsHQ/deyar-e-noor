<?php

namespace App\Http\Requests;

use App\Enums\AccommodationPlanType;
use App\Models\Property;
use App\Models\PropertyAkad;
use App\Support\SeasonValidation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreAccommodationPlanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', SeasonValidation::unique('accommodation_plans', 'name')],
            'type' => ['required', Rule::enum(AccommodationPlanType::class)],
            'is_active' => ['boolean'],
            'slots' => ['required', 'array'],
            'slots.*.property_akad_id' => ['nullable', 'integer', 'exists:property_akads,id'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            $this->validateRequiredSlots($validator);
            $this->validateSlotProperties($validator);
            $this->validateAkadsBelongToProperty($validator);
        });
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_active' => $this->boolean('is_active'),
        ]);
    }

    private function validateRequiredSlots(Validator $validator): void
    {
        $type = AccommodationPlanType::tryFrom((string) $this->input('type'));

        if ($type === null) {
            return;
        }

        foreach ($type->slots() as $slot) {
            $row = $this->input('slots.'.$slot->value);

            if ($row === null || empty($row['property_id'])) {
                $validator->errors()->add('slots.'.$slot->value.'.property_id', 'Select a property for '.$slot->label().'.');
            }
        }
    }

    private function validateSlotProperties(Validator $validator): void
    {
        $type = AccommodationPlanType::tryFrom((string) $this->input('type'));

        if ($type === null) {
            return;
        }

        foreach ($type->slots() as $slot) {
            $row = $this->input('slots.'.$slot->value);

            if ($row === null || empty($row['property_id'])) {
                continue;
            }

            $property = Property::query()->find((int) $row['property_id']);

            if ($property === null) {
                $validator->errors()->add('slots.'.$slot->value.'.property_id', 'Selected property was not found.');

                continue;
            }

            if ($property->city !== $slot->propertyCity() || $property->type !== $slot->propertyType()) {
                $validator->errors()->add(
                    'slots.'.$slot->value.'.property_id',
                    'Selected property does not match the '.$slot->label().' slot.',
                );
            }
        }
    }

    private function validateAkadsBelongToProperty(Validator $validator): void
    {
        foreach ($this->input('slots', []) as $slotKey => $row) {
            $akadId = $row['property_akad_id'] ?? null;

            if ($akadId === null || $akadId === '') {
                continue;
            }

            $akad = PropertyAkad::query()->find((int) $akadId);

            if ($akad === null) {
                continue;
            }

            if ((int) $akad->property_id !== (int) ($row['property_id'] ?? 0)) {
                $validator->errors()->add(
                    'slots.'.$slotKey.'.property_akad_id',
                    'Selected akad does not belong to the chosen property.',
                );
            }
        }
    }
}
