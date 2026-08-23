<?php

use App\Models\Company;

test('company registration option label includes munazzam code', function () {
    $company = Company::factory()->make([
        'name' => 'Deyar-e-Noor',
        'code' => 'DYN',
        'munazzam_code' => 'MZ-DYN-100',
    ]);

    expect($company->registrationOptionLabel())
        ->toBe('Deyar-e-Noor (MZ-DYN-100)');
});

test('company registration option label shows only name when munazzam is missing', function () {
    $company = Company::factory()->make([
        'name' => 'Deyar-e-Noor',
        'code' => 'DYN',
        'munazzam_code' => null,
    ]);

    expect($company->registrationOptionLabel())
        ->toBe('Deyar-e-Noor');
});
