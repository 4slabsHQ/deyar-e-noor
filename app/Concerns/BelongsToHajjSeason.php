<?php

namespace App\Concerns;

use App\Services\HajjSeasonService;
use Illuminate\Database\Eloquent\Builder;

trait BelongsToHajjSeason
{
    /** @param  Builder<static>  $query */
    public function scopeForYear(Builder $query, int $year): Builder
    {
        return $query->where($this->getTable().'.hajj_year', $year);
    }

    /** @param  Builder<static>  $query */
    public function scopeForActiveYear(Builder $query): Builder
    {
        return $query->forYear(app(HajjSeasonService::class)->activeYear());
    }
}
