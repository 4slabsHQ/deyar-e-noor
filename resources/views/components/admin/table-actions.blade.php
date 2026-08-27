@props([
    'viewRoute' => null,
    'editRoute' => null,
    'deleteRoute' => null,
    'deletePreviewRoute' => null,
    'assignRoute' => null,
    'viewTitle' => 'View',
    'assignTitle' => 'Assign hujaj',
    'deleteConfirm' => 'Delete this record?',
    'viewPermission' => null,
    'editPermission' => null,
    'deletePermission' => null,
    'assignPermission' => null,
])

<div {{ $attributes->merge(['class' => 'admin-table-actions d-flex']) }}>
    @if($viewRoute)
        @if($viewPermission)
            @can($viewPermission)
                <a href="{{ $viewRoute }}" class="btn btn-info shadow btn-xs sharp me-1" title="{{ $viewTitle }}">
                    <i class="fas fa-eye"></i>
                </a>
            @endcan
        @else
            <a href="{{ $viewRoute }}" class="btn btn-info shadow btn-xs sharp me-1" title="{{ $viewTitle }}">
                <i class="fas fa-eye"></i>
            </a>
        @endif
    @endif

    @if($editRoute)
        @if($editPermission)
            @can($editPermission)
                <a href="{{ $editRoute }}" class="btn btn-warning shadow btn-xs sharp me-1" title="Edit">
                    <i class="fas fa-pencil-alt"></i>
                </a>
            @endcan
        @else
            <a href="{{ $editRoute }}" class="btn btn-warning shadow btn-xs sharp me-1" title="Edit">
                <i class="fas fa-pencil-alt"></i>
            </a>
        @endif
    @endif

    @if($assignRoute)
        @if($assignPermission)
            @can($assignPermission)
                <a href="{{ $assignRoute }}" class="btn btn-primary shadow btn-xs sharp me-1" title="{{ $assignTitle }}">
                    <i class="fas fa-user-check"></i>
                </a>
            @endcan
        @else
            <a href="{{ $assignRoute }}" class="btn btn-primary shadow btn-xs sharp me-1" title="{{ $assignTitle }}">
                <i class="fas fa-user-check"></i>
            </a>
        @endif
    @endif

    @if($deleteRoute)
        @if($deletePermission)
            @can($deletePermission)
                <form action="{{ $deleteRoute }}" method="POST" @if($deletePreviewRoute) data-pilgrim-delete-form @else onsubmit="return confirm(@js($deleteConfirm))" @endif>
                    @csrf
                    @method('DELETE')
                    <button type="{{ $deletePreviewRoute ? 'button' : 'submit' }}"
                            class="btn btn-danger shadow btn-xs sharp"
                            title="Delete"
                            @if($deletePreviewRoute)
                                data-pilgrim-delete-trigger
                                data-preview-url="{{ $deletePreviewRoute }}"
                            @endif>
                        <i class="fa fa-trash"></i>
                    </button>
                </form>
            @endcan
        @else
            <form action="{{ $deleteRoute }}" method="POST" @if($deletePreviewRoute) data-pilgrim-delete-form @else onsubmit="return confirm(@js($deleteConfirm))" @endif>
                @csrf
                @method('DELETE')
                <button type="{{ $deletePreviewRoute ? 'button' : 'submit' }}"
                        class="btn btn-danger shadow btn-xs sharp"
                        title="Delete"
                        @if($deletePreviewRoute)
                            data-pilgrim-delete-trigger
                            data-preview-url="{{ $deletePreviewRoute }}"
                        @endif>
                    <i class="fa fa-trash"></i>
                </button>
            </form>
        @endif
    @endif
</div>
