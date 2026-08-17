<?php

namespace App\Services;

use App\Models\Company;
use App\Models\Pilgrim;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
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

    public function formatFamilyCode(Company $company, int $familyNumber, string $suffix): string
    {
        return sprintf(
            '%s-%02d-%s',
            strtoupper((string) $company->code),
            $familyNumber,
            strtoupper($suffix)
        );
    }

    /**
     * @param  callable(): mixed  $callback
     */
    public function withFamilyLock(int $companyId, int $hajjYear, ?int $familyNumber, callable $callback): mixed
    {
        $lockKey = $familyNumber === null
            ? "pilgrim-family:{$companyId}:{$hajjYear}"
            : "pilgrim-family:{$companyId}:{$hajjYear}:{$familyNumber}";

        return Cache::lock($lockKey, 10)->block(5, function () use ($callback, $companyId, $hajjYear, $familyNumber): mixed {
            return DB::transaction(function () use ($callback, $companyId, $hajjYear, $familyNumber): mixed {
                if ($familyNumber !== null) {
                    $this->lockFamilyRows($companyId, $hajjYear, $familyNumber);
                } else {
                    Pilgrim::query()
                        ->where('company_id', $companyId)
                        ->where('hajj_year', $hajjYear)
                        ->lockForUpdate()
                        ->pluck('id');
                }

                return $callback();
            });
        });
    }

    public function nextFamilyNumber(int $companyId, int $hajjYear): int
    {
        $inUse = Pilgrim::query()
            ->where('company_id', $companyId)
            ->where('hajj_year', $hajjYear)
            ->distinct()
            ->orderBy('family_number')
            ->pluck('family_number')
            ->map(fn ($number) => (int) $number)
            ->all();

        $candidate = 1;

        foreach ($inUse as $usedNumber) {
            if ($usedNumber > $candidate) {
                break;
            }

            if ($usedNumber === $candidate) {
                $candidate++;
            }
        }

        return $candidate;
    }

    /** @return Collection<int, Pilgrim> */
    public function familyMembers(int $companyId, int $hajjYear, int $familyNumber): Collection
    {
        return Pilgrim::query()
            ->where('company_id', $companyId)
            ->where('hajj_year', $hajjYear)
            ->where('family_number', $familyNumber)
            ->orderBy('created_at')
            ->orderBy('id')
            ->get();
    }

    public function isSingleFamily(int $companyId, int $hajjYear, int $familyNumber): bool
    {
        $members = $this->familyMembers($companyId, $hajjYear, $familyNumber);

        return $members->count() === 1
            && strtoupper($members->first()->family_member_suffix) === 'S';
    }

    /**
     * @return array{suffix: string, promote_single: bool, existing_pilgrim_id: int|null}
     */
    public function resolveNewMemberAssignment(int $companyId, int $hajjYear, int $familyNumber): array
    {
        $members = $this->familyMembers($companyId, $hajjYear, $familyNumber);

        if ($members->isEmpty()) {
            throw new RuntimeException('Family not found.');
        }

        if ($this->isSingleFamily($companyId, $hajjYear, $familyNumber)) {
            return [
                'suffix' => 'B',
                'promote_single' => true,
                'existing_pilgrim_id' => $members->first()->id,
            ];
        }

        $suffix = $this->suffixForMemberIndex($members->count());

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

    public function rebalanceFamily(Company $company, int $hajjYear, int $familyNumber): void
    {
        $members = $this->familyMembers($company->id, $hajjYear, $familyNumber);

        if ($members->isEmpty()) {
            return;
        }

        if ($members->count() === 1) {
            $member = $members->first();
            $member->update([
                'family_member_suffix' => 'S',
                'family_code' => $this->formatFamilyCode($company, $familyNumber, 'S'),
                'updated_by' => auth()->id(),
            ]);

            return;
        }

        foreach ($members->values() as $index => $member) {
            $suffix = $this->suffixForMemberIndex($index);

            if ($suffix === null) {
                throw new RuntimeException('Unable to rebalance family suffixes.');
            }

            $member->update([
                'family_member_suffix' => $suffix,
                'family_code' => $this->formatFamilyCode($company, $familyNumber, $suffix),
                'updated_by' => auth()->id(),
            ]);
        }
    }

    public function deletePilgrim(Pilgrim $pilgrim): void
    {
        if ($pilgrim->company_id === null || $pilgrim->hajj_year === null || $pilgrim->family_number === null) {
            $pilgrim->delete();

            return;
        }

        $company = Company::query()->findOrFail($pilgrim->company_id);
        $companyId = (int) $pilgrim->company_id;
        $hajjYear = (int) $pilgrim->hajj_year;
        $familyNumber = (int) $pilgrim->family_number;

        $this->withFamilyLock($companyId, $hajjYear, $familyNumber, function () use ($pilgrim, $company, $hajjYear, $familyNumber): void {
            $pilgrim->delete();
            $this->rebalanceFamily($company, $hajjYear, $familyNumber);
        });
    }

    /**
     * @return array<int, array{family_number: int, family_code: string, is_single: bool, members: array<int, array{suffix: string, name: string, family_code: string}>, used_suffixes: array<int, string>, label: string}>
     */
    public function existingFamiliesForCompany(int $companyId, int $hajjYear): array
    {
        /** @var Collection<int, Collection<int, Pilgrim>> $grouped */
        $grouped = Pilgrim::query()
            ->where('company_id', $companyId)
            ->where('hajj_year', $hajjYear)
            ->orderBy('family_number')
            ->orderBy('family_member_suffix')
            ->get()
            ->groupBy('family_number');

        return $grouped->map(function (Collection $members, int|string $familyNumber) use ($companyId, $hajjYear) {
            $members = $members->values();
            $familyNumber = (int) $familyNumber;

            /** @var Pilgrim $first */
            $first = $members->first();
            $isSingle = $this->isSingleFamily($companyId, $hajjYear, $familyNumber);
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
    public function prepareNewSingleFamily(Company $company, int $hajjYear): array
    {
        $familyNumber = $this->nextFamilyNumber($company->id, $hajjYear);

        return [
            'family_code' => $this->formatFamilyCode($company, $familyNumber, 'S'),
            'family_number' => $familyNumber,
            'family_member_suffix' => 'S',
        ];
    }

    /**
     * @return array{family_code: string, family_number: int, family_member_suffix: string, promote_single: bool, existing_pilgrim_id: int|null}
     */
    public function prepareAddToFamily(Company $company, int $hajjYear, int $familyNumber): array
    {
        $assignment = $this->resolveNewMemberAssignment($company->id, $hajjYear, $familyNumber);

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
        int $hajjYear,
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
            $assignment = $this->resolveNewMemberAssignment($company->id, $hajjYear, $familyNumber);

            return [
                'family_code' => $this->formatFamilyCode($company, $familyNumber, $assignment['suffix']),
                'family_number' => $familyNumber,
                'suffix' => $assignment['suffix'],
                'promote_single' => $assignment['promote_single'],
            ];
        }

        $nextFamilyNumber = $this->nextFamilyNumber($company->id, $hajjYear);

        return [
            'family_code' => $this->formatFamilyCode($company, $nextFamilyNumber, 'S'),
            'family_number' => $nextFamilyNumber,
            'suffix' => 'S',
            'promote_single' => false,
        ];
    }

    /** @return array{family_code: string, family_number: int, family_member_suffix: string} */
    public function prepareFamilyDataForEdit(Company $company, int $hajjYear, string $familyCode, string $suffix): array
    {
        if (preg_match('/^([A-Z0-9]+)-(\d+)-([A-Z])$/i', $familyCode, $matches)) {
            return [
                'family_code' => strtoupper($familyCode),
                'family_number' => (int) $matches[2],
                'family_member_suffix' => strtoupper($suffix ?: $matches[3]),
            ];
        }

        return $this->prepareNewSingleFamily($company, $hajjYear);
    }

    private function lockFamilyRows(int $companyId, int $hajjYear, int $familyNumber): void
    {
        Pilgrim::query()
            ->where('company_id', $companyId)
            ->where('hajj_year', $hajjYear)
            ->where('family_number', $familyNumber)
            ->lockForUpdate()
            ->pluck('id');
    }

    private function suffixForMemberIndex(int $index): ?string
    {
        if ($index < 0 || $index > 25) {
            return null;
        }

        return chr(ord('A') + $index);
    }
}
