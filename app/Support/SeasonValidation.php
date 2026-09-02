<?php

namespace App\Support;

use App\Services\HajjSeasonService;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Exists;
use Illuminate\Validation\Rules\Unique;

class SeasonValidation
{
    public static function activeYear(): int
    {
        return app(HajjSeasonService::class)->activeYear();
    }

    public static function unique(string $table, string $column, mixed $ignore = null): Unique
    {
        $rule = Rule::unique($table, $column)
            ->where('hajj_year', self::activeYear());

        if ($ignore !== null) {
            $rule->ignore($ignore);
        }

        return $rule;
    }

    public static function exists(string $table, string $column = 'id'): Exists
    {
        return Rule::exists($table, $column)
            ->where('hajj_year', self::activeYear());
    }

    public static function existsActive(string $table, string $column = 'id'): Exists
    {
        return self::exists($table, $column)
            ->where('is_active', true);
    }
}
