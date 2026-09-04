<?php

namespace App\Http\Requests;

use App\Enums\BloodGroup;
use App\Enums\Gender;
use App\Enums\PackageDuration;
use App\Models\Company;
use App\Models\FormOwner;
use App\Models\Package;
use App\Models\Pilgrim;
use App\Rules\FourDigitYearDate;
use App\Services\HajjSeasonService;
use App\Support\SeasonValidation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StorePilgrimRequest extends FormRequest
{
    /** @var list<string> */
    private const NULLABLE_FIELDS = [
        'hajj_year',
        'entry_date',
        'form_owner_id',
        'company_id',
        'maktab_category_id',
        'package_id',
        'care_off_id',
        'pod_city_id',
        'room_type_id',
        'gender',
        'surname',
        'given_name',
        'father_husband_name',
        'passport_no',
        'date_of_birth',
        'birth_place',
        'passport_expiry',
        'address',
        'mobile',
        'cnic',
        'blood_group',
        'mehram_name',
        'mehram_relation_id',
        'waris_name',
        'waris_cnic',
        'waris_relation_id',
        'waris_mobile',
        'existing_family_number',
        'comments',
    ];

    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return $this->baseRules();
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('passport_no')) {
            $passport = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', (string) $this->input('passport_no')) ?? '');

            $this->merge(['passport_no' => $passport !== '' ? $passport : null]);
        }

        if ($this->has('cnic')) {
            $this->merge(['cnic' => $this->nullableNormalizedCnic((string) $this->input('cnic'))]);
        }

        if ($this->has('waris_cnic')) {
            $this->merge(['waris_cnic' => $this->nullableNormalizedCnic((string) $this->input('waris_cnic'))]);
        }

        if ($this->filled('family_member_suffix')) {
            $this->merge(['family_member_suffix' => strtoupper((string) $this->input('family_member_suffix'))]);
        }

        foreach (self::NULLABLE_FIELDS as $field) {
            if ($this->has($field) && $this->input($field) === '') {
                $this->merge([$field => null]);
            }
        }

        $seasonService = app(HajjSeasonService::class);
        /** @var Pilgrim|null $pilgrim */
        $pilgrim = $this->route('pilgrim');

        if ($pilgrim) {
            $this->merge([
                'hajj_year' => $pilgrim->hajj_year ?? $seasonService->activeYear(),
                'entry_date' => $pilgrim->entry_date?->toDateString() ?? now()->toDateString(),
            ]);
        } else {
            $this->merge([
                'hajj_year' => $seasonService->activeYear(),
                'entry_date' => now()->toDateString(),
            ]);
        }

        $package = Package::query()->find($this->input('package_id'));

        $this->merge([
            'qurbani_included' => $this->has('qurbani_included')
                ? $this->boolean('qurbani_included')
                : (bool) $package?->qurbani_included,
            'days' => is_numeric($this->input('days'))
                ? (int) $this->input('days')
                : $package?->days,
            'duration' => $this->filled('duration')
                ? $this->input('duration')
                : $package?->duration?->value,
        ]);
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'passport_no.regex' => 'Passport must start with 2 letters followed by 7 numbers (e.g. AB1234567).',
            'passport_no.unique' => 'This passport is already registered for the selected Hajj year.',
            'cnic.regex' => 'CNIC must be in the format 12345-1234567-1.',
            'cnic.unique' => 'This CNIC is already registered for the selected Hajj year.',
            'waris_cnic.regex' => 'Waris CNIC must be in the format 12345-1234567-1.',
            'family_member_suffix.regex' => 'Family member suffix must be a single letter (A–Z).',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $this->validateUniqueFamilyMember($validator);
            $this->validateCompanyQuota($validator);
            $this->validateCompanyCodeForAssignment($validator);
            $this->validatePackageLimit($validator);
            $this->validateFormOwnerLimit($validator);
        });
    }

    protected function validateCompanyQuota(Validator $validator): void
    {
        $companyId = $this->input('company_id');
        $hajjYear = $this->input('hajj_year');

        if (! $companyId || ! $hajjYear) {
            return;
        }

        $company = Company::query()->find($companyId);

        if (! $company || $company->quota === null) {
            return;
        }

        $excludingPilgrimId = $this->route('pilgrim')?->id;

        if ($company->hasQuotaForYear((int) $hajjYear, $excludingPilgrimId)) {
            return;
        }

        $registered = $company->registeredPilgrimCountForYear((int) $hajjYear, $excludingPilgrimId);

        $validator->errors()->add(
            'company_id',
            "Company quota reached for {$company->name} (Hajj {$hajjYear}: {$registered}/{$company->quota})."
        );
    }

    protected function validateCompanyCodeForAssignment(Validator $validator): void
    {
        if ($validator->errors()->isNotEmpty()) {
            return;
        }

        $companyId = $this->input('company_id');

        if (! $companyId) {
            return;
        }

        /** @var Pilgrim|null $pilgrim */
        $pilgrim = $this->route('pilgrim');
        $company = Company::query()->find($companyId);

        if (! $company || filled($company->code)) {
            return;
        }

        if ($pilgrim !== null && (int) $pilgrim->company_id === (int) $companyId) {
            return;
        }

        if ($this->filled('existing_family_number')) {
            $validator->errors()->add(
                'company_id',
                'Selected company has no code configured. Family linking requires a company code.'
            );

            return;
        }

        if ($pilgrim === null) {
            return;
        }

        $validator->errors()->add(
            'company_id',
            'Selected company has no code configured.'
        );
    }

    protected function validatePackageLimit(Validator $validator): void
    {
        $packageId = $this->input('package_id');
        $hajjYear = $this->input('hajj_year');

        if (! $packageId || ! $hajjYear) {
            return;
        }

        $package = Package::query()->find($packageId);

        if (! $package || $package->limit === null) {
            return;
        }

        $excludingPilgrimId = $this->route('pilgrim')?->id;

        if ($package->hasLimitForYear((int) $hajjYear, $excludingPilgrimId)) {
            return;
        }

        $registered = $package->registeredPilgrimCountForYear((int) $hajjYear, $excludingPilgrimId);

        $validator->errors()->add(
            'package_id',
            "Package limit reached for {$package->name} (Hajj {$hajjYear}: {$registered}/{$package->limit})."
        );
    }

    protected function validateFormOwnerLimit(Validator $validator): void
    {
        $formOwnerId = $this->input('form_owner_id');
        $hajjYear = $this->input('hajj_year');

        if (! $formOwnerId || ! $hajjYear) {
            return;
        }

        $formOwner = FormOwner::query()->find($formOwnerId);

        if (! $formOwner || $formOwner->limit === null) {
            return;
        }

        $excludingPilgrimId = $this->route('pilgrim')?->id;

        if ($formOwner->hasLimitForYear((int) $hajjYear, $excludingPilgrimId)) {
            return;
        }

        $registered = $formOwner->registeredPilgrimCountForYear((int) $hajjYear, $excludingPilgrimId);

        $validator->errors()->add(
            'form_owner_id',
            "Form owner limit reached for {$formOwner->name} (Hajj {$hajjYear}: {$registered}/{$formOwner->limit})."
        );
    }

    protected function validateUniqueFamilyMember(Validator $validator): void
    {
        if ($validator->errors()->isNotEmpty()) {
            return;
        }

        $familyCode = (string) $this->input('family_code', '');
        $companyId = (int) $this->input('company_id');
        $hajjYear = (int) $this->input('hajj_year');

        if ($familyCode === '' || $companyId === 0 || $hajjYear === 0) {
            return;
        }

        if (! preg_match('/^[A-Z0-9]+-(\d+)-([A-Z])$/i', $familyCode, $matches)) {
            return;
        }

        $familyNumber = (int) $matches[1];
        $suffix = strtoupper($matches[2]);
        $pilgrimId = $this->route('pilgrim')?->id;

        if (Pilgrim::query()
            ->where('company_id', $companyId)
            ->where('hajj_year', $hajjYear)
            ->where('family_number', $familyNumber)
            ->where('family_member_suffix', $suffix)
            ->when($pilgrimId, fn ($query) => $query->where('id', '!=', $pilgrimId))
            ->exists()) {
            $validator->errors()->add(
                'family_member_suffix',
                'This family member suffix is already registered for the selected family.'
            );
        }
    }

    protected function nullableNormalizedCnic(string $value): ?string
    {
        if (trim($value) === '') {
            return null;
        }

        return $this->normalizeCnic($value);
    }

    protected function normalizeCnic(string $value): string
    {
        $digits = preg_replace('/\D/', '', $value) ?? '';

        if (strlen($digits) !== 13) {
            return $value;
        }

        return substr($digits, 0, 5).'-'.substr($digits, 5, 7).'-'.substr($digits, 12, 1);
    }

    /** @return array<string, mixed> */
    protected function baseRules(?int $pilgrimId = null): array
    {
        return [
            'hajj_year' => ['nullable', 'integer', 'min:2000', 'max:2100'],
            'entry_date' => ['nullable', new FourDigitYearDate],
            'form_owner_id' => ['nullable', SeasonValidation::existsActive('form_owners')],
            'company_id' => ['nullable', SeasonValidation::existsActive('companies')],
            'maktab_category_id' => ['nullable', SeasonValidation::existsActive('maktab_categories')],
            'package_id' => ['nullable', SeasonValidation::existsActive('packages')],
            'qurbani_included' => ['boolean'],
            'days' => ['nullable', 'integer', 'min:0'],
            'duration' => ['nullable', Rule::enum(PackageDuration::class)],
            'care_off_id' => ['nullable', SeasonValidation::existsActive('care_offs')],
            'pod_city_id' => ['nullable', Rule::exists('cities', 'id')],
            'room_type_id' => ['nullable', SeasonValidation::existsActive('room_types')],
            'gender' => ['nullable', Rule::enum(Gender::class)],
            'surname' => ['nullable', 'string', 'max:100'],
            'given_name' => ['nullable', 'string', 'max:100'],
            'father_husband_name' => ['nullable', 'string', 'max:150'],
            'passport_no' => [
                'nullable',
                'string',
                'regex:/^[A-Z]{2}\d{7}$/',
                Rule::unique('pilgrims', 'passport_no')
                    ->where(fn ($query) => $query->where('hajj_year', $this->input('hajj_year')))
                    ->ignore($pilgrimId),
            ],
            'date_of_birth' => ['nullable', new FourDigitYearDate, 'before:today'],
            'birth_place' => ['nullable', 'string', 'max:150'],
            'passport_expiry' => ['nullable', new FourDigitYearDate, 'after:today'],
            'address' => ['nullable', 'string', 'max:500'],
            'mobile' => ['nullable', 'string', 'max:20'],
            'cnic' => [
                'nullable',
                'string',
                'regex:/^\d{5}-\d{7}-\d$/',
                Rule::unique('pilgrims', 'cnic')
                    ->where(fn ($query) => $query->where('hajj_year', $this->input('hajj_year')))
                    ->ignore($pilgrimId),
            ],
            'blood_group' => ['nullable', Rule::enum(BloodGroup::class)],
            'mehram_name' => ['nullable', 'string', 'max:150'],
            'mehram_relation_id' => ['nullable', SeasonValidation::existsActive('mehram_relations')],
            'waris_name' => ['nullable', 'string', 'max:150'],
            'waris_cnic' => ['nullable', 'string', 'regex:/^\d{5}-\d{7}-\d$/'],
            'waris_relation_id' => ['nullable', SeasonValidation::existsActive('waris_relations')],
            'waris_mobile' => ['nullable', 'string', 'max:20'],
            'existing_family_number' => [
                'nullable',
                'integer',
                'min:1',
                Rule::exists('pilgrims', 'family_number')
                    ->where(fn ($query) => $query
                        ->where('company_id', $this->input('company_id'))
                        ->where('hajj_year', $this->input('hajj_year'))),
            ],
            'family_move_to' => ['nullable', 'string', 'max:20'],
            'family_code' => ['nullable', 'string', 'max:50'],
            'family_member_suffix' => ['nullable', 'string', 'max:2', 'regex:/^[A-Z]$/i'],
            'photo' => ['nullable', 'file', 'max:5120'],
            'remove_photo' => ['nullable', 'boolean'],
            'passport' => ['nullable', 'file', 'max:5120'],
            'remove_passport' => ['nullable', 'boolean'],
            'visa' => ['nullable', 'file', 'max:5120'],
            'remove_visa' => ['nullable', 'boolean'],
            'ticket' => ['nullable', 'file', 'max:5120'],
            'remove_ticket' => ['nullable', 'boolean'],
            'comments' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
