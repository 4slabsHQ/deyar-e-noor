<?php

namespace App\Support;

use Carbon\Carbon;

class AdminDate
{
    public static function display(?string $value): string
    {
        if (! filled($value)) {
            return '';
        }

        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
            return Carbon::createFromFormat('Y-m-d', $value)->format('d/m/Y');
        }

        return $value;
    }

    public static function normalize(?string $value): ?string
    {
        if (! filled($value)) {
            return null;
        }

        $value = trim($value);

        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
            return $value;
        }

        if (preg_match('/^(\d{1,2})[\s\/\-.](\d{1,2})[\s\/\-.](\d{4})$/', $value, $matches)) {
            $day = (int) $matches[1];
            $month = (int) $matches[2];
            $year = (int) $matches[3];

            if ($year >= 1000 && $year <= 9999 && checkdate($month, $day, $year)) {
                return sprintf('%04d-%02d-%02d', $year, $month, $day);
            }
        }

        return $value;
    }

    /**
     * @param  array<string, mixed>  $fields
     * @param  list<string>  $keys
     * @return array<string, mixed>
     */
    public static function normalizeFields(array $fields, array $keys): array
    {
        foreach ($keys as $key) {
            if (array_key_exists($key, $fields)) {
                $fields[$key] = self::normalize(is_string($fields[$key]) ? $fields[$key] : null);
            }
        }

        return $fields;
    }
}
