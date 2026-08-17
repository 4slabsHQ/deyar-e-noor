<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreHajjSeasonRequest;
use App\Models\HajjSeason;
use App\Services\HajjSeasonService;

class HajjSeasonController extends Controller
{
    public function index()
    {
        $seasons = HajjSeason::query()
            ->with('activator')
            ->orderByDesc('year')
            ->get();

        return view('admin.hajj-seasons.index', compact('seasons'));
    }

    public function store(StoreHajjSeasonRequest $request, HajjSeasonService $hajjSeasonService)
    {
        $hajjSeasonService->create((int) $request->validated('year'));

        return redirect()
            ->route('admin.hajj-seasons.index')
            ->with('success', 'Hajj season created successfully.');
    }

    public function activate(HajjSeason $hajjSeason, HajjSeasonService $hajjSeasonService)
    {
        $hajjSeasonService->activate($hajjSeason);

        return redirect()
            ->route('admin.hajj-seasons.index')
            ->with('success', "Hajj {$hajjSeason->year} is now the active season.");
    }
}
