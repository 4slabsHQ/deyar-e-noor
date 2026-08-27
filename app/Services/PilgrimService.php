<?php

namespace App\Services;

use App\Models\Company;
use App\Models\Pilgrim;
use App\Models\PilgrimDeletionLog;
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

    public function rebalanceFamilyAfterRemovingMember(
        Company $company,
        int $hajjYear,
        int $familyNumber,
        int $excludingPilgrimId,
    ): void {
        $members = $this->familyMembers($company->id, $hajjYear, $familyNumber)
            ->where('id', '!=', $excludingPilgrimId)
            ->values();

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

    /**
     * @return array{family_code: string|null, family_number: int|null, family_member_suffix: string|null}
     */
    public function transferPilgrimCompany(
        Pilgrim $pilgrim,
        Company $newCompany,
        int $hajjYear,
        ?int $existingFamilyNumber = null,
    ): array {
        $oldCompanyId = $pilgrim->company_id !== null ? (int) $pilgrim->company_id : null;
        $oldHajjYear = $pilgrim->hajj_year !== null ? (int) $pilgrim->hajj_year : null;
        $oldFamilyNumber = $pilgrim->family_number !== null ? (int) $pilgrim->family_number : null;
        $oldCompany = $oldCompanyId !== null ? Company::query()->find($oldCompanyId) : null;

        $releaseFromOldFamily = function () use ($pilgrim, $oldCompany, $oldCompanyId, $oldHajjYear, $oldFamilyNumber): void {
            if ($oldCompany === null || $oldCompanyId === null || $oldHajjYear === null || $oldFamilyNumber === null) {
                return;
            }

            $this->withFamilyLock($oldCompanyId, $oldHajjYear, $oldFamilyNumber, function () use ($pilgrim, $oldCompany, $oldHajjYear, $oldFamilyNumber): void {
                $this->rebalanceFamilyAfterRemovingMember($oldCompany, $oldHajjYear, $oldFamilyNumber, $pilgrim->id);
            });
        };

        if (! $newCompany->code) {
            $releaseFromOldFamily();

            return [
                'family_code' => null,
                'family_number' => null,
                'family_member_suffix' => null,
            ];
        }

        $assignNewFamily = function () use ($newCompany, $hajjYear, $existingFamilyNumber): array {
            if ($existingFamilyNumber !== null) {
                $familyData = $this->prepareAddToFamily($newCompany, $hajjYear, $existingFamilyNumber);

                if ($familyData['promote_single'] ?? false) {
                    $existingPilgrim = Pilgrim::query()->findOrFail($familyData['existing_pilgrim_id']);
                    $this->promoteSingleToA($existingPilgrim, $newCompany);
                }

                unset($familyData['promote_single'], $familyData['existing_pilgrim_id']);

                return $familyData;
            }

            return $this->prepareNewSingleFamily($newCompany, $hajjYear);
        };

        if ($oldCompany !== null && $oldCompanyId !== null && $oldHajjYear !== null && $oldFamilyNumber !== null) {
            return $this->withFamilyLock($oldCompanyId, $oldHajjYear, $oldFamilyNumber, function () use (
                $pilgrim,
                $newCompany,
                $hajjYear,
                $existingFamilyNumber,
                $oldCompany,
                $oldHajjYear,
                $oldFamilyNumber,
                $assignNewFamily,
            ): array {
                $this->rebalanceFamilyAfterRemovingMember($oldCompany, $oldHajjYear, $oldFamilyNumber, $pilgrim->id);

                return $this->withFamilyLock(
                    $newCompany->id,
                    $hajjYear,
                    $existingFamilyNumber,
                    fn (): array => $assignNewFamily(),
                );
            });
        }

        return $this->withFamilyLock(
            $newCompany->id,
            $hajjYear,
            $existingFamilyNumber,
            fn (): array => $assignNewFamily(),
        );
    }

    /** @return array<string, mixed> */
    public function previewDeletion(Pilgrim $pilgrim): array
    {
        $pilgrim->loadMissing(['company', 'package', 'podCity', 'flights']);

        return [
            'pilgrim' => [
                'id' => $pilgrim->id,
                'full_name' => $pilgrim->full_name,
                'passport_no' => $pilgrim->passport_no,
                'family_code' => $pilgrim->family_code,
                'hajj_year' => $pilgrim->hajj_year,
                'company' => $pilgrim->company?->name,
                'package' => $pilgrim->package?->registrationOptionLabel(),
                'pod_city' => $pilgrim->podCity?->name,
                'gender' => $pilgrim->gender?->label(),
            ],
            'family' => $this->previewFamilyImpact($pilgrim),
            'flights' => $pilgrim->flights
                ->sortBy('departure_date')
                ->values()
                ->map(fn ($flight): array => [
                    'id' => $flight->id,
                    'label' => $flight->reportFilterLabel(),
                    'direction' => $flight->direction->label(),
                    'flight_no' => $flight->departure_flight_no,
                    'departure_date' => $flight->departure_date?->format('d M Y'),
                ])
                ->all(),
        ];
    }

    public function deletePilgrim(Pilgrim $pilgrim): void
    {
        $this->recordDeletionLog($pilgrim);
        $pilgrim->flights()->detach();

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

    private function recordDeletionLog(Pilgrim $pilgrim): void
    {
        $pilgrim->loadMissing(['company', 'package', 'podCity']);

        PilgrimDeletionLog::query()->create([
            'pilgrim_id' => $pilgrim->id,
            'deleted_by' => auth()->id(),
            'deleted_at' => now(),
            'hajj_year' => $pilgrim->hajj_year,
            'full_name' => $pilgrim->full_name,
            'passport_no' => $pilgrim->passport_no,
            'family_code' => $pilgrim->family_code,
            'company_id' => $pilgrim->company_id,
            'company_name' => $pilgrim->company?->registrationOptionLabel() ?? $pilgrim->company?->name,
            'package_label' => $pilgrim->package?->registrationOptionLabel(),
            'pod_city_name' => $pilgrim->podCity?->name,
            'gender' => $pilgrim->gender?->label(),
            'mobile' => $pilgrim->mobile,
            'entry_date' => $pilgrim->entry_date,
        ]);
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

            $memberNames = $members
                ->map(fn (Pilgrim $pilgrim) => strtoupper($pilgrim->family_member_suffix).': '.($pilgrim->full_name ?: 'Unnamed'))
                ->implode(', ');

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
                    '%s%s — %s',
                    $baseCode,
                    $isSingle ? ' [Single]' : '',
                    $memberNames,
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
        ?string $familyMoveTo = null,
    ): array {
        if ($familyMoveTo === 'new') {
            $familyNumber = null;
            $pilgrim = null;
        } elseif ($familyMoveTo !== null && $familyMoveTo !== 'keep' && ctype_digit($familyMoveTo)) {
            $familyNumber = (int) $familyMoveTo;
        }

        if ($pilgrim !== null && $familyNumber === null && (int) $pilgrim->company_id === (int) $company->id) {
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

    /** @return array<string, mixed> */
    private function previewFamilyImpact(Pilgrim $pilgrim): array
    {
        if ($pilgrim->company_id === null || $pilgrim->hajj_year === null || $pilgrim->family_number === null) {
            return [
                'connected' => false,
                'outcome' => 'none',
                'summary' => 'This registration is not linked to a family group.',
                'other_members' => [],
                'changes' => [],
            ];
        }

        $company = Company::query()->findOrFail($pilgrim->company_id);
        $companyId = (int) $pilgrim->company_id;
        $hajjYear = (int) $pilgrim->hajj_year;
        $familyNumber = (int) $pilgrim->family_number;

        $otherMembers = $this->familyMembers($companyId, $hajjYear, $familyNumber)
            ->where('id', '!=', $pilgrim->id)
            ->values();

        $otherMemberRows = $otherMembers
            ->map(fn (Pilgrim $member): array => [
                'id' => $member->id,
                'full_name' => $member->full_name,
                'family_code' => $member->family_code,
            ])
            ->all();

        if ($otherMembers->isEmpty()) {
            return [
                'connected' => true,
                'family_number' => $familyNumber,
                'outcome' => 'freed',
                'summary' => sprintf(
                    'Family number %02d will be released and can be reused for new registrations.',
                    $familyNumber,
                ),
                'other_members' => [],
                'changes' => [],
            ];
        }

        if ($otherMembers->count() === 1) {
            /** @var Pilgrim $member */
            $member = $otherMembers->first();
            $newCode = $this->formatFamilyCode($company, $familyNumber, 'S');

            return [
                'connected' => true,
                'family_number' => $familyNumber,
                'outcome' => 'revert_to_single',
                'summary' => 'The remaining family member will become a single registration.',
                'other_members' => $otherMemberRows,
                'changes' => [[
                    'full_name' => $member->full_name,
                    'current_family_code' => $member->family_code,
                    'new_family_code' => $newCode,
                    'will_change' => $member->family_code !== $newCode,
                ]],
            ];
        }

        $changes = $otherMembers
            ->values()
            ->map(function (Pilgrim $member, int $index) use ($company, $familyNumber): array {
                $suffix = $this->suffixForMemberIndex($index) ?? '?';
                $newCode = $this->formatFamilyCode($company, $familyNumber, $suffix);

                return [
                    'full_name' => $member->full_name,
                    'current_family_code' => $member->family_code,
                    'new_family_code' => $newCode,
                    'will_change' => $member->family_code !== $newCode,
                ];
            })
            ->all();

        return [
            'connected' => true,
            'family_number' => $familyNumber,
            'outcome' => 'rebalance',
            'summary' => sprintf(
                '%d remaining family members will be renumbered to close the gap.',
                $otherMembers->count(),
            ),
            'other_members' => $otherMemberRows,
            'changes' => $changes,
        ];
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
