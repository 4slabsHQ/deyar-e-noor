<?php

namespace App\Http\Controllers\Admin;

use App\Enums\FlightDirection;
use App\Enums\FlightType;
use App\Http\Controllers\Controller;
use App\Http\Requests\BulkFlightAssignmentRequest;
use App\Models\City;
use App\Models\Company;
use App\Models\Flight;
use App\Models\FormOwner;
use App\Models\Package;
use App\Models\Pilgrim;
use App\Services\FlightAssignmentService;
use App\Services\HajjSeasonService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FlightAssignmentController extends Controller
{
    public function index(Request $request, FlightAssignmentService $assignmentService, HajjSeasonService $hajjSeasonService): View
    {
        $direction = $request->enum('direction', FlightDirection::class);
        $flightType = $request->input('flight_type');

        $flights = Flight::query()
            ->forActiveYear()
            ->with([
                'departureCity',
                'departureAirport',
                'arrivalCity',
                'arrivalAirport',
                'viaCity',
            ])
            ->withCount('pilgrims')
            ->when($direction instanceof FlightDirection, fn ($query) => $query->where('direction', $direction))
            ->when(filled($flightType), fn ($query) => $query->where('flight_type', $flightType))
            ->orderBy('departure_date')
            ->orderBy('departure_time')
            ->get();

        $selectedFlight = $request->filled('flight')
            ? Flight::query()->find($request->integer('flight'))
            : null;

        $workspace = $selectedFlight instanceof Flight
            ? $this->workspaceViewData($request, $selectedFlight, $assignmentService, $hajjSeasonService)
            : null;

        return view('admin.flight-assignments.index', [
            'flights' => $flights,
            'direction' => $direction,
            'flightType' => $flightType,
            'hasFilters' => $direction instanceof FlightDirection || filled($flightType),
            'directions' => FlightDirection::cases(),
            'flightTypes' => FlightType::cases(),
            'selectedFlightId' => $selectedFlight?->id,
            'workspace' => $workspace,
        ]);
    }

    public function workspace(Request $request, Flight $flight, FlightAssignmentService $assignmentService, HajjSeasonService $hajjSeasonService): View
    {
        return view('admin.flight-assignments._workspace', $this->workspaceViewData($request, $flight, $assignmentService, $hajjSeasonService));
    }

    public function results(Request $request, Flight $flight, FlightAssignmentService $assignmentService, HajjSeasonService $hajjSeasonService): JsonResponse
    {
        $viewData = $this->resultsViewData($request, $flight, $assignmentService, $hajjSeasonService);

        return response()->json([
            'html' => view('admin.flight-assignments._workspace-results', $viewData)->render(),
            'count' => $viewData['resultsCount'],
        ]);
    }

    public function show(Request $request, Flight $flight): RedirectResponse
    {
        return redirect()->route('admin.flight-assignments.index', array_merge(
            ['flight' => $flight->id],
            $request->query(),
        ));
    }

    public function store(BulkFlightAssignmentRequest $request, Flight $flight, FlightAssignmentService $assignmentService): RedirectResponse
    {
        $filters = $assignmentService->normalizeFilters($request->filters());
        $action = $request->string('action')->toString();

        $pilgrimIds = $request->shouldSelectAll()
            ? $assignmentService->resolvePilgrimIdsForAction($flight, $filters, $action)
            : $request->pilgrimIds();

        $redirect = fn (string $flashKey, string $message): RedirectResponse => redirect()
            ->route('admin.flight-assignments.index', array_merge(
                ['flight' => $flight->id],
                $assignmentService->filterQueryParams($filters),
            ))
            ->with($flashKey, $message);

        if ($pilgrimIds === []) {
            return $redirect('error', $assignmentService->emptySelectionMessage($action));
        }

        if (! $request->shouldSelectAll()) {
            $validationError = $action === 'assign'
                ? $assignmentService->validateAssignSelection($flight, $pilgrimIds)
                : $assignmentService->validateRemoveSelection($flight, $pilgrimIds);

            if ($validationError !== null) {
                return $redirect('error', $validationError);
            }
        }

        $summary = $action === 'assign'
            ? $assignmentService->assign($flight, $pilgrimIds, $request->user())
            : $assignmentService->remove($flight, $pilgrimIds, $request->user());

        return $redirect('success', $assignmentService->assignmentSummaryMessage($action, $summary));
    }

    /** @return array<string, mixed> */
    private function workspaceViewData(
        Request $request,
        Flight $flight,
        FlightAssignmentService $assignmentService,
        HajjSeasonService $hajjSeasonService,
    ): array {
        $flight->load([
            'departureCity',
            'departureAirport',
            'arrivalCity',
            'arrivalAirport',
            'viaCity',
        ])->loadCount('pilgrims');

        $activeYear = $hajjSeasonService->activeYear();
        $filters = $assignmentService->normalizeFilters($request->all());

        return [
            'flight' => $flight,
            'activeYear' => $activeYear,
            'filters' => $filters,
            'pilgrims' => $assignmentService->pilgrimsForAssignment($flight, $filters),
            'filterOptions' => $this->filterOptions($activeYear),
            'workspaceUrl' => route('admin.flight-assignments.workspace', $flight),
            'resultsUrl' => route('admin.flight-assignments.results', $flight),
        ];
    }

    /** @return array<string, mixed> */
    private function resultsViewData(
        Request $request,
        Flight $flight,
        FlightAssignmentService $assignmentService,
        HajjSeasonService $hajjSeasonService,
    ): array {
        $filters = $assignmentService->normalizeFilters($request->all());

        $pilgrims = $assignmentService->pilgrimsForAssignment($flight, $filters);

        return [
            'flight' => $flight,
            'filters' => $filters,
            'pilgrims' => $pilgrims,
            'resultsCount' => $pilgrims->count(),
        ];
    }

    /** @return array<string, mixed> */
    private function filterOptions(int $activeYear): array
    {
        $pilgrimScope = Pilgrim::query()->where('hajj_year', $activeYear);

        $companyIds = (clone $pilgrimScope)->distinct()->pluck('company_id')->filter();
        $podCityIds = (clone $pilgrimScope)->distinct()->pluck('pod_city_id')->filter();
        $formOwnerIds = (clone $pilgrimScope)->distinct()->pluck('form_owner_id')->filter();

        return [
            'companies' => Company::query()->whereIn('id', $companyIds)->orderBy('name')->get(['id', 'name', 'munazzam_code']),
            'podCities' => City::query()->whereIn('id', $podCityIds)->orderBy('name')->get(['id', 'name']),
            'packages' => Package::query()->forYear($activeYear)->where('is_active', true)->orderBy('number')->get(),
            'formOwners' => FormOwner::query()->whereIn('id', $formOwnerIds)->orderBy('name')->get(['id', 'name']),
        ];
    }
}
