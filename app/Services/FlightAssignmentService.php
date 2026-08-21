<?php

namespace App\Services;

use App\Enums\FlightAssignmentAction;
use App\Models\Flight;
use App\Models\FlightAssignmentLog;
use App\Models\Pilgrim;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class FlightAssignmentService
{
    public function __construct(private HajjSeasonService $hajjSeasonService) {}

    /**
     * @param  array<string, mixed>  $filters
     * @return Collection<int, Pilgrim>
     */
    public function pilgrimsForAssignment(Flight $flight, array $filters = []): Collection
    {
        $query = $this->basePilgrimQuery($flight, $filters)
            ->with([
                'flights' => fn ($query) => $query
                    ->select('flights.id', 'flights.direction', 'flights.departure_flight_no'),
            ])
            ->withExists(['flights as on_this_flight' => fn (Builder $query) => $query->where('flights.id', $flight->id)]);

        $this->applyAssignmentStatusFilter($query, $flight, $filters);

        return $query
            ->orderBy('family_code')
            ->orderBy('full_name')
            ->get()
            ->each(fn (Pilgrim $pilgrim): Pilgrim => $this->applyAssignmentEligibility($pilgrim, $flight));
    }

    /**
     * @param  array<string, mixed>  $filters
     * @return list<int>
     */
    public function resolvePilgrimIdsForAction(Flight $flight, array $filters, string $action): array
    {
        return $this->pilgrimsForAssignment($flight, $filters)
            ->filter(fn (Pilgrim $pilgrim): bool => $action === 'assign'
                ? (bool) $pilgrim->can_assign
                : (bool) $pilgrim->can_remove)
            ->pluck('id')
            ->all();
    }

    /**
     * @param  list<int>  $pilgrimIds
     */
    public function validateAssignSelection(Flight $flight, array $pilgrimIds): ?string
    {
        if ($pilgrimIds === []) {
            return null;
        }

        $pilgrims = Pilgrim::query()
            ->whereIn('id', $pilgrimIds)
            ->with([
                'flights' => fn ($query) => $query
                    ->select('flights.id', 'flights.direction', 'flights.departure_flight_no'),
            ])
            ->get()
            ->each(fn (Pilgrim $pilgrim): Pilgrim => $this->applyAssignmentEligibility($pilgrim, $flight))
            ->keyBy('id');

        $blocked = [];

        foreach ($pilgrimIds as $pilgrimId) {
            $pilgrim = $pilgrims->get($pilgrimId);

            if ($pilgrim === null) {
                $blocked[] = 'One or more selected hujaj could not be found.';

                continue;
            }

            if (! $pilgrim->can_assign) {
                $blocked[] = "{$pilgrim->full_name}: {$pilgrim->assignment_block_reason}";
            }
        }

        if ($blocked === []) {
            return null;
        }

        if (count($blocked) === 1) {
            return 'Cannot assign — '.$blocked[0];
        }

        $preview = implode('; ', array_slice($blocked, 0, 3));

        if (count($blocked) > 3) {
            $preview .= '; and '.(count($blocked) - 3).' more';
        }

        return 'Cannot assign '.count($blocked).' hujaj — '.$preview.'.';
    }

    /**
     * @param  list<int>  $pilgrimIds
     */
    public function validateRemoveSelection(Flight $flight, array $pilgrimIds): ?string
    {
        if ($pilgrimIds === []) {
            return null;
        }

        $assignedIds = $flight->pilgrims()
            ->whereIn('pilgrims.id', $pilgrimIds)
            ->pluck('pilgrims.id')
            ->all();

        $notOnFlight = array_values(array_diff($pilgrimIds, $assignedIds));

        if ($notOnFlight === []) {
            return null;
        }

        $names = Pilgrim::query()
            ->whereIn('id', $notOnFlight)
            ->orderBy('full_name')
            ->pluck('full_name')
            ->all();

        if (count($names) === 1) {
            return "Cannot remove — {$names[0]} is not assigned to this flight.";
        }

        $preview = implode(', ', array_slice($names, 0, 3));

        if (count($names) > 3) {
            $preview .= ', and '.(count($names) - 3).' more';
        }

        return 'Cannot remove — '.$preview.' are not assigned to this flight.';
    }

    /**
     * @param  list<int>  $pilgrimIds
     * @return array{assigned: int}
     */
    public function assign(Flight $flight, array $pilgrimIds, User $user): array
    {
        $summary = ['assigned' => 0];

        $activeYear = $this->hajjSeasonService->activeYear();
        $now = now();

        DB::transaction(function () use ($flight, $pilgrimIds, $user, $activeYear, $now, &$summary): void {
            $pilgrims = Pilgrim::query()
                ->whereIn('id', $pilgrimIds)
                ->where('hajj_year', $activeYear)
                ->get()
                ->keyBy('id');

            foreach ($pilgrimIds as $pilgrimId) {
                $pilgrim = $pilgrims->get($pilgrimId);

                if ($pilgrim === null) {
                    continue;
                }

                $flight->pilgrims()->attach($pilgrim->id, [
                    'assigned_by' => $user->id,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);

                FlightAssignmentLog::query()->create([
                    'flight_id' => $flight->id,
                    'pilgrim_id' => $pilgrim->id,
                    'action' => FlightAssignmentAction::Assigned,
                    'performed_by' => $user->id,
                    'performed_at' => $now,
                ]);

                $summary['assigned']++;
            }
        });

        return $summary;
    }

    /**
     * @param  list<int>  $pilgrimIds
     * @return array{removed: int}
     */
    public function remove(Flight $flight, array $pilgrimIds, User $user): array
    {
        $summary = ['removed' => 0];

        $now = now();

        DB::transaction(function () use ($flight, $pilgrimIds, $user, $now, &$summary): void {
            $assignedIds = $flight->pilgrims()
                ->whereIn('pilgrims.id', $pilgrimIds)
                ->pluck('pilgrims.id')
                ->all();

            foreach ($assignedIds as $pilgrimId) {
                $flight->pilgrims()->detach($pilgrimId);

                FlightAssignmentLog::query()->create([
                    'flight_id' => $flight->id,
                    'pilgrim_id' => $pilgrimId,
                    'action' => FlightAssignmentAction::Removed,
                    'performed_by' => $user->id,
                    'performed_at' => $now,
                ]);

                $summary['removed']++;
            }
        });

        return $summary;
    }

    public function assignmentSummaryMessage(string $action, array $summary): string
    {
        if ($action === 'assign') {
            $count = (int) ($summary['assigned'] ?? 0);

            return $count === 1 ? '1 hajji assigned.' : "{$count} hujaj assigned.";
        }

        $count = (int) ($summary['removed'] ?? 0);

        return $count === 1 ? '1 hajji removed.' : "{$count} hujaj removed.";
    }

    public function emptySelectionMessage(string $action): string
    {
        return $action === 'assign'
            ? 'No hujaj in the current list can be assigned.'
            : 'No hujaj in the current list can be removed.';
    }

    /**
     * @return array<string, mixed>
     */
    public function normalizeFilters(array $input): array
    {
        return [
            'company_id' => $input['company_id'] ?? null,
            'pod_city_id' => $input['pod_city_id'] ?? null,
            'package_id' => $input['package_id'] ?? null,
            'form_owner_id' => $input['form_owner_id'] ?? null,
            'family_code' => $input['family_code'] ?? null,
            'search' => $input['search'] ?? null,
            'assignment_status' => $input['assignment_status'] ?? 'all',
        ];
    }

    /**
     * @param  array<string, mixed>  $filters
     * @return array<string, mixed>
     */
    public function filterQueryParams(array $filters): array
    {
        return array_filter(
            $filters,
            fn (mixed $value, string $key): bool => filled($value) && ! ($key === 'assignment_status' && $value === 'all'),
            ARRAY_FILTER_USE_BOTH,
        );
    }

    private function applyAssignmentEligibility(Pilgrim $pilgrim, Flight $flight): Pilgrim
    {
        $sameDirectionFlight = $pilgrim->flights
            ->first(fn (Flight $assignedFlight): bool => $assignedFlight->direction === $flight->direction
                && $assignedFlight->id !== $flight->id);

        if ($pilgrim->on_this_flight) {
            $pilgrim->can_assign = false;
            $pilgrim->can_remove = true;
            $pilgrim->assignment_status_label = 'On this flight';
            $pilgrim->assignment_block_reason = 'Already assigned to this flight. Use Remove to unassign.';
            $pilgrim->blocking_flight_no = null;
        } elseif ($sameDirectionFlight instanceof Flight) {
            $pilgrim->can_assign = false;
            $pilgrim->can_remove = false;
            $pilgrim->assignment_status_label = 'On '.$sameDirectionFlight->departure_flight_no;
            $pilgrim->blocking_flight_no = $sameDirectionFlight->departure_flight_no;
            $pilgrim->assignment_block_reason = 'On '.$sameDirectionFlight->departure_flight_no.' ('.$flight->direction->label().'). Remove from that flight first.';
        } else {
            $pilgrim->can_assign = true;
            $pilgrim->can_remove = false;
            $pilgrim->assignment_status_label = 'Not on this flight';
            $pilgrim->assignment_block_reason = null;
            $pilgrim->blocking_flight_no = null;
        }

        return $pilgrim;
    }

    /**
     * @param  array<string, mixed>  $filters
     * @return Builder<Pilgrim>
     */
    private function basePilgrimQuery(Flight $flight, array $filters): Builder
    {
        $query = Pilgrim::query()
            ->where('hajj_year', $this->hajjSeasonService->activeYear())
            ->with([
                'company:id,name',
                'podCity:id,name',
                'package:id,name',
                'formOwner:id,name',
            ]);

        $this->applyFilters($query, $filters);

        return $query;
    }

    /**
     * @param  Builder<Pilgrim>  $query
     * @param  array<string, mixed>  $filters
     */
    private function applyFilters(Builder $query, array $filters): void
    {
        if (! empty($filters['company_id'])) {
            $query->where('company_id', (int) $filters['company_id']);
        }

        if (! empty($filters['pod_city_id'])) {
            $query->where('pod_city_id', (int) $filters['pod_city_id']);
        }

        if (! empty($filters['package_id'])) {
            $query->where('package_id', (int) $filters['package_id']);
        }

        if (! empty($filters['form_owner_id'])) {
            $query->where('form_owner_id', (int) $filters['form_owner_id']);
        }

        if (! empty($filters['family_code'])) {
            $query->where('family_code', 'like', '%'.trim((string) $filters['family_code']).'%');
        }

        if (! empty($filters['search'])) {
            $term = trim((string) $filters['search']);

            $query->where(function (Builder $query) use ($term): void {
                $query->where('full_name', 'like', "%{$term}%")
                    ->orWhere('passport_no', 'like', "%{$term}%")
                    ->orWhere('family_code', 'like', "%{$term}%");
            });
        }
    }

    /**
     * @param  Builder<Pilgrim>  $query
     * @param  array<string, mixed>  $filters
     */
    private function applyAssignmentStatusFilter(Builder $query, Flight $flight, array $filters): void
    {
        $status = $filters['assignment_status'] ?? 'all';

        match ($status) {
            'on_flight' => $query->whereHas('flights', fn (Builder $query) => $query->where('flights.id', $flight->id)),
            'not_on_flight' => $query->whereDoesntHave('flights', fn (Builder $query) => $query->where('flights.id', $flight->id)),
            default => null,
        };
    }
}
