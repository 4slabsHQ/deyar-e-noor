<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\RunReportRequest;
use App\Http\Requests\SaveReportColumnsRequest;
use App\Reports\Contracts\ReportDefinition;
use App\Reports\ReportRegistry;
use App\Services\HajjSeasonService;
use App\Services\ReportBuilderService;
use App\Services\ReportExportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\View\View;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function index(ReportRegistry $registry): RedirectResponse
    {
        return redirect()->route('admin.reports.show', $registry->defaultKey());
    }

    public function show(
        string $report,
        RunReportRequest $request,
        ReportRegistry $registry,
        ReportBuilderService $builder,
        HajjSeasonService $hajjSeasonService,
    ): View {
        $definition = $this->resolveDefinition($registry, $report);
        $filters = $definition->normalizeFilters($request->filters());
        $columns = $request->shouldRun()
            ? $request->selectedColumns()
            : $this->resolveInitialColumns($request, $definition);

        $result = $request->shouldRun()
            ? $builder->build($definition, $columns, $filters)
            : null;

        $columnCatalog = $definition->columnCatalog();

        return view('admin.reports.show', [
            'reportKey' => $definition->key(),
            'reportLabel' => $definition->label(),
            'columnOptions' => collect($columnCatalog)
                ->map(fn (array $column, string $key): array => [
                    'key' => $key,
                    'label' => $column['label'],
                ])
                ->sortBy('label')
                ->values()
                ->all(),
            'selectedColumns' => $columns,
            'filters' => $filters,
            'activeYear' => $hajjSeasonService->activeYear(),
            'availableYears' => $definition->availableYears(),
            'filterOptions' => $definition->filterOptions($filters),
            'hasFilters' => $this->hasAppliedFilters($filters, $hajjSeasonService->activeYear()),
            'result' => $result,
            'exportQuery' => $result
                ? $this->exportQuery($definition, $columns, $filters)
                : [],
        ]);
    }

    public function saveColumns(SaveReportColumnsRequest $request): RedirectResponse
    {
        $request->user()->saveReportColumns(
            $request->reportKey(),
            $request->validatedColumns(),
        );

        return redirect()
            ->route('admin.reports.show', $request->reportKey())
            ->with('status', 'column-defaults-saved');
    }

    public function results(
        string $report,
        RunReportRequest $request,
        ReportRegistry $registry,
        ReportBuilderService $builder,
    ): JsonResponse {
        $definition = $this->resolveDefinition($registry, $report);
        $filters = $definition->normalizeFilters($request->filters());
        $columns = $request->selectedColumns();
        $result = $builder->build($definition, $columns, $filters);
        $viewData = $this->resultViewData($definition, $columns, $filters, $result);

        return response()->json([
            'html' => view('admin.reports._results', $viewData)->render(),
        ]);
    }

    public function exportExcel(
        RunReportRequest $request,
        ReportRegistry $registry,
        ReportBuilderService $builder,
        ReportExportService $exportService,
    ): Response {
        return $this->export($request, $registry, $builder, $exportService, 'excel');
    }

    public function exportPdf(
        RunReportRequest $request,
        ReportRegistry $registry,
        ReportBuilderService $builder,
        ReportExportService $exportService,
    ): Response {
        return $this->export($request, $registry, $builder, $exportService, 'pdf');
    }

    public function exportCsv(
        RunReportRequest $request,
        ReportRegistry $registry,
        ReportBuilderService $builder,
        ReportExportService $exportService,
    ): StreamedResponse {
        $definition = $registry->get($request->reportKey());
        $filters = $definition->normalizeFilters($request->filters());
        $columns = $request->selectedColumns();
        $result = $builder->build($definition, $columns, $filters);
        $filename = sprintf('%s-%s.csv', $definition->key(), $filters['hajj_year']);

        return $exportService->toCsv($result['headings'], $result['rows'], $filename);
    }

    private function export(
        RunReportRequest $request,
        ReportRegistry $registry,
        ReportBuilderService $builder,
        ReportExportService $exportService,
        string $format,
    ): Response {
        $definition = $registry->get($request->reportKey());
        $filters = $definition->normalizeFilters($request->filters());
        $columns = $request->selectedColumns();
        $result = $builder->build($definition, $columns, $filters);
        $title = $definition->label().' — Hajj '.$filters['hajj_year'];
        $filename = sprintf('%s-%s.%s', $definition->key(), $filters['hajj_year'], $format === 'pdf' ? 'pdf' : 'xls');

        return $format === 'pdf'
            ? $exportService->toPdf($title, $result['headings'], $result['rows'], $filename)
            : $exportService->toExcel($title, $result['headings'], $result['rows'], $filename);
    }

    private function resolveDefinition(ReportRegistry $registry, string $report): ReportDefinition
    {
        try {
            return $registry->get($report);
        } catch (InvalidArgumentException) {
            abort(404);
        }
    }

    /** @return list<string> */
    private function resolveInitialColumns(RunReportRequest $request, ReportDefinition $definition): array
    {
        if ($request->has('columns')) {
            try {
                return $definition->validateColumns($request->input('columns', []));
            } catch (InvalidArgumentException) {
                //
            }
        }

        $savedColumns = $request->user()?->reportColumns($definition->key());

        if ($savedColumns !== null) {
            try {
                return $definition->validateColumns($savedColumns);
            } catch (InvalidArgumentException) {
                //
            }
        }

        return $definition->defaultColumns();
    }

    /** @param  array<string, mixed>  $filters */
    private function exportQuery(ReportDefinition $definition, array $columns, array $filters): array
    {
        return array_merge(
            [
                'report' => $definition->key(),
                'columns' => $columns,
            ],
            $definition->filterQueryParams($filters),
        );
    }

    /**
     * @param  list<string>  $columns
     * @param  array<string, mixed>  $filters
     * @param  array<string, mixed>  $result
     * @return array{result: array<string, mixed>, exportQuery: array<string, mixed>}
     */
    private function resultViewData(
        ReportDefinition $definition,
        array $columns,
        array $filters,
        array $result,
    ): array {
        return [
            'result' => $result,
            'exportQuery' => $this->exportQuery($definition, $columns, $filters),
            'reportLabel' => $definition->label(),
        ];
    }

    /** @param  array<string, mixed>  $filters */
    private function hasAppliedFilters(array $filters, int $activeYear): bool
    {
        foreach ($filters as $key => $value) {
            if ($key === 'hajj_year') {
                continue;
            }

            if (filled($value)) {
                return true;
            }
        }

        return (int) ($filters['hajj_year'] ?? $activeYear) !== $activeYear;
    }
}
