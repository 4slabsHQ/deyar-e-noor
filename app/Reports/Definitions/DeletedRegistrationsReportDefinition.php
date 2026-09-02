<?php

namespace App\Reports\Definitions;

use App\Models\Company;
use App\Models\HajjSeason;
use App\Models\PilgrimDeletionLog;
use App\Models\User;
use App\Reports\Contracts\ReportDefinition;
use App\Services\HajjSeasonService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use InvalidArgumentException;

class DeletedRegistrationsReportDefinition implements ReportDefinition
{
    public const KEY = 'deleted_registrations';

    public function __construct(private HajjSeasonService $hajjSeasonService) {}

    public function key(): string
    {
        return self::KEY;
    }

    public function label(): string
    {
        return 'Deleted Registrations';
    }

    public function category(): string
    {
        return 'Deleted';
    }

    /** @return list<string> */
    public function nonSpreadsheetExportColumns(): array
    {
        return [];
    }

    /** @return list<string> */
    public function frontendOnlyColumns(): array
    {
        return [];
    }

    public function exportCellValue(string $column, string|int|null $value): string|int|null
    {
        return $value;
    }

    public function description(): string
    {
        return 'Audit log of deleted Hajj registrations.';
    }

    public function filtersView(): string
    {
        return 'admin.reports.filters.deleted-registrations';
    }

    public function columnCatalog(): array
    {
        return [
            'deleted_at' => ['label' => 'Deleted At', 'group' => 'Deletion'],
            'deleted_by' => ['label' => 'Deleted By', 'group' => 'Deletion'],
            'hajj_year' => ['label' => 'Hajj Year', 'group' => 'Registration'],
            'entry_date' => ['label' => 'Entry Date', 'group' => 'Registration'],
            'full_name' => ['label' => 'Full Name', 'group' => 'Registration'],
            'passport_no' => ['label' => 'Passport No', 'group' => 'Registration'],
            'family_code' => ['label' => 'Family Code', 'group' => 'Registration'],
            'gender' => ['label' => 'Gender', 'group' => 'Registration'],
            'company' => ['label' => 'Company', 'group' => 'Registration'],
            'package' => ['label' => 'Package', 'group' => 'Registration'],
            'pod_city' => ['label' => 'POD', 'group' => 'Registration'],
            'mobile' => ['label' => 'Mobile', 'group' => 'Registration'],
        ];
    }

    public function columnGroupOrder(): array
    {
        return [
            'Deletion',
            'Registration',
        ];
    }

    public function defaultColumns(): array
    {
        return [
            'deleted_at',
            'deleted_by',
            'full_name',
            'passport_no',
            'family_code',
            'company',
            'hajj_year',
        ];
    }

    public function validateColumns(array $columns): array
    {
        $allowed = array_keys($this->columnCatalog());
        $selected = array_values(array_unique(array_filter(
            $columns,
            fn (mixed $column): bool => is_string($column) && $column !== '',
        )));

        if ($selected === []) {
            throw new InvalidArgumentException('Select at least one column for the report.');
        }

        $invalid = array_diff($selected, $allowed);

        if ($invalid !== []) {
            throw new InvalidArgumentException('Invalid report columns selected.');
        }

        return array_values(array_intersect($selected, $allowed));
    }

    public function normalizeFilters(array $input): array
    {
        return [
            'hajj_year' => filled($input['hajj_year'] ?? null)
                ? (int) $input['hajj_year']
                : $this->hajjSeasonService->activeYear(),
            'company_id' => $input['company_id'] ?? null,
            'deleted_by' => $input['deleted_by'] ?? null,
            'deleted_from' => $input['deleted_from'] ?? null,
            'deleted_to' => $input['deleted_to'] ?? null,
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
        $scopedCompanyIds = PilgrimDeletionLog::query()
            ->when(filled($filters['hajj_year'] ?? null), fn (Builder $query) => $query->where('hajj_year', (int) $filters['hajj_year']))
            ->distinct()
            ->pluck('company_id')
            ->filter();

        $deleterIds = PilgrimDeletionLog::query()
            ->when(filled($filters['hajj_year'] ?? null), fn (Builder $query) => $query->where('hajj_year', (int) $filters['hajj_year']))
            ->distinct()
            ->pluck('deleted_by')
            ->filter();

        return [
            'companies' => Company::query()
                ->whereIn('id', $scopedCompanyIds)
                ->orderBy('name')
                ->get(['id', 'name', 'munazzam_code']),
            'deleters' => User::query()
                ->whereIn('id', $deleterIds)
                ->orderBy('name')
                ->get(['id', 'name']),
        ];
    }

    public function availableYears(): array
    {
        $years = array_values(array_unique(array_merge(
            HajjSeason::query()->orderByDesc('year')->pluck('year')->all(),
            PilgrimDeletionLog::query()->distinct()->orderByDesc('hajj_year')->pluck('hajj_year')->all(),
        )));

        return $years !== [] ? $years : [$this->hajjSeasonService->activeYear()];
    }

    public function records(array $filters, array $columns): Collection
    {
        $needsDeleter = in_array('deleted_by', $columns, true);

        return $this->baseQuery($filters)
            ->when($needsDeleter, fn (Builder $query) => $query->with('deleter:id,name'))
            ->orderByDesc('deleted_at')
            ->orderByDesc('id')
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
        if (! $record instanceof PilgrimDeletionLog) {
            return array_fill(0, count($columns), null);
        }

        return array_map(
            fn (string $column): string|int|null => $this->resolveColumnValue($record, $column),
            $columns,
        );
    }

    private function resolveColumnValue(PilgrimDeletionLog $log, string $column): string|int|null
    {
        return match ($column) {
            'deleted_at' => $log->deleted_at?->format('d M Y H:i'),
            'deleted_by' => $log->deleter?->name,
            'hajj_year' => $log->hajj_year !== null ? (string) $log->hajj_year : null,
            'entry_date' => $log->entry_date?->format('d M Y'),
            'full_name' => $log->full_name,
            'passport_no' => $log->passport_no,
            'family_code' => $log->family_code,
            'gender' => $log->gender,
            'company' => $log->company_name,
            'package' => $log->package_label,
            'pod_city' => $log->pod_city_name,
            'mobile' => $log->mobile,
            default => null,
        };
    }

    /** @param  array<string, mixed>  $filters
     * @return Builder<PilgrimDeletionLog>
     */
    private function baseQuery(array $filters): Builder
    {
        $query = PilgrimDeletionLog::query();

        if (filled($filters['hajj_year'] ?? null)) {
            $query->where('hajj_year', (int) $filters['hajj_year']);
        }

        if (filled($filters['company_id'] ?? null)) {
            $query->where('company_id', (int) $filters['company_id']);
        }

        if (filled($filters['deleted_by'] ?? null)) {
            $query->where('deleted_by', (int) $filters['deleted_by']);
        }

        if (filled($filters['deleted_from'] ?? null)) {
            $query->whereDate('deleted_at', '>=', $filters['deleted_from']);
        }

        if (filled($filters['deleted_to'] ?? null)) {
            $query->whereDate('deleted_at', '<=', $filters['deleted_to']);
        }

        if (filled($filters['search'] ?? null)) {
            $term = trim((string) $filters['search']);

            $query->where(function (Builder $query) use ($term): void {
                $query->where('full_name', 'like', "%{$term}%")
                    ->orWhere('passport_no', 'like', "%{$term}%")
                    ->orWhere('family_code', 'like', "%{$term}%")
                    ->orWhere('company_name', 'like', "%{$term}%")
                    ->orWhere('mobile', 'like', "%{$term}%");
            });
        }

        return $query;
    }
}
