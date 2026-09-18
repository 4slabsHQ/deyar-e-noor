@extends('layouts.app')

@section('title', 'Hajj Registration')
@section('page-title', 'Hajj Registration')

@push('styles')
    <link href="{{ asset('css/pilgrim-registration.css') }}?v=15" rel="stylesheet">
@endpush

@section('content')
    <div class="pilgrim-view-page">
        <div class="pilgrim-doc-toolbar no-print mb-4">
            <a href="{{ route('admin.pilgrims.index') }}" class="btn btn-light">
                <i class="fas fa-arrow-left me-1"></i> Back to List
            </a>

            <div class="btn-group">
                <a href="{{ route('admin.pilgrims.print', $pilgrim) }}" target="_blank" rel="noopener" class="btn btn-primary">
                    <i class="fas fa-print me-1"></i> Print
                </a>
                <a href="{{ route('admin.pilgrims.print', $pilgrim) }}" target="_blank" rel="noopener" class="btn btn-outline-primary">
                    <i class="fas fa-file-pdf me-1"></i> Save as PDF
                </a>
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
</script>
@endpush
