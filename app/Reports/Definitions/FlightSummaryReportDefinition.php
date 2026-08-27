<?php

namespace App\Reports\Definitions;

use App\Enums\FlightDirection;
use App\Enums\FlightType;
use App\Models\CareOff;
use App\Models\City;
use App\Models\Company;
use App\Models\Flight;
use App\Models\HajjSeason;
use App\Models\Package;
use App\Models\Pilgrim;
use App\Reports\Contracts\ProvidesReportSummary;
use App\Reports\Contracts\ReportDefinition;
use App\Services\HajjSeasonService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use InvalidArgumentException;

class FlightSummaryReportDefinition implements ProvidesReportSummary, ReportDefinition
{
    public const KEY = 'flight_summary';

    public function __construct(private HajjSeasonService $hajjSeasonService) {}

    public function key(): string
    {
        return self::KEY;
    }

    public function label(): string
    {
        return 'Flight Summary';
    }

    public function category(): string
    {
        return 'Flight';
    }

    /** @return list<string> */
    public function nonSpreadsheetExportColumns(): array
    {
        return [];
    }

    public function description(): string
    {
        return 'Flight-wise hujaj counts and assignment overview for the selected Hajj year.';
    }

    public function filtersView(): string
    {
        return 'admin.reports.filters.flight-summary';
    }

    public function columnCatalog(): array
    {
        return [
            'direction' => ['label' => 'Journey', 'group' => 'Flight'],
            'flight_type' => ['label' => 'Type', 'group' => 'Flight'],
            'departure_city' => ['label' => 'From', 'group' => 'Flight'],
            'via_city' => ['label' => 'Via', 'group' => 'Flight'],
            'arrival_city' => ['label' => 'To', 'group' => 'Flight'],
            'departure_date' => ['label' => 'Departure Date', 'group' => 'Flight'],
            'departure_time' => ['label' => 'Departure Time', 'group' => 'Flight'],
            'departure_flight_no' => ['label' => 'Flight No', 'group' => 'Flight'],
            'departure_airline' => ['label' => 'Airline', 'group' => 'Flight'],
            'via_total_stay' => ['label' => 'Total Stay', 'group' => 'Flight'],
            'arrival_date' => ['label' => 'Arrival Date', 'group' => 'Flight'],
            'pilgrims_count' => ['label' => 'Hujaj', 'group' => 'Flight'],
        ];
    }

    public function columnGroupOrder(): array
    {
        return ['Flight'];
    }

