<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class FourDigitYearDate implements ValidationRule
{
    /**
     * @param  Closure(string): void  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! filled($value)) {
            return;
        }

        if (! is_string($value) || ! preg_match('/^(?<year>\d{4})-(?<month>\d{2})-(?<day>\d{2})$/', $value, $matches)) {
            $fail('The :attribute must use a 4-digit year (YYYY-MM-DD).');

            return;
        }

        if (! checkdate((int) $matches['month'], (int) $matches['day'], (int) $matches['year'])) {
            $fail('The :attribute must be a valid date.');
        }
    }
}
