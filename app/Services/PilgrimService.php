<?php

namespace App\Services;

use App\Models\Company;
use App\Models\Pilgrim;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use RuntimeException;

class PilgrimService
{
    public function buildFullName(string $surname, string $givenName): string
    {
        return trim($givenName.' '.$surname);
    }

    public function calculateAge(Carbon $dateOfBirth, int $hajjYear): int
    {
        return max(0, $hajjYear - $dateOfBirth->year);
    }

    public function nextFamilyNumber(int $companyId): int
    {
        $max = Pilgrim::query()
            ->where('company_id', $companyId)
            ->max('family_number');

        return ((int) $max) + 1;
    }

    public function formatFamilyCode(Company $company, int $familyNumber, string $suffix): string
    {
        return sprintf(
            '%s-%d-%s',
            strtoupper((string) $company->code),
            $familyNumber,
            strtoupper($suffix)
        );
    }

    /** @return Collection<int, Pilgrim> */
    public function familyMembers(int $companyId, int $familyNumber): Collection
    {
        return Pilgrim::query()
            ->where('company_id', $companyId)
            ->where('family_number', $familyNumber)
            ->orderBy('family_member_suffix')
            ->get();
    }

    public function isSingleFamily(int $companyId, int $familyNumber): bool
    {
        $members = $this->familyMembers($companyId, $familyNumber);

        return $members->count() === 1
            && strtoupper($members->first()->family_member_suffix) === 'S';
    }

    public function nextLetterSuffix(array $usedSuffixes): ?string
    {
        $used = array_map('strtoupper', $usedSuffixes);

        foreach (range('A', 'Z') as $letter) {
            if (! in_array($letter, $used, true)) {
                return $letter;
            }
        }

        return null;
    }

    /**
     * @return array{suffix: string, promote_single: bool, existing_pilgrim_id: int|null}
     */
    public function resolveNewMemberAssignment(int $companyId, int $familyNumber): array
    {
        $members = $this->familyMembers($companyId, $familyNumber);

        if ($members->isEmpty()) {
            throw new RuntimeException('Family not found.');
        }

        if ($this->isSingleFamily($companyId, $familyNumber)) {
            return [
                'suffix' => 'B',
                'promote_single' => true,
                'existing_pilgrim_id' => $members->first()->id,
            ];
        }

        $usedSuffixes = $members
            ->pluck('family_member_suffix')
            ->map(fn (string $suffix) => strtoupper($suffix))
            ->all();

        $suffix = $this->nextLetterSuffix($usedSuffixes);

        if ($suffix === null) {
            throw new RuntimeException('No family member suffix available.');
        }

        return [
            'suffix' => $suffix,
            'promote_single' => false,
            'existing_pilgrim_id' => null,
        ];
    }

    public function promoteSingleToA(Pilgrim $pilgrim, Company $company): void
    {
        $pilgrim->update([
            'family_member_suffix' => 'A',
            'family_code' => $this->formatFamilyCode($company, $pilgrim->family_number, 'A'),
            'updated_by' => auth()->id(),
        ]);
    }

