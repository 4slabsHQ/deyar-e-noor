@extends('layouts.app')

@section('title', 'Maktab Categories')
@section('page-title', 'Maktab Categories')

@section('content')
    <x-admin.index-page
        title="Maktab Categories"
        card-title="All Maktab Categories"
        :create-route="route('admin.maktab-categories.create')"
        create-label="New Maktab Category"
        create-permission="maktab-categories.create"
    >
        <table data-datatable data-empty-message="No maktab categories yet." class="display" style="width:100%">
            <thead>
                <tr>
                    <th>Category</th>
                    <th>Zone</th>
                    <th>Status</th>
                    <th class="no-sort">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($maktabCategories as $maktabCategory)
                    <tr>
                        <td class="fw-medium">{{ $maktabCategory->name }}</td>
                        <td>{{ $maktabCategory->zone }}</td>
                        <td>
                            <x-admin.status-badge :active="$maktabCategory->is_active" />
                        </td>
                        <td>
                            <x-admin.table-actions
                                :edit-route="route('admin.maktab-categories.edit', $maktabCategory)"
                                :delete-route="route('admin.maktab-categories.destroy', $maktabCategory)"
                                edit-permission="maktab-categories.update"
                                delete-permission="maktab-categories.delete"
                                :delete-confirm="'Delete '.$maktabCategory->name.'?'"
                            />
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </x-admin.index-page>
@endsection
