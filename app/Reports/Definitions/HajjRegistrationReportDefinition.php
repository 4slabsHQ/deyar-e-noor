<?php

namespace App\Reports\Definitions;

use App\Models\CareOff;
use App\Models\City;
use App\Models\Company;
use App\Models\FormOwner;
use App\Models\HajjSeason;
use App\Models\MaktabCategory;
use App\Models\Package;
use App\Models\Pilgrim;
use App\Reports\Contracts\ReportDefinition;
use App\Services\HajjSeasonService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use InvalidArgumentException;

class HajjRegistrationReportDefinition implements ReportDefinition
{
    public const KEY = 'hajj_registration';

    public function __construct(private HajjSeasonService $hajjSeasonService) {}

    public function key(): string
    {
        return self::KEY;
    }

    public function label(): string
    {
        return 'Hajj Registration';
    }

    public function category(): string
    {
        return 'Hajj';
    }

    public function description(): string
    {
        return 'Registration list with selectable columns and filters.';
    }

    public function columnCatalog(): array
    {
        return [
            'hajj_year' => ['label' => 'Hajj Year', 'group' => 'Registration'],
            'entry_date' => ['label' => 'Entry Date', 'group' => 'Registration'],
            'family_code' => ['label' => 'Family Code', 'group' => 'Family'],
            'family_number' => ['label' => 'Family Number', 'group' => 'Family'],
            'family_member_suffix' => ['label' => 'Family Suffix', 'group' => 'Family'],
            'full_name' => ['label' => 'Full Name', 'group' => 'Personal'],
            'surname' => ['label' => 'Surname', 'group' => 'Personal'],
            'given_name' => ['label' => 'Given Name', 'group' => 'Personal'],
            'father_husband_name' => ['label' => 'Father / Husband', 'group' => 'Personal'],
            'gender' => ['label' => 'Gender', 'group' => 'Personal'],
            'date_of_birth' => ['label' => 'Date of Birth', 'group' => 'Personal'],
            'age' => ['label' => 'Age', 'group' => 'Personal'],
            'birth_place' => ['label' => 'Birth Place', 'group' => 'Personal'],
            'blood_group' => ['label' => 'Blood Group', 'group' => 'Personal'],
            'cnic' => ['label' => 'CNIC', 'group' => 'Personal'],
            'mobile' => ['label' => 'Mobile', 'group' => 'Personal'],
            'address' => ['label' => 'Address', 'group' => 'Personal'],
            'passport_no' => ['label' => 'Passport No', 'group' => 'Travel Documents'],
            'passport_expiry' => ['label' => 'Passport Expiry', 'group' => 'Travel Documents'],
            'company' => ['label' => 'Company', 'group' => 'Masters'],
            'package' => ['label' => 'Package', 'group' => 'Masters'],
            'maktab_category' => ['label' => 'Maktab', 'group' => 'Masters'],
            'form_owner' => ['label' => 'Form Owner', 'group' => 'Masters'],
            'care_off' => ['label' => 'Care Off', 'group' => 'Masters'],
            'pod_city' => ['label' => 'POD', 'group' => 'Masters'],
            'room_type' => ['label' => 'Room Type', 'group' => 'Masters'],
            'mehram_name' => ['label' => 'Mehram Name', 'group' => 'Mehram & Waris'],
            'mehram_relation' => ['label' => 'Mehram Relation', 'group' => 'Mehram & Waris'],
            'waris_name' => ['label' => 'Waris Name', 'group' => 'Mehram & Waris'],
            'waris_cnic' => ['label' => 'Waris CNIC', 'group' => 'Mehram & Waris'],
            'waris_relation' => ['label' => 'Waris Relation', 'group' => 'Mehram & Waris'],
            'waris_mobile' => ['label' => 'Waris Mobile', 'group' => 'Mehram & Waris'],
            'qurbani_included' => ['label' => 'Qurbani', 'group' => 'Other'],
            'comments' => ['label' => 'Comments', 'group' => 'Other'],
            'entered_by' => ['label' => 'Entered By', 'group' => 'Other'],
            'created_at' => ['label' => 'Created At', 'group' => 'Other'],
        ];
    }

    public function defaultColumns(): array
    {
        return [
            'family_code',
            'full_name',
            'passport_no',
            'gender',
            'company',
            'package',
            'pod_city',
            'entry_date',
        ];
    }

