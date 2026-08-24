<?php

namespace App\Reports\Definitions;

use App\Enums\FlightDirection;
use App\Enums\FlightType;
use App\Models\Company;
use App\Models\Flight;
use App\Models\HajjSeason;
use App\Models\Package;
use App\Models\Pilgrim;
use App\Reports\Contracts\ReportDefinition;
use App\Reports\FlightReportRecord;
use App\Services\HajjSeasonService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Collection;
use InvalidArgumentException;

class FlightReportDefinition implements ReportDefinition
{
    public const KEY = 'flight';

    public function __construct(private HajjSeasonService $hajjSeasonService) {}

    public function key(): string
    {
        return self::KEY;
    }

    public function label(): string
    {
        return 'Flight Reports';
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
        return 'Flight assignments with selectable columns and filters.';
    }

    public function filtersView(): string
    {
        return 'admin.reports.filters.flight';
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
            'departure_airport' => ['label' => 'Departure Airport', 'group' => 'Flight'],
            'departure_airline' => ['label' => 'Departure Airline', 'group' => 'Flight'],
            'via_airport' => ['label' => 'Via Airport', 'group' => 'Flight'],
            'via_arrival_date' => ['label' => 'Via Arrival Date', 'group' => 'Flight'],
            'via_arrival_time' => ['label' => 'Via Arrival Time', 'group' => 'Flight'],
            'via_departure_flight_no' => ['label' => 'Via Flight No', 'group' => 'Flight'],
            'via_departure_date' => ['label' => 'Via Departure Date', 'group' => 'Flight'],
            'via_departure_time' => ['label' => 'Via Departure Time', 'group' => 'Flight'],
            'via_airline' => ['label' => 'Via Airline', 'group' => 'Flight'],
            'via_total_stay' => ['label' => 'Total Stay', 'group' => 'Flight'],
            'arrival_date' => ['label' => 'Arrival Date', 'group' => 'Flight'],
            'arrival_time' => ['label' => 'Arrival Time', 'group' => 'Flight'],
            'arrival_airport' => ['label' => 'Arrival Airport', 'group' => 'Flight'],
            'assigned_at' => ['label' => 'Assigned At', 'group' => 'Flight'],
            'hajj_year' => ['label' => 'Hajj Year', 'group' => 'Hujaj'],
            'full_name' => ['label' => 'Full Name', 'group' => 'Hujaj'],
            'passport_no' => ['label' => 'Passport No', 'group' => 'Hujaj'],
            'family_code' => ['label' => 'Family Code', 'group' => 'Hujaj'],
            'gender' => ['label' => 'Gender', 'group' => 'Hujaj'],
            'company' => ['label' => 'Company', 'group' => 'Hujaj'],
            'package' => ['label' => 'Package', 'group' => 'Hujaj'],
            'pod_city' => ['label' => 'POD', 'group' => 'Hujaj'],
            'mobile' => ['label' => 'Mobile', 'group' => 'Hujaj'],
        ];
    }

    public function columnGroupOrder(): array
    {
        return [
            'Flight',
            'Hujaj',
        ];
    }

    public function defaultColumns(): array
    {
        return [
            'direction',
            'departure_flight_no',
            'departure_date',
            'full_name',
            'passport_no',
            'family_code',
            'company',
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
            'flight_id' => $input['flight_id'] ?? null,
            'departure_from' => $input['departure_from'] ?? null,
            'departure_to' => $input['departure_to'] ?? null,
            'company_id' => $input['company_id'] ?? null,
            'package_id' => $input['package_id'] ?? null,
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
        $scopedCompanyIds = Pilgrim::query()
            ->where('hajj_year', $filters['hajj_year'])
            ->whereHas('flights')
            ->distinct()
            ->pluck('company_id')
            ->filter();

        return [
            'directions' => FlightDirection::cases(),
            'flightTypes' => FlightType::cases(),
            'companies' => Company::query()
                ->whereIn('id', $scopedCompanyIds)
                ->orderBy('name')
                ->get(['id', 'name', 'munazzam_code']),
            'packages' => Package::query()->where('is_active', true)->orderBy('number')->get(),
            'flights' => Flight::query()
                ->whereHas('pilgrims', fn (Builder $query) => $query->where('hajj_year', $filters['hajj_year']))
                ->with('departureCity:id,name')
                ->orderBy('departure_date')
                ->orderBy('departure_flight_no')
                ->get(),
        ];
    }

    public function availableYears(): array
    {
        $years = array_values(array_unique(array_merge(
            HajjSeason::query()->orderByDesc('year')->pluck('year')->all(),
            Pilgrim::query()
                ->whereHas('flights')
                ->distinct()
                ->orderByDesc('hajj_year')
                ->pluck('hajj_year')
                ->all(),
        )));

        return $years !== [] ? $years : [$this->hajjSeasonService->activeYear()];
    }

    public function records(array $filters, array $columns): Collection
    {
        $pilgrims = $this->baseQuery($filters)
            ->with([
                'flights' => fn ($query) => $this->applyFlightFilters($query, $filters)
                    ->with($this->flightRelationsForColumns($columns))
                    ->orderBy('departure_date')
                    ->orderBy('departure_flight_no'),
                ...$this->pilgrimRelationsForColumns($columns),
            ])
            ->orderBy('family_code')
            ->orderBy('full_name')
            ->get();

        return $pilgrims
            ->flatMap(function (Pilgrim $pilgrim): Collection {
                return $pilgrim->flights->map(fn (Flight $flight): FlightReportRecord => new FlightReportRecord(
                    $flight,
                    $pilgrim,
                    $flight->pivot?->created_at,
                ));
            })
            ->sortBy([
                fn (FlightReportRecord $record): string => $record->flight->departure_date?->format('Y-m-d') ?? '',
                fn (FlightReportRecord $record): string => $record->flight->direction->value,
                fn (FlightReportRecord $record): string => $record->pilgrim->family_code ?? '',
                fn (FlightReportRecord $record): string => $record->pilgrim->full_name ?? '',
            ])
            ->values();
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
        if (! $record instanceof FlightReportRecord) {
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
    private function flightRelationsForColumns(array $columns): array
    {
        $map = [
            'departure_city' => 'departureCity:id,name',
            'departure_airport' => 'departureAirport:id,name,code',
            'departure_airline' => 'departureAirline:id,name',
            'via_city' => 'viaCity:id,name',
            'via_airport' => 'viaAirport:id,name,code',
            'via_airline' => 'viaAirline:id,name',
            'arrival_city' => 'arrivalCity:id,name',
            'arrival_airport' => 'arrivalAirport:id,name,code',
        ];

        return array_values(array_unique(array_intersect_key(
            $map,
            array_flip($columns),
        )));
    }

    /** @param  list<string>  $columns
     * @return list<string>
     */
    private function pilgrimRelationsForColumns(array $columns): array
    {
        $map = [
            'company' => 'company:id,name,munazzam_code',
            'package' => 'package:id,name,number,price,days,duration,qurbani_included',
            'pod_city' => 'podCity:id,name',
        ];

        return array_values(array_unique(array_intersect_key(
            $map,
            array_flip($columns),
        )));
    }

    private function resolveColumnValue(FlightReportRecord $record, string $column): string|int|null
    {
        $flight = $record->flight;
        $pilgrim = $record->pilgrim;

        return match ($column) {
            'direction' => $flight->direction->label(),
            'flight_type' => $flight->flight_type->label(),
            'departure_city' => $flight->departureCity?->name,
            'via_city' => $flight->viaCity?->name,
            'arrival_city' => $flight->arrivalCity?->name,
            'departure_date' => $flight->departure_date?->format('d M Y'),
            'departure_time' => $this->formatTime($flight->departure_time),
            'departure_flight_no' => $flight->departure_flight_no,
            'departure_airport' => $flight->departureAirport?->name,
            'departure_airline' => $flight->departureAirline?->name,
            'via_airport' => $flight->viaAirport?->name,
            'via_arrival_date' => $flight->via_arrival_date?->format('d M Y'),
            'via_arrival_time' => $this->formatTime($flight->via_arrival_time),
            'via_departure_flight_no' => $flight->via_departure_flight_no,
            'via_departure_date' => $flight->via_departure_date?->format('d M Y'),
            'via_departure_time' => $this->formatTime($flight->via_departure_time),
            'via_airline' => $flight->viaAirline?->name,
            'via_total_stay' => $flight->via_total_stay_label,
            'arrival_date' => $flight->arrival_date?->format('d M Y'),
            'arrival_time' => $this->formatTime($flight->arrival_time),
            'arrival_airport' => $flight->arrivalAirport?->name,
            'assigned_at' => $record->assignedAt?->format('d M Y H:i'),
            'hajj_year' => (string) $pilgrim->hajj_year,
            'full_name' => $pilgrim->full_name,
            'passport_no' => $pilgrim->passport_no,
            'family_code' => $pilgrim->family_code,
            'gender' => $pilgrim->gender?->label(),
            'company' => $pilgrim->company?->registrationOptionLabel(),
            'package' => $pilgrim->package?->registrationOptionLabel(),
            'pod_city' => $pilgrim->podCity?->name,
            'mobile' => $pilgrim->mobile,
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

    /** @param  array<string, mixed>  $filters
     * @return Builder<Pilgrim>
     */
    private function baseQuery(array $filters): Builder
    {
        $query = Pilgrim::query()
            ->where('hajj_year', $filters['hajj_year'])
            ->whereHas('flights', fn (Builder $query) => $this->applyFlightFilters($query, $filters));

        $this->applyPilgrimFilters($query, $filters);

        return $query;
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

        if (filled($filters['search'] ?? null)) {
            $term = trim((string) $filters['search']);

            $query->where(function (Builder $query) use ($term): void {
                $query->where('full_name', 'like', "%{$term}%")
                    ->orWhere('passport_no', 'like', "%{$term}%")
                    ->orWhere('family_code', 'like', "%{$term}%")
                    ->orWhere('mobile', 'like', "%{$term}%")
                    ->orWhereHas('flights', function (Builder $query) use ($term): void {
                        $query->where('departure_flight_no', 'like', "%{$term}%")
                            ->orWhere('via_departure_flight_no', 'like', "%{$term}%");
                    });
            });
        }
    }

    /** @param  Builder<Flight>|Relation<Flight, Pilgrim, Pivot>  $query
     * @param  array<string, mixed>  $filters
     * @return Builder<Flight>|Relation<Flight, Pilgrim, Pivot>
     */
    private function applyFlightFilters(Builder|Relation $query, array $filters): Builder|Relation
    {
        if (filled($filters['direction'] ?? null)) {
            $query->where('direction', $filters['direction']);
        }

        if (filled($filters['flight_type'] ?? null)) {
            $query->where('flight_type', $filters['flight_type']);
        }

        if (filled($filters['flight_id'] ?? null)) {
            $query->where($query->qualifyColumn('id'), (int) $filters['flight_id']);
        }

        if (filled($filters['departure_from'] ?? null)) {
            $query->whereDate('departure_date', '>=', $filters['departure_from']);
        }

        if (filled($filters['departure_to'] ?? null)) {
            $query->whereDate('departure_date', '<=', $filters['departure_to']);
        }

        return $query;
    }
}
