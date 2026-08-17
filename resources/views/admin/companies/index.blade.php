@extends('layouts.app')

@section('title', 'Companies')

@section('page-title', 'Companies')

@section('content')
    <x-admin.index-page
        title="Companies"
        card-title="All Companies"
        :create-route="route('admin.companies.create')"
        create-label="New Company"
        create-permission="companies.create"
    >
        <table data-datatable data-empty-message="No companies yet." class="display" style="width:100%">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Code</th>
                    <th>ENR No</th>
                    <th>Munazzam Code</th>
                    <th>Quota</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($companies as $company)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                @if ($company->logo)
                                    <img src="{{ Storage::url($company->logo) }}"
                                         class="rounded me-2" width="32" height="32"
                                         style="object-fit: cover;">
                                @endif
                                <div>
                                    <span class="fw-medium">{{ $company->name }}</span>
                                    @if ($company->legal_name)
                                        <br><small class="text-muted">{{ $company->legal_name }}</small>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>{{ $company->code ?? '—' }}</td>
                        <td>{{ $company->enr_number ?? '—' }}</td>
                        <td>{{ $company->munazzam_code ?? '—' }}</td>
                        <td>{{ $company->quota ?? 'Unlimited' }}</td>
                        <td>
                            <x-admin.status-badge :active="$company->is_active" />
                        </td>
                        <td>
                            <x-admin.table-actions
                                :edit-route="route('admin.companies.edit', $company)"
                                :delete-route="route('admin.companies.destroy', $company)"
                                edit-permission="companies.edit"
                                delete-permission="companies.destroy"
                                :delete-confirm="'Delete '.$company->name.'?'"
                            />
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </x-admin.index-page>
@endsection