    public function validateColumns(array $columns): array
    {
        $allowed = array_keys($this->columnCatalog());
        $selected = array_values(array_unique(array_filter(
            array_map(
                fn (mixed $column): mixed => $column === 'booking_date' ? 'entry_date' : $column,
                $columns,
            ),
            fn (mixed $column): bool => is_string($column) && $column !== '',
        )));

        if ($selected === []) {
            throw new InvalidArgumentException('Select at least one column for the report.');
        }

        $invalid = array_diff($selected, $allowed);

        if ($invalid !== []) {
            throw new InvalidArgumentException('Invalid report columns selected.');
        }

        return array_values(array_intersect($allowed, $selected));
    }

    public function normalizeFilters(array $input): array
    {
        return [
            'hajj_year' => filled($input['hajj_year'] ?? null)
                ? (int) $input['hajj_year']
                : $this->hajjSeasonService->activeYear(),
            'company_id' => $input['company_id'] ?? null,
            'package_id' => $input['package_id'] ?? null,
            'maktab_category_id' => $input['maktab_category_id'] ?? null,
            'form_owner_id' => $input['form_owner_id'] ?? null,
            'pod_city_id' => $input['pod_city_id'] ?? null,
            'care_off_id' => $input['care_off_id'] ?? null,
            'gender' => $input['gender'] ?? null,
            'entry_from' => $input['entry_from'] ?? null,
            'entry_to' => $input['entry_to'] ?? null,
            'search' => $input['search'] ?? null,
        ];
    }

    public function filterQueryParams(array $filters): array
    {
        return array_filter(
            $filters,
            fn (mixed $value): bool => filled($value),
        );
    }

    public function filterOptions(array $filters): array
    {
        $scopedIds = fn (string $column) => Pilgrim::query()
            ->where('hajj_year', $filters['hajj_year'])
            ->distinct()
            ->pluck($column)
            ->filter();

        return [
            'companies' => Company::query()->whereIn('id', $scopedIds('company_id'))->orderBy('name')->get(['id', 'name']),
            'packages' => Package::query()->whereIn('id', $scopedIds('package_id'))->orderBy('name')->get(['id', 'name']),
            'maktabCategories' => MaktabCategory::query()->whereIn('id', $scopedIds('maktab_category_id'))->orderBy('name')->get(['id', 'name']),
            'formOwners' => FormOwner::query()->whereIn('id', $scopedIds('form_owner_id'))->orderBy('name')->get(['id', 'name']),
            'podCities' => City::query()->whereIn('id', $scopedIds('pod_city_id'))->orderBy('name')->get(['id', 'name']),
            'careOffs' => CareOff::query()->whereIn('id', $scopedIds('care_off_id'))->orderBy('name')->get(['id', 'name']),
        ];
    }

    public function availableYears(): array
    {
        $years = array_values(array_unique(array_merge(
            HajjSeason::query()->orderByDesc('year')->pluck('year')->all(),
            Pilgrim::query()->distinct()->orderByDesc('hajj_year')->pluck('hajj_year')->all(),
        )));

        return $years !== [] ? $years : [$this->hajjSeasonService->activeYear()];
    }

    public function records(array $filters, array $columns): Collection
    {
        return $this->baseQuery($filters)
            ->with($this->relationsForColumns($columns))
            ->orderBy('family_code')
            ->orderBy('full_name')
            ->get();
    }

    public function headings(array $columns): array
    {
        $catalog = $this->columnCatalog();

        return array_map(
            fn (string $column): string => $catalog[$column]['label'],
            $columns,
        );
    }

    public function rowValues(mixed $record, array $columns): array
    {
        if (! $record instanceof Pilgrim) {
            return array_fill(0, count($columns), null);
        }

        return array_map(
            fn (string $column): string|int|null => $this->resolveColumnValue($record, $column),
            $columns,
        );
    }

    /** @param  list<string>  $columns
     * @return list<string>
     */
    private function relationsForColumns(array $columns): array
    {
        $map = [
            'company' => 'company:id,name',
            'package' => 'package:id,name,number',
            'maktab_category' => 'maktabCategory:id,name,zone',
            'form_owner' => 'formOwner:id,name',
            'care_off' => 'careOff:id,name',
            'pod_city' => 'podCity:id,name',
            'room_type' => 'roomType:id,name',
            'mehram_relation' => 'mehramRelation:id,name',
            'waris_relation' => 'warisRelation:id,name',
            'entered_by' => 'creator:id,name',
        ];

        return array_values(array_unique(array_intersect_key(
            $map,
            array_flip($columns),
        )));
    }

