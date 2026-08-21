<?php

namespace App\Http\Requests;

use App\Models\Pilgrim;
use Illuminate\Validation\Validator;

class UpdatePilgrimRequest extends StorePilgrimRequest
{
    /** @return array<string, mixed> */
    public function rules(): array
    {
        /** @var Pilgrim $pilgrim */
        $pilgrim = $this->route('pilgrim');

        return $this->baseRules($pilgrim->id);
    }

    public function withValidator(Validator $validator): void
    {
        parent::withValidator($validator);

        $validator->after(function (Validator $validator): void {
            $this->validateFamilyMove($validator);
        });
    }

    protected function validateFamilyMove(Validator $validator): void
    {
        if ($validator->errors()->isNotEmpty()) {
            return;
        }

        $familyMoveTo = $this->input('family_move_to', 'keep');

        if ($familyMoveTo === 'keep' || $familyMoveTo === 'new' || $familyMoveTo === null || $familyMoveTo === '') {
            return;
        }

        if (! ctype_digit((string) $familyMoveTo)) {
            $validator->errors()->add('family_move_to', 'Invalid family selection.');

            return;
        }

        $companyId = $this->input('company_id');
        $hajjYear = $this->input('hajj_year');

        if (! $companyId || ! $hajjYear) {
            return;
        }

        $familyExists = Pilgrim::query()
            ->where('company_id', $companyId)
            ->where('hajj_year', $hajjYear)
            ->where('family_number', (int) $familyMoveTo)
            ->exists();

        if (! $familyExists) {
            $validator->errors()->add('family_move_to', 'Selected family was not found for this company and Hajj year.');
        }
    }
}
