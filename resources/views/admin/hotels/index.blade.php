@extends('layouts.app')

@section('title', 'Hotels')
@section('page-title', 'Hotels')

@section('content')
    <x-admin.index-page
        title="Hotels"
        card-title="All Hotels"
        :create-route="route('admin.hotels.create')"
        create-label="New Hotel"
        create-permission="hotels.create"
    >
        <table data-datatable data-empty-message="No hotels yet." class="display" style="width:100%">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Code</th>
                    <th>Rating</th>
                    <th>Country</th>
                    <th>City</th>
                    <th>Phone</th>
                    <th>Status</th>
                    <th class="no-sort">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($hotels as $hotel)
                    <tr>
                        <td class="fw-medium">{{ $hotel->name }}</td>
                        <td>{{ $hotel->code ?? '—' }}</td>
                        <td>
                            @if ($hotel->star_rating)
                                {{ $hotel->star_rating }} <i class="fas fa-star text-warning"></i>
                            @else
                                —
                            @endif
                        </td>
                        <td>{{ $hotel->country->name ?? '—' }}</td>
                        <td>{{ $hotel->city->name ?? '—' }}</td>
                        <td>{{ $hotel->phone ?? '—' }}</td>
                        <td>
                            <x-admin.status-badge :active="$hotel->is_active" />
                        </td>
                        <td>
                            <x-admin.table-actions
                                :edit-route="route('admin.hotels.edit', $hotel)"
                                :delete-route="route('admin.hotels.destroy', $hotel)"
                                edit-permission="hotels.update"
                                delete-permission="hotels.delete"
                                :delete-confirm="'Delete '.$hotel->name.'?'"
                            />
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </x-admin.index-page>
@endsection
