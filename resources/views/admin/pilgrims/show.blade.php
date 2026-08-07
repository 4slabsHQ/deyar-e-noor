@extends('layouts.app')

@section('title', 'Hajj Registration')
@section('page-title', 'Hajj Registration')

@push('styles')
    <link href="{{ asset('css/pilgrim-registration.css') }}" rel="stylesheet">
@endpush

@section('content')
    <div class="pilgrim-view-page">
        <div class="pilgrim-doc-toolbar no-print mb-4">
            <a href="{{ route('admin.pilgrims.index') }}" class="btn btn-light">
                <i class="fas fa-arrow-left me-1"></i> Back to List
            </a>

            <div class="btn-group">
                <button type="button" class="btn btn-primary" onclick="printRegistration()">
                    <i class="fas fa-print me-1"></i> Print
                </button>
                <button type="button" class="btn btn-outline-primary" onclick="printRegistration()">
                    <i class="fas fa-file-pdf me-1"></i> Save as PDF
                </button>
                @can('pilgrims.update')
                    <a href="{{ route('admin.pilgrims.edit', $pilgrim) }}" class="btn btn-outline-secondary">
                        <i class="fas fa-pencil-alt me-1"></i> Edit
                    </a>
                @endcan
            </div>
        </div>

        @include('admin.pilgrims._registration-document', ['pilgrim' => $pilgrim])
    </div>
@endsection

@push('scripts')
<script>
    document.title = @json($pilgrim->family_code.' — '.$pilgrim->full_name);

    function printRegistration() {
        const source = document.querySelector('.pilgrim-view-page .pilgrim-registration-doc');

        if (!source) {
            window.print();
            return;
        }

        const existing = document.getElementById('pilgrim-print-root');
        if (existing) {
            existing.remove();
        }

        const printRoot = document.createElement('div');
        printRoot.id = 'pilgrim-print-root';
        printRoot.appendChild(source.cloneNode(true));

        document.body.appendChild(printRoot);
        document.body.classList.add('is-printing-pilgrim');

        window.print();
    }

    window.addEventListener('afterprint', function () {
        document.body.classList.remove('is-printing-pilgrim');

        const printRoot = document.getElementById('pilgrim-print-root');
        if (printRoot) {
            printRoot.remove();
        }
    });
</script>
@endpush