    /**
     * @return array<int, array{family_number: int, family_code: string, is_single: bool, members: array<int, array{suffix: string, name: string, family_code: string}>, used_suffixes: array<int, string>, label: string}>
     */
    public function existingFamiliesForCompany(int $companyId): array
    {
        /** @var Collection<int, Collection<int, Pilgrim>> $grouped */
        $grouped = Pilgrim::query()
            ->where('company_id', $companyId)
            ->orderBy('family_number')
            ->orderBy('family_member_suffix')
            ->get()
            ->groupBy('family_number');

        return $grouped->map(function (Collection $members, int|string $familyNumber) use ($companyId) {
            $members = $members->values();
            $familyNumber = (int) $familyNumber;

            /** @var Pilgrim $first */
            $first = $members->first();
            $isSingle = $this->isSingleFamily($companyId, $familyNumber);
            $baseCode = preg_replace('/-[A-Z]$/', '', $first->family_code) ?: $first->family_code;

            $usedSuffixes = $members
                ->pluck('family_member_suffix')
                ->map(fn (string $suffix) => strtoupper($suffix))
                ->unique()
                ->values()
                ->all();

            $memberNames = $members->pluck('full_name')->take(3)->implode(', ');
            $suffixList = implode(', ', $usedSuffixes);

            return [
                'family_number' => $familyNumber,
                'family_code' => $baseCode,
                'is_single' => $isSingle,
                'members' => $members->map(fn (Pilgrim $pilgrim) => [
                    'suffix' => strtoupper($pilgrim->family_member_suffix),
                    'name' => $pilgrim->full_name,
                    'family_code' => $pilgrim->family_code,
                ])->all(),
                'used_suffixes' => $usedSuffixes,
                'label' => sprintf(
                    '%s%s — %s (%s)',
                    $baseCode,
                    $isSingle ? ' [Single]' : '',
                    $memberNames,
                    $suffixList
                ),
            ];
        })->values()->all();
    }

    /**
     * @return array{family_code: string, family_number: int, family_member_suffix: string}
     */
    public function prepareNewSingleFamily(Company $company): array
    {
        $familyNumber = $this->nextFamilyNumber($company->id);

        return [
            'family_code' => $this->formatFamilyCode($company, $familyNumber, 'S'),
            'family_number' => $familyNumber,
            'family_member_suffix' => 'S',
        ];
    }

    /**
     * @return array{family_code: string, family_number: int, family_member_suffix: string, promote_single: bool, existing_pilgrim_id: int|null}
     */
    public function prepareAddToFamily(Company $company, int $familyNumber): array
    {
        $assignment = $this->resolveNewMemberAssignment($company->id, $familyNumber);

        return [
            'family_code' => $this->formatFamilyCode($company, $familyNumber, $assignment['suffix']),
            'family_number' => $familyNumber,
            'family_member_suffix' => $assignment['suffix'],
            'promote_single' => $assignment['promote_single'],
            'existing_pilgrim_id' => $assignment['existing_pilgrim_id'],
        ];
    }

    /**
     * @return array{family_code: string, family_number: int, suffix: string, promote_single: bool}
     */
    public function previewFamilyCode(
        Company $company,
        ?Pilgrim $pilgrim = null,
        ?int $familyNumber = null,
    ): array {
        if ($pilgrim !== null && $familyNumber === null) {
            return [
                'family_code' => $pilgrim->family_code,
                'family_number' => $pilgrim->family_number,
                'suffix' => strtoupper($pilgrim->family_member_suffix),
                'promote_single' => false,
            ];
        }

        if ($familyNumber !== null) {
            $assignment = $this->resolveNewMemberAssignment($company->id, $familyNumber);

            return [
                'family_code' => $this->formatFamilyCode($company, $familyNumber, $assignment['suffix']),
                'family_number' => $familyNumber,
                'suffix' => $assignment['suffix'],
                'promote_single' => $assignment['promote_single'],
            ];
        }

        $familyNumber = $pilgrim !== null && $pilgrim->company_id === $company->id
            ? $pilgrim->family_number
            : $this->nextFamilyNumber($company->id);

        return [
            'family_code' => $this->formatFamilyCode($company, $familyNumber, 'S'),
            'family_number' => $familyNumber,
            'suffix' => 'S',
            'promote_single' => false,
        ];
    }

    /** @return array{family_code: string, family_number: int, family_member_suffix: string} */
    public function prepareFamilyDataForEdit(Company $company, string $familyCode, string $suffix): array
    {
        if (preg_match('/^([A-Z0-9]+)-(\d+)-([A-Z])$/i', $familyCode, $matches)) {
            return [
                'family_code' => strtoupper($familyCode),
                'family_number' => (int) $matches[2],
                'family_member_suffix' => strtoupper($suffix ?: $matches[3]),
            ];
        }

        return $this->prepareNewSingleFamily($company);
    }
}