    public function defaultColumns(): array
    {
        return [
            'direction',
            'departure_flight_no',
            'departure_date',
            'departure_city',
            'arrival_city',
            'pilgrims_count',
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
            'direction' => $input['direction'] ?? null,
            'flight_type' => $input['flight_type'] ?? null,
            'departure_from' => $input['departure_from'] ?? null,
            'departure_to' => $input['departure_to'] ?? null,
            'company_id' => $input['company_id'] ?? null,
            'package_id' => $input['package_id'] ?? null,
            'pod_city_id' => $input['pod_city_id'] ?? null,
            'care_off_id' => $input['care_off_id'] ?? null,
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
            'directions' => FlightDirection::cases(),
            'flightTypes' => FlightType::cases(),
            'companies' => Company::query()
                ->whereIn('id', $scopedIds('company_id'))
                ->orderBy('name')
                ->get(['id', 'name', 'munazzam_code']),
            'packages' => Package::query()->where('is_active', true)->orderBy('number')->get(),
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
        return $this->flightQuery($filters, $columns)
            ->orderBy('departure_date')
            ->orderBy('departure_flight_no')
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
        if (! $record instanceof Flight) {
            return array_fill(0, count($columns), null);
        }

        return array_map(
            fn (string $column): string|int|null => $this->resolveColumnValue($record, $column),
            $columns,
        );
    }

    public function summaryStats(array $filters): array
    {
        $pilgrimQuery = Pilgrim::query()->where('hajj_year', $filters['hajj_year']);
        $this->applyPilgrimFilters($pilgrimQuery, $filters);

        $registered = (clone $pilgrimQuery)->count();
        $assigned = (clone $pilgrimQuery)->whereHas('flights')->count();
        $missingOutbound = (clone $pilgrimQuery)
            ->whereHas('flights', fn (Builder $query) => $query->where('direction', FlightDirection::Return))
            ->whereDoesntHave('flights', fn (Builder $query) => $query->where('direction', FlightDirection::Outbound))
            ->count();
        $missingReturn = (clone $pilgrimQuery)
            ->whereHas('flights', fn (Builder $query) => $query->where('direction', FlightDirection::Outbound))
            ->whereDoesntHave('flights', fn (Builder $query) => $query->where('direction', FlightDirection::Return))
            ->count();

        $flights = $this->flightQuery($filters, ['pilgrims_count'])->get();
        $totalAssignments = (int) $flights->sum('pilgrims_count');

        return [
            ['label' => 'Flights', 'value' => $flights->count(), 'variant' => 'total'],
            ['label' => 'Registered Hujaj', 'value' => $registered, 'variant' => 'entered'],
            ['label' => 'Assigned Hujaj', 'value' => $assigned, 'variant' => 'entered'],
            ['label' => 'Unassigned Hujaj', 'value' => max(0, $registered - $assigned), 'variant' => 'remaining'],
            ['label' => 'Total Assignments', 'value' => $totalAssignments, 'variant' => 'total'],
            ['label' => 'Missing Outbound', 'value' => $missingOutbound, 'variant' => 'remaining'],
            ['label' => 'Missing Return', 'value' => $missingReturn, 'variant' => 'remaining'],
        ];
    }

    /** @param  list<string>  $columns
     * @return Builder<Flight>
     */
    private function flightQuery(array $filters, array $columns): Builder
    {
        $query = Flight::query();
        $this->applyFlightFilters($query, $filters);

        $relations = $this->flightRelationsForColumns($columns);

        if ($relations !== []) {
            $query->with($relations);
        }

        return $query->withCount([
            'pilgrims as pilgrims_count' => function (Builder $query) use ($filters): void {
                $query->where('hajj_year', $filters['hajj_year']);
                $this->applyPilgrimFilters($query, $filters);
            },
        ]);
    }

    /** @param  list<string>  $columns
     * @return list<string>
     */
    private function flightRelationsForColumns(array $columns): array
    {
        $map = [
            'departure_city' => 'departureCity:id,name',
            'departure_airline' => 'departureAirline:id,name',
            'via_city' => 'viaCity:id,name',
            'arrival_city' => 'arrivalCity:id,name',
        ];

        return array_values(array_unique(array_intersect_key(
            $map,
            array_flip($columns),
        )));
    }

    private function resolveColumnValue(Flight $flight, string $column): string|int|null
    {
        return match ($column) {
            'direction' => $flight->direction->label(),
            'flight_type' => $flight->flight_type->label(),
            'departure_city' => $flight->departureCity?->name,
            'via_city' => $flight->viaCity?->name,
            'arrival_city' => $flight->arrivalCity?->name,
            'departure_date' => $flight->departure_date?->format('d M Y'),
            'departure_time' => $this->formatTime($flight->departure_time),
            'departure_flight_no' => $flight->departure_flight_no,
            'departure_airline' => $flight->departureAirline?->name,
            'via_total_stay' => $flight->via_total_stay_label,
            'arrival_date' => $flight->arrival_date?->format('d M Y'),
            'pilgrims_count' => (string) ($flight->pilgrims_count ?? 0),
            default => null,
        };
    }

    private function formatTime(mixed $time): ?string
    {
        if (! filled($time)) {
            return null;
        }

        return substr((string) $time, 0, 5);
    }

    /** @param  Builder<Flight>|Builder<Pilgrim>  $query
     * @param  array<string, mixed>  $filters
     */
    private function applyFlightFilters(Builder $query, array $filters): void
    {
        if (filled($filters['direction'] ?? null)) {
            $query->where('direction', $filters['direction']);
        }

        if (filled($filters['flight_type'] ?? null)) {
            $query->where('flight_type', $filters['flight_type']);
        }

        if (filled($filters['departure_from'] ?? null)) {
            $query->whereDate('departure_date', '>=', $filters['departure_from']);
        }

        if (filled($filters['departure_to'] ?? null)) {
            $query->whereDate('departure_date', '<=', $filters['departure_to']);
        }

        if (filled($filters['search'] ?? null)) {
            $term = trim((string) $filters['search']);

            $query->where(function (Builder $query) use ($term): void {
                $query->where('departure_flight_no', 'like', "%{$term}%")
                    ->orWhere('via_departure_flight_no', 'like', "%{$term}%");
            });
        }
    }

    /** @param  Builder<Pilgrim>  $query
     * @param  array<string, mixed>  $filters
     */
    private function applyPilgrimFilters(Builder $query, array $filters): void
    {
        if (filled($filters['company_id'] ?? null)) {
            $query->where('company_id', (int) $filters['company_id']);
        }

        if (filled($filters['package_id'] ?? null)) {
            $query->where('package_id', (int) $filters['package_id']);
        }

        if (filled($filters['pod_city_id'] ?? null)) {
            $query->where('pod_city_id', (int) $filters['pod_city_id']);
        }

        if (filled($filters['care_off_id'] ?? null)) {
            $query->where('care_off_id', (int) $filters['care_off_id']);
        }
    }
}
