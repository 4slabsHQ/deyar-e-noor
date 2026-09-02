<?php

namespace App\Http\Controllers\Concerns;

use App\Services\HajjSeasonService;

trait AssignsActiveHajjYear
{
    protected function activeHajjYear(): int
    {
        return app(HajjSeasonService::class)->activeYear();
    }

    /** @param  array<string, mixed>  $data */
    protected function withActiveHajjYear(array $data): array
    {
        $data['hajj_year'] = $this->activeHajjYear();

        return $data;
    }
}
