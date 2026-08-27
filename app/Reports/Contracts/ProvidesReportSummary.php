<?php

namespace App\Reports\Contracts;

interface ProvidesReportSummary
{
    /**
     * @param  array<string, mixed>  $filters
     * @return list<array{label: string, value: int|string, variant?: string}>
     */
    public function summaryStats(array $filters): array;
}