    private function resolveColumnValue(Pilgrim $pilgrim, string $column): string|int|null
    {
        return match ($column) {
            'hajj_year' => (string) $pilgrim->hajj_year,
            'entry_date' => $pilgrim->entry_date?->format('d M Y'),
            'family_code' => $pilgrim->family_code,
            'family_number' => $pilgrim->family_number !== null ? (string) $pilgrim->family_number : null,
            'family_member_suffix' => $pilgrim->family_member_suffix,
            'full_name' => $pilgrim->full_name,
            'surname' => $pilgrim->surname,
            'given_name' => $pilgrim->given_name,
            'father_husband_name' => $pilgrim->father_husband_name,
            'gender' => $pilgrim->gender?->label(),
            'date_of_birth' => $pilgrim->date_of_birth?->format('d M Y'),
            'age' => $pilgrim->age !== null ? (string) $pilgrim->age : null,
            'birth_place' => $pilgrim->birth_place,
            'blood_group' => $pilgrim->blood_group?->label(),
            'cnic' => $pilgrim->cnic,
            'mobile' => $pilgrim->mobile,
            'address' => $pilgrim->address,
            'passport_no' => $pilgrim->passport_no,
            'passport_expiry' => $pilgrim->passport_expiry?->format('d M Y'),
            'company' => $pilgrim->company?->name,
            'package' => $pilgrim->package?->name,
            'maktab_category' => $pilgrim->maktabCategory?->name,
            'form_owner' => $pilgrim->formOwner?->name,
            'care_off' => $pilgrim->careOff?->name,
            'pod_city' => $pilgrim->podCity?->name,
            'room_type' => $pilgrim->roomType?->name,
            'mehram_name' => $pilgrim->mehram_name,
            'mehram_relation' => $pilgrim->mehramRelation?->name,
            'waris_name' => $pilgrim->waris_name,
            'waris_cnic' => $pilgrim->waris_cnic,
            'waris_relation' => $pilgrim->warisRelation?->name,
            'waris_mobile' => $pilgrim->waris_mobile,
            'qurbani_included' => $pilgrim->qurbani_included ? 'Yes' : 'No',
            'comments' => $pilgrim->comments,
            'entered_by' => $pilgrim->creator?->name,
            'created_at' => $pilgrim->created_at?->format('d M Y H:i'),
            default => null,
        };
    }

    /** @param  array<string, mixed>  $filters
     * @return Builder<Pilgrim>
     */
    private function baseQuery(array $filters): Builder
    {
        $query = Pilgrim::query();
        $this->applyFilters($query, $filters);

        return $query;
    }

    /** @param  Builder<Pilgrim>  $query
     * @param  array<string, mixed>  $filters
     */
    private function applyFilters(Builder $query, array $filters): void
    {
        if (filled($filters['hajj_year'] ?? null)) {
            $query->where('hajj_year', (int) $filters['hajj_year']);
        }

        if (filled($filters['company_id'] ?? null)) {
            $query->where('company_id', (int) $filters['company_id']);
        }

        if (filled($filters['package_id'] ?? null)) {
            $query->where('package_id', (int) $filters['package_id']);
        }

        if (filled($filters['maktab_category_id'] ?? null)) {
            $query->where('maktab_category_id', (int) $filters['maktab_category_id']);
        }

        if (filled($filters['form_owner_id'] ?? null)) {
            $query->where('form_owner_id', (int) $filters['form_owner_id']);
        }

        if (filled($filters['pod_city_id'] ?? null)) {
            $query->where('pod_city_id', (int) $filters['pod_city_id']);
        }

        if (filled($filters['care_off_id'] ?? null)) {
            $query->where('care_off_id', (int) $filters['care_off_id']);
        }

        if (filled($filters['gender'] ?? null)) {
            $query->where('gender', $filters['gender']);
        }

        if (filled($filters['entry_from'] ?? null)) {
            $query->whereDate('entry_date', '>=', $filters['entry_from']);
        }

        if (filled($filters['entry_to'] ?? null)) {
            $query->whereDate('entry_date', '<=', $filters['entry_to']);
        }

        if (filled($filters['search'] ?? null)) {
            $term = trim((string) $filters['search']);

            $query->where(function (Builder $query) use ($term): void {
                $query->where('full_name', 'like', "%{$term}%")
                    ->orWhere('passport_no', 'like', "%{$term}%")
                    ->orWhere('family_code', 'like', "%{$term}%")
                    ->orWhere('cnic', 'like', "%{$term}%")
                    ->orWhere('mobile', 'like', "%{$term}%");
            });
        }
    }
}
