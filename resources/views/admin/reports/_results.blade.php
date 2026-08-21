<div class="card admin-index-card" data-report-title="{{ $reportLabel }}">
    @php
        $printData = [
            'title' => $reportLabel,
            'meta' => number_format($result['total']).' rows',
            'headings' => $result['headings'],
            'rows' => $result['rows'],
        ];
    @endphp
    <script type="application/json" class="report-print-data">{!! json_encode($printData, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) !!}</script>
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div class="d-flex align-items-center flex-wrap gap-2">
            <h4 class="card-title mb-0">Results</h4>
            <span class="text-muted small">{{ number_format($result['total']) }} rows</span>
        </div>
        <div class="d-flex flex-wrap gap-2">
            @can('reports.export')
                <a href="{{ route('admin.reports.export.excel', $exportQuery) }}" class="btn btn-outline-primary btn-sm">Excel</a>
                <a href="{{ route('admin.reports.export.pdf', $exportQuery) }}" class="btn btn-outline-primary btn-sm">PDF</a>
            @endcan
            <button type="button" class="btn btn-outline-secondary btn-sm" data-report-print>Print</button>
        </div>
    </div>
    <div class="card-body">
        <div class="admin-index-table-wrap report-results-table-wrap">
            <table data-datatable
                   data-scroll-x="true"
                   data-empty-message="No rows match the current filters."
                   class="display report-results-table"
                   style="width:100%">
                <thead>
                    <tr>
                        @foreach ($result['headings'] as $heading)
                            <th>{{ $heading }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach ($result['rows'] as $row)
                        <tr>
                            @foreach ($row as $cell)
                                <td>{{ $cell ?? '—' }}</td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
