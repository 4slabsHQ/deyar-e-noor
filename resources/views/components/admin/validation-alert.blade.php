@if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show admin-validation-alert" role="alert" id="admin-validation-alert">
        <strong class="admin-validation-alert__title">Could not save — please review:</strong>
        <ul class="admin-validation-alert__list mb-0 mt-2">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@push('scripts')
    @if ($errors->any())
        <script>
            document.getElementById('admin-validation-alert')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
        </script>
    @endif
@endpush
