<div class="card admin-index-card" data-default-report-title="{{ $defaultReportTitle }}">
    @php
        $printData = [
            'title' => $defaultReportTitle,
            'meta' => number_format($result['total']).' rows',
            'headings' => $printResult['headings'],
            'rows' => $printResult['rows'],
            'columnKeys' => array_merge(['serial'], $printResult['columns']),
        ];
        $documentColumns = $documentColumns ?? [];
    @endphp
    <script type="application/json" class="report-print-data">{!! json_encode($printData, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) !!}</script>
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div class="d-flex align-items-center flex-wrap gap-2">
            <h4 class="card-title mb-0">Results</h4>
            <span class="text-muted small">{{ number_format($result['total']) }} rows</span>
        </div>
        <div class="d-flex flex-wrap gap-2">
            @can('reports.export')
                <a href="{{ route('admin.reports.export.excel', $exportQuery) }}" class="btn btn-outline-primary btn-sm" data-report-export>Excel</a>
                <a href="{{ route('admin.reports.export.pdf', $exportQuery) }}" class="btn btn-outline-primary btn-sm" data-report-export>PDF</a>
            @endcan
            <button type="button" class="btn btn-outline-secondary btn-sm" data-report-print>Print</button>
        </div>
    </div>
    <div class="card-body">
        @includeWhen(isset($summaryStats), 'admin.reports._summary-stats', ['summaryStats' => $summaryStats ?? null])

        <div class="admin-index-table-wrap report-results-table-wrap">
            <table data-datatable
                   data-scroll-x="true"
                   data-empty-message="No rows match the current filters."
                   class="display report-results-table"
                   style="width:100%">
                <thead>
                    <tr>
                        @foreach ($result['headings'] as $heading)
                            <th @class([
                                'no-sort report-serial-column' => $heading === 'S.No.',
                                'no-sort report-picture-column' => $heading === 'Picture',
                            ])>{{ $heading }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach ($result['rows'] as $row)
                        <tr>
                            @foreach ($row as $index => $cell)
                                @php
                                    $columnKey = $index === 0 ? 'serial' : ($result['columns'][$index - 1] ?? null);
                                @endphp
                                <td @class([
                                    'report-serial-column' => $columnKey === 'serial',
                                    'report-picture-column' => $columnKey === 'picture',
                                ])>
                                    @if ($columnKey === 'picture')
                                        @if (filled($cell))
                                            <img src="{{ $cell }}"
                                                 alt=""
                                                 class="report-pilgrim-photo rounded"
                                                 width="40"
                                                 height="40">
                                        @else
                                            <span class="report-pilgrim-photo-placeholder" aria-hidden="true">—</span>
                                        @endif
                                    @elseif (in_array($columnKey, $documentColumns, true))
                                        @if (filled($cell))
                                            <div class="report-document-actions d-flex">
                                                <a href="{{ $cell }}"
                                                   target="_blank"
                                                   rel="noopener noreferrer"
                                                   class="btn btn-info shadow btn-xs sharp me-1"
                                                   title="View">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ $cell }}"
                                                   download
                                                   class="btn btn-primary shadow btn-xs sharp"
                                                   title="Download">
                                                    <i class="fas fa-download"></i>
                                                </a>
                                            </div>
                                        @else
                                            —
                                        @endif
                                    @else
                                        {{ $cell ?? '—' }}
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
