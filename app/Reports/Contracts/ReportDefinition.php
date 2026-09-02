<?php

namespace App\Reports\Contracts;

use Illuminate\Support\Collection;

interface ReportDefinition
{
    public function key(): string;

    public function label(): string;

    public function category(): string;

    public function description(): string;

    public function filtersView(): string;

    /**
     * @return array<string, array{label: string, group: string}>
     */
    public function columnCatalog(): array;

    /** @return list<string> */
    public function columnGroupOrder(): array;

    /** @return list<string> */
    public function defaultColumns(): array;

    /** @return list<string> */
    public function nonSpreadsheetExportColumns(): array;

    /** @return list<string> */
    public function frontendOnlyColumns(): array;

    /**
     * @param  list<string>  $columns
     * @return list<string>
     */
    public function validateColumns(array $columns): array;

    /**
     * @param  array<string, mixed>  $input
     * @return array<string, mixed>
     */
    public function normalizeFilters(array $input): array;

    /**
     * @param  array<string, mixed>  $filters
     * @return array<string, mixed>
     */
    public function filterQueryParams(array $filters): array;

    /**
     * @param  array<string, mixed>  $filters
     * @return array<string, mixed>
     */
    public function filterOptions(array $filters): array;

    /** @return list<int> */
    public function availableYears(): array;

    /**
     * @param  array<string, mixed>  $filters
     * @param  list<string>  $columns
     * @return Collection<int, mixed>
     */
    public function records(array $filters, array $columns): Collection;

    /**
     * @param  list<string>  $columns
     * @return list<string>
     */
    public function headings(array $columns): array;

    /**
     * @param  list<string>  $columns
     * @return list<string|int|null>
     */
    public function rowValues(mixed $record, array $columns): array;

    public function exportCellValue(string $column, string|int|null $value): string|int|null;
}
