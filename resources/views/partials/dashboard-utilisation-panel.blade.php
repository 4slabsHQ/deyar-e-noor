@props([
    'title',
    'stats',
    'emptyMessage',
])

<div class="card h-100 deyar-panel-card">
    <div class="card-header">
        <h6 class="deyar-panel-card__title mb-0">{{ $title }}</h6>
    </div>
    <div class="card-body">
        @forelse ($stats['items'] as $item)
            <div class="deyar-quota-item">
                <div class="deyar-quota-item__header">
                    <span class="deyar-quota-item__name">
                        {{ $item['name'] }}
                        @if ($item['code'])
                            <span class="text-muted">({{ $item['code'] }})</span>
                        @endif
                    </span>
                    <span class="deyar-quota-item__meta">
                        {{ number_format($item['used']) }}/{{ number_format($item['limit']) }}
                    </span>
                </div>
                <div class="deyar-quota-progress" role="progressbar"
                     aria-valuenow="{{ $item['percentage'] }}" aria-valuemin="0" aria-valuemax="100"
                     aria-label="{{ $item['name'] }} utilisation">
                    <div class="deyar-quota-progress__fill {{ $item['percentage'] >= 100 ? 'deyar-quota-progress__fill--danger' : ($item['percentage'] >= 80 ? 'deyar-quota-progress__fill--warning' : '') }}"
                         style="width: {{ $item['percentage'] }}%"></div>
                </div>
            </div>
        @empty
            <div class="deyar-empty-state border-0">{{ $emptyMessage }}</div>
        @endforelse
    </div>
</div>
