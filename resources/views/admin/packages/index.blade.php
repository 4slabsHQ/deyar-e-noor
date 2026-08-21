@extends('layouts.app')

@section('title', 'Packages')
@section('page-title', 'Packages')

@section('content')
    <x-admin.index-page
        title="Packages"
        card-title="All Packages"
        :create-route="route('admin.packages.create')"
        create-label="New Package"
        create-permission="packages.create"
    >
        <table data-datatable data-empty-message="No packages yet." class="display" style="width:100%">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Name</th>
                    <th>Price</th>
                    <th>Days</th>
                    <th>Qurbani</th>
                    <th>Duration</th>
                    <th>Limit</th>
                    <th>Status</th>
                    <th class="no-sort">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($packages as $package)
                    <tr>
                        <td class="fw-medium">{{ $package->number }}</td>
                        <td>{{ $package->name }}</td>
                        <td>{{ number_format($package->price, 2) }}</td>
                        <td>{{ $package->days }}</td>
                        <td>
                            @if ($package->qurbani_included)
                                <span class="badge light badge-success">Yes</span>
                            @else
                                <span class="badge light badge-secondary">No</span>
                            @endif
                        </td>
                        <td>{{ $package->duration->label() }}</td>
                        <td>{{ $package->limit ?? 'Unlimited' }}</td>
                        <td>
                            <x-admin.status-badge :active="$package->is_active" />
                        </td>
                        <td>
                            <x-admin.table-actions
                                :edit-route="route('admin.packages.edit', $package)"
                                :delete-route="route('admin.packages.destroy', $package)"
                                edit-permission="packages.update"
                                delete-permission="packages.delete"
                                :delete-confirm="'Delete '.$package->name.'?'"
                            />
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </x-admin.index-page>
@endsection
