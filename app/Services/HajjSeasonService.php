<?php

namespace App\Services;

use App\Enums\HajjSeasonStatus;
use App\Models\HajjSeason;
use Illuminate\Support\Facades\DB;

class HajjSeasonService
{
    public function activeYear(): int
    {
        return $this->active()?->year
            ?? (int) (config('hajj.default_active_year') ?? now()->year);
    }

    public function active(): ?HajjSeason
    {
        return HajjSeason::query()
            ->where('status', HajjSeasonStatus::Active)
            ->first();
    }

    public function create(int $year): HajjSeason
    {
        return HajjSeason::query()->firstOrCreate(
            ['year' => $year],
            ['status' => HajjSeasonStatus::Archived],
        );
    }

    public function activate(HajjSeason $season): HajjSeason
    {
        return DB::transaction(function () use ($season): HajjSeason {
            HajjSeason::query()
                ->where('status', HajjSeasonStatus::Active)
                ->whereKeyNot($season->id)
                ->update(['status' => HajjSeasonStatus::Archived]);

            $season->update([
                'status' => HajjSeasonStatus::Active,
                'activated_at' => now(),
                'activated_by' => auth()->id(),
            ]);

            return $season->fresh();
        });
    }
}
