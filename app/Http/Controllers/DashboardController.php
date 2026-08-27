<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Flight;
use App\Models\FormOwner;
use App\Models\Package;
use App\Models\Pilgrim;
use App\Services\HajjSeasonService;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(HajjSeasonService $hajjSeasonService): View
    {
        $hajjYear = $hajjSeasonService->activeYear();
        $pilgrimStats = null;
        $quotaStats = null;
        $packageStats = null;
        $formOwnerStats = null;
        $flightStats = null;

        if (auth()->user()?->can('pilgrims.view')) {
            $pilgrimStats = [
                'hajj_year' => $hajjYear,
                'this_year' => Pilgrim::query()->where('hajj_year', $hajjYear)->count(),
                'recent' => Pilgrim::query()->with('company')->where('hajj_year', $hajjYear)->latest()->limit(5)->get(),
            ];
        }

        if (auth()->user()?->can('companies.view') || auth()->user()?->can('pilgrims.view')) {
            $quotaStats = $this->companyQuotaStats($hajjYear);
        }

        if (auth()->user()?->can('packages.view') || auth()->user()?->can('pilgrims.view')) {
            $packageStats = $this->packageLimitStats($hajjYear);
        }

        if (auth()->user()?->can('form-owners.view') || auth()->user()?->can('pilgrims.view')) {
            $formOwnerStats = $this->formOwnerLimitStats($hajjYear);
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

        return view('dashboard', compact(
            'pilgrimStats',
            'quotaStats',
            'packageStats',
            'formOwnerStats',
            'flightStats',
            'hajjYear',
        ));
    }

    /** @return array{total_quota: int, entered: int, remaining: int, utilisation_percentage: float, unlimited_count: int, unlimited_label: string, items: list<array{name: string, code: string|null, limit: int, used: int, percentage: float}>} */
    private function companyQuotaStats(int $hajjYear): array
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

        return $this->buildLimitOverview(
            items: $this->mapUtilisationRows($companies, 'quota', fn (Company $company) => [
                'name' => $company->name,
                'code' => $company->code,
            ]),
            unlimitedCount: $unlimitedCompanies,
            unlimitedLabel: 'company',
            limitAttribute: 'quota',
            limitedItems: $companies,
        );
    }

    /** @return array{total_quota: int, entered: int, remaining: int, utilisation_percentage: float, unlimited_count: int, unlimited_label: string, items: list<array{name: string, code: string|null, limit: int, used: int, percentage: float}>} */
    private function packageLimitStats(int $hajjYear): array
    {
        $packages = Package::query()
            ->where('is_active', true)
            ->whereNotNull('limit')
            ->withCount([
                'pilgrims as registered_count' => fn ($query) => $query->where('hajj_year', $hajjYear),
            ])
            ->orderBy('number')
            ->get();

        $unlimitedPackages = Package::query()->where('is_active', true)->whereNull('limit')->count();

        return $this->buildLimitOverview(
            items: $this->mapUtilisationRows($packages, 'limit', fn (Package $package) => [
                'name' => $package->registrationOptionLabel(),
                'code' => null,
            ], sortByPercentage: false),
            unlimitedCount: $unlimitedPackages,
            unlimitedLabel: 'package',
            limitAttribute: 'limit',
            limitedItems: $packages,
        );
    }

    /** @return array{total_quota: int, entered: int, remaining: int, utilisation_percentage: float, unlimited_count: int, unlimited_label: string, items: list<array{name: string, code: string|null, limit: int, used: int, percentage: float}>} */
    private function formOwnerLimitStats(int $hajjYear): array
    {
        $formOwners = FormOwner::query()
            ->where('is_active', true)
            ->whereNotNull('limit')
            ->withCount([
                'pilgrims as registered_count' => fn ($query) => $query->where('hajj_year', $hajjYear),
            ])
            ->orderBy('name')
            ->get();

        $unlimitedFormOwners = FormOwner::query()->where('is_active', true)->whereNull('limit')->count();

        return $this->buildLimitOverview(
            items: $this->mapUtilisationRows($formOwners, 'limit', fn (FormOwner $formOwner) => [
                'name' => $formOwner->name,
                'code' => null,
            ]),
            unlimitedCount: $unlimitedFormOwners,
            unlimitedLabel: 'form owner',
            limitAttribute: 'limit',
            limitedItems: $formOwners,
        );
    }

    /**
     * @param  list<array{name: string, code: string|null, limit: int, used: int, percentage: float}>  $items
     * @param  Collection<int, Company|Package|FormOwner>  $limitedItems
     * @return array{total_quota: int, entered: int, remaining: int, utilisation_percentage: float, unlimited_count: int, unlimited_label: string, items: list<array{name: string, code: string|null, limit: int, used: int, percentage: float}>}
     */
    private function buildLimitOverview(
        array $items,
        int $unlimitedCount,
        string $unlimitedLabel,
        string $limitAttribute,
        Collection $limitedItems,
    ): array {
        $totalLimit = (int) $limitedItems->sum($limitAttribute);
        $entered = (int) $limitedItems->sum('registered_count');

        return [
            'total_quota' => $totalLimit,
            'entered' => $entered,
            'remaining' => max(0, $totalLimit - $entered),
            'utilisation_percentage' => $totalLimit > 0
                ? min(100, round(($entered / $totalLimit) * 100, 1))
                : 0.0,
            'unlimited_count' => $unlimitedCount,
            'unlimited_label' => $unlimitedLabel,
            'items' => $items,
        ];
    }

    /**
     * @param  Collection<int, Company|Package|FormOwner>  $items
     * @param  callable(Company|Package|FormOwner): array{name: string, code: string|null}  $labelResolver
     * @return list<array{name: string, code: string|null, limit: int, used: int, percentage: float}>
     */
    private function mapUtilisationRows(Collection $items, string $limitAttribute, callable $labelResolver, bool $sortByPercentage = true): array
    {
        $rows = $items
            ->map(function ($item) use ($limitAttribute, $labelResolver) {
                $labels = $labelResolver($item);
                $limit = (int) $item->{$limitAttribute};
                $used = (int) $item->registered_count;

                return [
                    'name' => $labels['name'],
                    'code' => $labels['code'],
                    'limit' => $limit,
                    'used' => $used,
                    'percentage' => $limit > 0
                        ? min(100, round(($used / $limit) * 100, 1))
                        : 0.0,
                ];
            });

        if ($sortByPercentage) {
            $rows = $rows->sortByDesc('percentage');
        }

        return $rows
            ->values()
            ->take(10)
            ->all();
    }
}
