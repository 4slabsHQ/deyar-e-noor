@extends('layouts.app')

@section('title', 'Accommodation Plans')
@section('page-title', 'Accommodation Plans')

@section('content')
    <x-admin.index-page
        title="Accommodation Plans"
        card-title="All Accommodation Plans"
        :create-route="route('admin.accommodation-plans.create')"
        create-label="New Plan"
        create-permission="accommodation-plans.create"
    >
        <table data-datatable data-empty-message="No accommodation plans yet." class="display" style="width:100%">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Type</th>
                    <th>Properties</th>
                    <th>Status</th>
                    <th class="no-sort">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($accommodationPlans as $plan)
                    <tr>
                        <td class="fw-medium">{{ $plan->name }}</td>
                        <td>{{ $plan->type->label() }}</td>
                        <td>
                            @foreach ($plan->slots as $slot)
                                <div>{{ $slot->slot->label() }}: {{ $slot->property->name }}@if($slot->akad) ({{ $slot->akad->akad_number }})@endif</div>
                            @endforeach
                        </td>
                        <td><x-admin.status-badge :active="$plan->is_active" /></td>
                        <td>
                            <x-admin.table-actions
                                :edit-route="route('admin.accommodation-plans.edit', $plan)"
                                :delete-route="route('admin.accommodation-plans.destroy', $plan)"
                                edit-permission="accommodation-plans.update"
                                delete-permission="accommodation-plans.delete"
                                :delete-confirm="'Delete '.$plan->name.'?'"
                            />
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </x-admin.index-page>
@endsection
