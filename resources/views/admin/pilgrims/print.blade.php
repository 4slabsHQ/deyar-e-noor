@extends('layouts.print')

@section('title', $pilgrim->family_code.' — '.$pilgrim->full_name)

@section('content')
    @include('admin.pilgrims._registration-document', ['pilgrim' => $pilgrim])
@endsection

@push('scripts')
<script>
    window.addEventListener('load', function () {
        window.setTimeout(function () {
            window.print();
        }, 300);
    });
</script>
@endpush
