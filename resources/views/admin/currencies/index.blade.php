@extends('layouts.app')

@section('title', 'Currencies')
@section('page-title', 'Currencies')

@section('content')
    <x-admin.index-page
        title="Currencies"
        card-title="All Currencies"
        :create-route="route('admin.currencies.create')"
        create-label="New Currency"
        create-permission="currencies.create"
    >
        <table data-datatable data-empty-message="No currencies yet." class="display" style="width:100%">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Code</th>
                    <th>Symbol</th>
                    <th>Exchange Rate</th>
                    <th>Default</th>
                    <th>Status</th>
                    <th class="no-sort">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($currencies as $currency)
                    <tr>
                        <td class="fw-medium">{{ $currency->name }}</td>
                        <td>{{ $currency->code }}</td>
                        <td>{{ $currency->symbol ?? '—' }}</td>
                        <td>{{ number_format($currency->exchange_rate, 6) }}</td>
                        <td>
                            @if ($currency->is_default)
                                <span class="badge light badge-primary">Default</span>
                            @else
                                —
                            @endif
                        </td>
                        <td>
                            <x-admin.status-badge :active="$currency->is_active" />
                        </td>
                        <td>
                            <x-admin.table-actions
                                :edit-route="route('admin.currencies.edit', $currency)"
                                :delete-route="route('admin.currencies.destroy', $currency)"
                                edit-permission="currencies.update"
                                delete-permission="currencies.delete"
                                :delete-confirm="'Delete '.$currency->name.'?'"
                            />
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </x-admin.index-page>
@endsection
