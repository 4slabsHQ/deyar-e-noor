<?php

namespace App\Http\Controllers;

use App\Models\Pilgrim;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $pilgrimStats = null;

        if (auth()->user()?->can('pilgrims.view')) {
            $currentYear = (int) now()->year;

            $pilgrimStats = [
                'total' => Pilgrim::query()->count(),
                'this_year' => Pilgrim::query()->where('hajj_year', $currentYear)->count(),
                'recent' => Pilgrim::query()
                    ->with('company')
                    ->latest()
                    ->limit(5)
                    ->get(),
            ];
        }

        return view('dashboard', [
            'pilgrimStats' => $pilgrimStats,
        ]);
    }
}
