<?php

namespace App\Http\Controllers;

use App\Models\Flight;
use App\Models\Pilgrim;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $pilgrimStats = null;
        $flightStats = null;

        if (auth()->user()?->can('pilgrims.view')) {
            $pilgrimStats = [
                'total' => Pilgrim::count(),
                'this_year' => Pilgrim::where('hajj_year', now()->year)->count(),
                'recent' => Pilgrim::with('company')->latest()->limit(5)->get(),

                'monthly_trend' => $this->registrationsByMonth(),
                'by_gender' => $this->countBy('gender'),
                'by_package' => $this->countBy('package_id', 'package', 'name'),
            ];
        }

        if (auth()->user()?->can('flights.view')) {
            $flightStats = [
                'total' => Flight::count(),
                'by_airline' => $this->flightsByAirline(),
                'upcoming' => Flight::with(['departureCity', 'arrivalCity', 'departureAirline'])
                    ->where('departure_date', '>=', now())
                    ->orderBy('departure_date')
                    ->limit(5)
                    ->get(),
            ];
        }

        return view('dashboard', compact('pilgrimStats', 'flightStats'));
    }

    private function registrationsByMonth(): array
    {
        $start = now()->subMonths(5)->startOfMonth();

        $monthExpression = Pilgrim::query()->getConnection()->getDriverName() === 'sqlite'
            ? "strftime('%Y-%m', booking_date)"
            : "DATE_FORMAT(booking_date, '%Y-%m')";

        $counts = Pilgrim::selectRaw("{$monthExpression} as month, COUNT(*) as total")
            ->where('booking_date', '>=', $start)
            ->groupBy('month')
            ->pluck('total', 'month');

        return collect(range(0, 5))
            ->map(fn ($i) => $start->copy()->addMonths($i))
            ->map(fn ($date) => [
                'label' => $date->format('M Y'),
                'total' => (int) ($counts[$date->format('Y-m')] ?? 0),
            ])
            ->all();
    }

    private function countBy(string $column, ?string $relation = null, ?string $labelField = null): array
    {
        $query = Pilgrim::selectRaw("$column, COUNT(*) as total")
            ->whereNotNull($column)
            ->groupBy($column)
            ->orderByDesc('total')
            ->limit(6);

        if ($relation) {
            $query->with($relation);
        }

        return $query->get()->map(fn ($row) => [
            'label' => $relation ? $row->$relation?->$labelField : $row->$column,
            'total' => (int) $row->total,
        ])->all();
    }

    private function flightsByAirline(): array
    {
        return Flight::with('departureAirline')
            ->selectRaw('departure_airline_id, COUNT(*) as total')
            ->whereNotNull('departure_airline_id')
            ->groupBy('departure_airline_id')
            ->orderByDesc('total')
            ->limit(6)
            ->get()
            ->map(fn ($row) => [
                'label' => $row->departureAirline?->name,
                'total' => (int) $row->total,
            ])->all();
    }
}
