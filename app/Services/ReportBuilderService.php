<?php

namespace App\Services;

use App\Reports\Contracts\ReportDefinition;

class ReportBuilderService
{
    /**
     * @param  list<string>  $columns
     * @param  array<string, mixed>  $filters
     * @return array{
     *     headings: list<string>,
     *     rows: list<list<string|int|null>>,
     *     total: int,
     *     columns: list<string>,
     *     filters: array<string, mixed>
     * }
     */
    public function build(ReportDefinition $definition, array $columns, array $filters): array
    {
        $records = $definition->records($filters, $columns);

        return [
            'headings' => $definition->headings($columns),
            'rows' => $records
                ->map(fn (mixed $record): array => $definition->rowValues($record, $columns))
                ->values()
                ->all(),
            'total' => $records->count(),
            'columns' => $columns,
            'filters' => $filters,
        ];
    }

    /**
     * @param  array{
     *     headings: list<string>,
     *     rows: list<list<string|int|null>>,
     *     total: int,
     *     columns: list<string>,
     *     filters: array<string, mixed>
     * }  $result
     * @return array{
     *     headings: list<string>,
     *     rows: list<list<string|int|null>>,
     *     total: int,
     *     columns: list<string>,
     *     filters: array<string, mixed>
     * }
     */
    public function appendSerialNumbers(array $result): array
    {
        $result['headings'] = array_merge(['S.No.'], $result['headings']);
        $result['rows'] = array_map(
            fn (array $row, int $index): array => array_merge([(string) ($index + 1)], $row),
            $result['rows'],
            array_keys($result['rows']),
        );

        return $result;
    }

    /**
     * @param  array<string, array{label: string, group: string}>  $catalog
     * @return array<string, list<array{key: string, label: string}>>
     */
    public function groupedColumns(array $catalog): array
    {
        $grouped = [];

        foreach ($catalog as $key => $meta) {
            $grouped[$meta['group']][] = [
                'key' => $key,
                'label' => $meta['label'],
            ];
        }

        return $grouped;
    }

    /**
     * @param  array<string, array{label: string, group: string}>  $catalog
     * @param  list<string>  $groupOrder
     * @return array<string, list<array{key: string, label: string}>>
     */
    public function orderedColumnGroups(array $catalog, array $groupOrder): array
    {
        $grouped = $this->groupedColumns($catalog);
        $ordered = [];

        foreach ($groupOrder as $group) {
            if (isset($grouped[$group])) {
                $ordered[$group] = $grouped[$group];
                unset($grouped[$group]);
            }
        }

        foreach ($grouped as $group => $columns) {
            $ordered[$group] = $columns;
        }

        return $ordered;
    }
}
