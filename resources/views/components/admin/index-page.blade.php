@props([
    'title',
    'cardTitle' => null,
    'createRoute' => null,
    'createLabel' => null,
    'createPermission' => null,
    'wrapTable' => true,
])

<div class="admin-index-page">
    <div class="admin-index-header d-flex justify-content-between align-items-center mb-4">
        <h4 class="admin-index-title mb-0">{{ $title }}</h4>
        @isset($headerActions)
            {{ $headerActions }}
        @elseif($createRoute)
            @if($createPermission)
                @can($createPermission)
                    <a href="{{ $createRoute }}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i> {{ $createLabel ?? 'Create' }}
                    </a>
                @endcan
            @else
                <a href="{{ $createRoute }}" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i> {{ $createLabel ?? 'Create' }}
                </a>
            @endif
        @endif
    </div>

    <div class="card admin-index-card">
        @if($cardTitle)
            <div class="card-header">
                <h4 class="card-title mb-0">{{ $cardTitle }}</h4>
            </div>
        @endif
        <div class="card-body">
            @if ($wrapTable)
                <div class="table-responsive">
                    {{ $slot }}
                </div>
            @else
                {{ $slot }}
            @endif
        </div>
    </div>
</div>
