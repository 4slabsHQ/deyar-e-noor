<?php

namespace App\Http\Controllers\Admin;

use App\Enums\RoutePointType;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRouteRequest;
use App\Http\Requests\UpdateRouteRequest;
use App\Models\Airport;
use App\Models\City;
use App\Models\Route;
use Illuminate\Support\Facades\DB;

class RouteController extends Controller
{
    public function index()
    {
        $routes = Route::query()
            ->forActiveYear()
            ->with(['steps.airport', 'steps.city'])
            ->orderBy('name')
            ->get();

        return view('admin.routes.index', compact('routes'));
    }

    public function create()
    {
        return view('admin.routes.create', $this->formData());
    }

    public function store(StoreRouteRequest $request)
    {
        DB::transaction(function () use ($request): void {
            $route = Route::create($this->withActiveHajjYear($request->safe()->except('steps')));
            $this->syncSteps($route, $request->input('steps', []));
        });

        return redirect()->route('admin.routes.index')->with('success', 'Route created successfully.');
    }

    public function edit(Route $route)
    {
        $route->load(['steps.airport', 'steps.city']);

        return view('admin.routes.edit', array_merge(
            compact('route'),
            $this->formData(),
        ));
    }

    public function update(UpdateRouteRequest $request, Route $route)
    {
        DB::transaction(function () use ($request, $route): void {
            $route->update($request->safe()->except('steps'));
            $this->syncSteps($route, $request->input('steps', []));
        });

        return redirect()->route('admin.routes.index')->with('success', 'Route updated successfully.');
    }

    public function destroy(Route $route)
    {
        return $this->deleteOrBack(
            $route,
            'admin.routes.index',
            'Route deleted successfully.',
        );
    }

    /** @return array<string, mixed> */
    private function formData(): array
    {
        return [
            'airports' => Airport::query()->with('city')->where('is_active', true)->orderBy('name')->get(),
            'cities' => City::query()->where('is_active', true)->orderBy('name')->get(),
        ];
    }

    /** @param  list<array<string, mixed>>  $steps */
    private function syncSteps(Route $route, array $steps): void
    {
        $route->steps()->delete();

        foreach (array_values($steps) as $index => $step) {
            $pointType = RoutePointType::from((string) $step['point_type']);

            $route->steps()->create([
                'sequence' => $index + 1,
                'point_type' => $pointType,
                'airport_id' => $pointType === RoutePointType::Airport ? (int) $step['airport_id'] : null,
                'city_id' => $pointType === RoutePointType::City ? (int) $step['city_id'] : null,
            ]);
        }
    }
}
