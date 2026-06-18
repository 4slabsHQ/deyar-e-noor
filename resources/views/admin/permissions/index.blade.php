@extends('layouts.app')

@section('title', 'Permissions')
@section('page-title', 'Permissions')

@section('content')
<div class="card">
    <div class="card-header">
        <h4 class="card-title">All Permissions</h4>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table data-datatable class="display" style="width:100%">
                <thead>
                    <tr><th>Permission Name</th></tr>
                </thead>
                <tbody>
                    @foreach ($permissions as $permission)
                        <tr><td>{{ $permission->name }}</td></tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection