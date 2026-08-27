@if (! empty($summaryStats))
    <div class="report-summary-stats mb-4">
        <div class="deyar-quota-overview__stats">
            @foreach ($summaryStats as $stat)
                <div @class([
                    'deyar-quota-stat-card',
                    'deyar-quota-stat-card--total' => ($stat['variant'] ?? '') === 'total',
                    'deyar-quota-stat-card--entered' => ($stat['variant'] ?? '') === 'entered',
                    'deyar-quota-stat-card--remaining' => ($stat['variant'] ?? '') === 'remaining',
                ])>
                    <span class="deyar-quota-stat-card__label">{{ $stat['label'] }}</span>
                    <span class="deyar-quota-stat-card__value">{{ number_format((int) $stat['value']) }}</span>
                </div>
            @endforeach
        </div>
    </div>
@endif
