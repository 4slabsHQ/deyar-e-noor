<?php

namespace Database\Factories\Concerns;

use App\Services\HajjSeasonService;

trait UsesActiveHajjYear
{
    protected function activeHajjYear(): int
    {
        try {
            return app(HajjSeasonService::class)->activeYear();
        } catch (\Throwable) {
            return (int) date('Y');
        }
    }
}
