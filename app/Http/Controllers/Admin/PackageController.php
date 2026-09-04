<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePackageRequest;
use App\Http\Requests\UpdatePackageRequest;
use App\Models\AccommodationPlan;
use App\Models\Package;
use App\Models\Route;

class PackageController extends Controller
{
    public function index()
    {
        $packages = Package::query()->forActiveYear()->orderBy('number')->get();

        return view('admin.packages.index', compact('packages'));
    }

    public function create()
    {
        $accommodationPlans = AccommodationPlan::query()->forActiveYear()->where('is_active', true)->orderBy('name')->get();
        $routes = Route::query()->forActiveYear()->where('is_active', true)->with(['steps.airport', 'steps.city'])->orderBy('name')->get();

        return view('admin.packages.create', compact('accommodationPlans', 'routes'));
    }

    public function store(StorePackageRequest $request)
    {
        Package::create($this->withActiveHajjYear($request->validated()));

        return redirect()->route('admin.packages.index')->with('success', 'Package created successfully.');
    }

    public function edit(Package $package)
    {
        $accommodationPlans = AccommodationPlan::query()->forActiveYear()->where('is_active', true)->orderBy('name')->get();
        $routes = Route::query()->forActiveYear()->where('is_active', true)->with(['steps.airport', 'steps.city'])->orderBy('name')->get();

        return view('admin.packages.edit', compact('package', 'accommodationPlans', 'routes'));
    }

    public function update(UpdatePackageRequest $request, Package $package)
    {
        $package->update($request->validated());

        return redirect()->route('admin.packages.index')->with('success', 'Package updated successfully.');
    }

    public function destroy(Package $package)
    {
        return $this->deleteOrBack(
            $package,
            'admin.packages.index',
            'Package deleted successfully.',
        );
    }
}
