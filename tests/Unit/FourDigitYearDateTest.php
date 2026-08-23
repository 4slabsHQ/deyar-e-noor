<?php

use App\Rules\FourDigitYearDate;

test('four digit year date rule accepts valid dates', function () {
    $rule = new FourDigitYearDate;
    $failed = false;

    $rule->validate('dob', '1975-03-15', function () use (&$failed) {
        $failed = true;
    });

    expect($failed)->toBeFalse();
});

test('four digit year date rule rejects six digit years', function () {
    $rule = new FourDigitYearDate;
    $message = null;

    $rule->validate('dob', '202456-03-15', function (string $error) use (&$message) {
        $message = $error;
    });

    expect($message)->toContain('4-digit year');
});

test('four digit year date rule rejects invalid calendar dates', function () {
    $rule = new FourDigitYearDate;
    $message = null;

    $rule->validate('dob', '2024-02-31', function (string $error) use (&$message) {
        $message = $error;
    });

    expect($message)->toContain('valid date');
});
