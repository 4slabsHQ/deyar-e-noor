@extends('layouts.app')

@section('title', 'Campaigns')
@section('page-title', 'Campaigns')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fs-20 font-w700 mb-0">Campaigns</h4>
        @can('campaigns.create')
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#campaignModal"
                    onclick="openCreateCampaignModal()">
                <i class="fas fa-plus me-1"></i> New Campaign
            </button>
        @endcan
    </div>

    <div class="card">
        <div class="card-header">
            <h4 class="card-title">All Campaigns</h4>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table data-datatable data-empty-message="No campaigns yet." class="display" style="width:100%">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Channel</th>
                            <th>Start</th>
                            <th>End</th>
                            <th>Budget</th>
                            <th>Status</th>
                            <th class="no-sort">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($campaigns as $campaign)
                            <tr>
                                <td class="fw-medium">{{ $campaign->name }}</td>
                                <td>{{ $campaign->channel->name ?? '—' }}</td>
                                <td>{{ $campaign->start_date?->format('d M Y') ?? '—' }}</td>
                                <td>{{ $campaign->end_date?->format('d M Y') ?? '—' }}</td>
                                <td>{{ $campaign->budget ? number_format($campaign->budget, 2) : '—' }}</td>
                                <td>
                                    <span class="badge light badge-{{ $campaign->is_active ? 'success' : 'secondary' }}">
                                        {{ $campaign->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex">
                                        @can('campaigns.update')
                                            <button type="button" class="btn btn-primary shadow btn-xs sharp me-1"
                                                    data-bs-toggle="modal" data-bs-target="#campaignModal"
                                                    data-id="{{ $campaign->id }}"
                                                    data-name="{{ $campaign->name }}"
                                                    data-code="{{ $campaign->code }}"
                                                    data-channel-id="{{ $campaign->channel_id }}"
                                                    data-start-date="{{ $campaign->start_date?->format('Y-m-d') }}"
                                                    data-end-date="{{ $campaign->end_date?->format('Y-m-d') }}"
                                                    data-budget="{{ $campaign->budget }}"
                                                    data-is-active="{{ $campaign->is_active ? '1' : '0' }}"
                                                    data-update-url="{{ route('admin.campaigns.update', $campaign) }}"
                                                    onclick="openEditCampaignModal(this)">
                                                <i class="fas fa-pencil-alt"></i>
                                            </button>
                                        @endcan
                                        @can('campaigns.delete')
                                            <form action="{{ route('admin.campaigns.destroy', $campaign) }}"
                                                  method="POST"
                                                  onsubmit="return confirm('Delete {{ $campaign->name }}?')">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-danger shadow btn-xs sharp">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="campaignModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="campaignForm" method="POST">
                    @csrf
                    <input type="hidden" name="_method" id="campaignMethod" value="POST">
                    <input type="hidden" name="record_id" id="record_id" value="{{ old('record_id') }}">

                    <div class="modal-header">
                        <h5 class="modal-title" id="campaignModalTitle">Create Campaign</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name" value="{{ old('name') }}"
                                   class="form-control @error('name') is-invalid @enderror" required>
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Code</label>
                            <input type="text" name="code" id="code" value="{{ old('code') }}"
                                   class="form-control @error('code') is-invalid @enderror">
                            @error('code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Channel</label>
                            <select name="channel_id" id="channel_id" class="form-control @error('channel_id') is-invalid @enderror">
                                <option value="">-- Select Channel --</option>
                                @foreach ($channels as $channel)
                                    <option value="{{ $channel->id }}" {{ (string) old('channel_id') === (string) $channel->id ? 'selected' : '' }}>
                                        {{ $channel->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('channel_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="row">
                            <div class="col-6 mb-3">
                                <label class="form-label">Start Date</label>
                                <input type="date" name="start_date" id="start_date" value="{{ old('start_date') }}"
                                       class="form-control @error('start_date') is-invalid @enderror">
                                @error('start_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-6 mb-3">
                                <label class="form-label">End Date</label>
                                <input type="date" name="end_date" id="end_date" value="{{ old('end_date') }}"
                                       class="form-control @error('end_date') is-invalid @enderror">
                                @error('end_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Budget</label>
                            <input type="number" step="0.01" min="0" name="budget" id="budget" value="{{ old('budget') }}"
                                   class="form-control @error('budget') is-invalid @enderror">
                            @error('budget') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="form-check form-switch">
                            <input type="hidden" name="is_active" value="0">
                            <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active"
                                   {{ old('is_active', true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">Active</label>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function openCreateCampaignModal() {
            document.getElementById('campaignForm').reset();
            document.getElementById('record_id').value = '';
            document.getElementById('campaignModalTitle').innerText = 'Create Campaign';
            document.getElementById('campaignMethod').value = 'POST';
            document.getElementById('campaignForm').action = '{{ route('admin.campaigns.store') }}';
            document.getElementById('is_active').checked = true;
        }

        function openEditCampaignModal(btn) {
            document.getElementById('campaignModalTitle').innerText = 'Edit Campaign';
            document.getElementById('campaignMethod').value = 'PUT';
            document.getElementById('campaignForm').action = btn.dataset.updateUrl;
            document.getElementById('record_id').value = btn.dataset.id;
            document.getElementById('name').value = btn.dataset.name;
            document.getElementById('code').value = btn.dataset.code ?? '';
            document.getElementById('channel_id').value = btn.dataset.channelId ?? '';
            document.getElementById('start_date').value = btn.dataset.startDate ?? '';
            document.getElementById('end_date').value = btn.dataset.endDate ?? '';
            document.getElementById('budget').value = btn.dataset.budget ?? '';
            document.getElementById('is_active').checked = btn.dataset.isActive === '1';
        }

        @if ($errors->any())
            document.addEventListener('DOMContentLoaded', function () {
                const recordId = document.getElementById('record_id').value;
                document.getElementById('campaignModalTitle').innerText = recordId ? 'Edit Campaign' : 'Create Campaign';
                document.getElementById('campaignMethod').value = recordId ? 'PUT' : 'POST';
                document.getElementById('campaignForm').action = recordId
                    ? '{{ url('admin/campaigns') }}/' + recordId
                    : '{{ route('admin.campaigns.store') }}';
                new bootstrap.Modal(document.getElementById('campaignModal')).show();
            });
        @endif
    </script>
    @endpush
@endsection