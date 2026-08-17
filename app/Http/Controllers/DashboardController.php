<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Flight;
use App\Models\Pilgrim;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $hajjYear = (int) now()->year;
        $pilgrimStats = null;
        $quotaStats = null;
        $flightStats = null;

        if (auth()->user()?->can('pilgrims.view')) {
            $pilgrimStats = [
                'hajj_year' => $hajjYear,
                'this_year' => Pilgrim::query()->where('hajj_year', $hajjYear)->count(),
                'recent' => Pilgrim::query()->with('company')->latest()->limit(5)->get(),
                'monthly_trend' => $this->registrationsByMonth(),
            ];
        }

        if (auth()->user()?->can('companies.view') || auth()->user()?->can('pilgrims.view')) {
            $quotaStats = $this->quotaStats($hajjYear);
        }

        if (auth()->user()?->can('flights.view')) {
            $flightStats = [
                'upcoming' => Flight::query()
                    ->with(['departureCity', 'arrivalCity', 'departureAirline'])
                    ->withCount('pilgrims')
                    ->where('departure_date', '>=', now())
                    ->orderBy('departure_date')
                    ->limit(5)
                    ->get(),
            ];
        }

        return view('dashboard', compact('pilgrimStats', 'quotaStats', 'flightStats', 'hajjYear'));
    }

    /** @return array{total_quota: int, utilised: int, remaining: int, utilisation_percentage: float, unlimited_companies: int, companies: list<array{name: string, code: string|null, quota: int, used: int, percentage: float}>} */
    private function quotaStats(int $hajjYear): array
    {
        $companies = Company::query()
            ->active()
            ->whereNotNull('quota')
            ->withCount([
                'pilgrims as registered_count' => fn ($query) => $query->where('hajj_year', $hajjYear),
            ])
            ->orderBy('name')
            ->get();

        $unlimitedCompanies = Company::query()->active()->whereNull('quota')->count();

        $companyRows = $companies
            ->map(fn (Company $company) => [
                'name' => $company->name,
                'code' => $company->code,
                'quota' => (int) $company->quota,
                'used' => (int) $company->registered_count,
                'percentage' => $company->quota > 0
                    ? min(100, round(((int) $company->registered_count / (int) $company->quota) * 100, 1))
                    : 0.0,
            ])
            ->sortByDesc('percentage')
            ->values()
            ->take(10)
            ->all();

        $totalQuota = (int) $companies->sum('quota');
        $utilised = (int) $companies->sum('registered_count');

        return [
            'total_quota' => $totalQuota,
            'utilised' => $utilised,
            'remaining' => max(0, $totalQuota - $utilised),
            'utilisation_percentage' => $totalQuota > 0
                ? min(100, round(($utilised / $totalQuota) * 100, 1))
                : 0.0,
            'unlimited_companies' => $unlimitedCompanies,
            'companies' => $companyRows,
        ];
    }

    /** @return list<array{label: string, total: int}> */
    private function registrationsByMonth(): array
    {
        $start = now()->subMonths(5)->startOfMonth();

        $monthExpression = Pilgrim::query()->getConnection()->getDriverName() === 'sqlite'
            ? "strftime('%Y-%m', booking_date)"
            : "DATE_FORMAT(booking_date, '%Y-%m')";

        $counts = Pilgrim::query()
            ->selectRaw("{$monthExpression} as month, COUNT(*) as total")
            ->where('booking_date', '>=', $start)
            ->groupBy('month')
            ->pluck('total', 'month');

        return collect(range(0, 5))
            ->map(fn (int $i) => $start->copy()->addMonths($i))
            ->map(fn ($date) => [
                'label' => $date->format('M Y'),
                'total' => (int) ($counts[$date->format('Y-m')] ?? 0),
            ])
            ->all();
    }
}
