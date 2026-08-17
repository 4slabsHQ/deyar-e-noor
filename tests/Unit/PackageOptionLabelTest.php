<?php

use App\Enums\PackageDuration;
use App\Models\Package;

test('package registration option label includes key package details', function () {
    $package = Package::factory()->make([
        'number' => 'PKG-001',
        'name' => 'Economy',
        'price' => 850000,
        'days' => 21,
        'duration' => PackageDuration::Long,
        'qurbani_included' => true,
    ]);

    expect($package->registrationOptionLabel())
        ->toBe('PKG-001 — Economy | 850,000.00 | 21 days | Long | Qurbani included');
});

test('package registration option label shows when qurbani is not included', function () {
    $package = Package::factory()->make([
        'number' => 'PKG-002',
        'name' => 'Standard',
        'price' => 650000,
        'days' => 14,
        'duration' => PackageDuration::Short,
        'qurbani_included' => false,
    ]);

    expect($package->registrationOptionLabel())
        ->toContain('No qurbani')
        ->toContain('14 days')
        ->toContain('Short');
});
