<?php

use App\Services\ReportBuilderService;

it('prepends serial numbers to report headings and rows', function () {
    $builder = app(ReportBuilderService::class);

    $result = $builder->appendSerialNumbers([
        'headings' => ['Full Name', 'Company'],
        'rows' => [
            ['Alice', 'Acme'],
            ['Bob', 'Beta'],
        ],
        'total' => 2,
        'columns' => ['full_name', 'company'],
        'filters' => ['hajj_year' => 1447],
    ]);

    expect($result['headings'])->toBe(['S.No.', 'Full Name', 'Company'])
        ->and($result['rows'])->toBe([
            ['1', 'Alice', 'Acme'],
            ['2', 'Bob', 'Beta'],
        ]);
});

it('orders grouped columns by the provided group order', function () {
    $builder = app(ReportBuilderService::class);

    $groups = $builder->orderedColumnGroups([
        'comments' => ['label' => 'Comments', 'group' => 'Comments'],
        'gender' => ['label' => 'Gender', 'group' => 'Personal Details'],
        'company' => ['label' => 'Company', 'group' => 'Registration'],
    ], [
        'Registration',
        'Personal Details',
        'Comments',
    ]);

    expect(array_keys($groups))->toBe(['Registration', 'Personal Details', 'Comments'])
        ->and($groups['Registration'][0]['key'])->toBe('company')
        ->and($groups['Personal Details'][0]['key'])->toBe('gender');
});
