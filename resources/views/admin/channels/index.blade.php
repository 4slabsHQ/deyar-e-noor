@extends('layouts.app')

@section('title', 'Channels')
@section('page-title', 'Channels')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fs-20 font-w700 mb-0">Channels</h4>
        @can('channels.create')
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#channelModal"
                    onclick="openCreateChannelModal()">
                <i class="fas fa-plus me-1"></i> New Channel
            </button>
        @endcan
    </div>

    <div class="card">
        <div class="card-header">
            <h4 class="card-title">All Channels</h4>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table data-datatable data-empty-message="No channels yet." class="display" style="width:100%">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Code</th>
                            <th>Status</th>
                            <th class="no-sort">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($channels as $channel)
                            <tr>
                                <td class="fw-medium">{{ $channel->name }}</td>
                                <td>{{ $channel->code ?? '—' }}</td>
                                <td>
                                    <span class="badge light badge-{{ $channel->is_active ? 'success' : 'secondary' }}">
                                        {{ $channel->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex">
                                        @can('channels.update')
                                            <button type="button" class="btn btn-primary shadow btn-xs sharp me-1"
                                                    data-bs-toggle="modal" data-bs-target="#channelModal"
                                                    data-id="{{ $channel->id }}"
                                                    data-name="{{ $channel->name }}"
                                                    data-code="{{ $channel->code }}"
                                                    data-is-active="{{ $channel->is_active ? '1' : '0' }}"
                                                    data-update-url="{{ route('admin.channels.update', $channel) }}"
                                                    onclick="openEditChannelModal(this)">
                                                <i class="fas fa-pencil-alt"></i>
                                            </button>
                                        @endcan
                                        @can('channels.delete')
                                            <form action="{{ route('admin.channels.destroy', $channel) }}"
                                                  method="POST"
                                                  onsubmit="return confirm('Delete {{ $channel->name }}?')">
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

    {{-- Single modal, reused for both Create and Edit --}}
    <div class="modal fade" id="channelModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="channelForm" method="POST">
                    @csrf
                    <input type="hidden" name="_method" id="channelMethod" value="POST">
                    <input type="hidden" name="record_id" id="record_id" value="{{ old('record_id') }}">

                    <div class="modal-header">
                        <h5 class="modal-title" id="channelModalTitle">Create Channel</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name" value="{{ old('name') }}"
                                   class="form-control @error('name') is-invalid @enderror"
                                   placeholder="e.g. Facebook, Website, Walk-in" required>
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Code</label>
                            <input type="text" name="code" id="code" value="{{ old('code') }}"
                                   class="form-control @error('code') is-invalid @enderror">
                            @error('code') <div class="invalid-feedback">{{ $message }}</div> @enderror
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
        function openCreateChannelModal() {
            document.getElementById('channelForm').reset();
            document.getElementById('record_id').value = '';
            document.getElementById('channelModalTitle').innerText = 'Create Channel';
            document.getElementById('channelMethod').value = 'POST';
            document.getElementById('channelForm').action = '{{ route('admin.channels.store') }}';
            document.getElementById('is_active').checked = true;
        }

        function openEditChannelModal(btn) {
            document.getElementById('channelModalTitle').innerText = 'Edit Channel';
            document.getElementById('channelMethod').value = 'PUT';
            document.getElementById('channelForm').action = btn.dataset.updateUrl;
            document.getElementById('record_id').value = btn.dataset.id;
            document.getElementById('name').value = btn.dataset.name;
            document.getElementById('code').value = btn.dataset.code ?? '';
            document.getElementById('is_active').checked = btn.dataset.isActive === '1';
        }

        // If validation failed, reopen the modal in the correct mode with old input already filled in by Blade
        @if ($errors->any())
            document.addEventListener('DOMContentLoaded', function () {
                const recordId = document.getElementById('record_id').value;
                document.getElementById('channelModalTitle').innerText = recordId ? 'Edit Channel' : 'Create Channel';
                document.getElementById('channelMethod').value = recordId ? 'PUT' : 'POST';
                document.getElementById('channelForm').action = recordId
                    ? '{{ url('admin/channels') }}/' + recordId
                    : '{{ route('admin.channels.store') }}';
                new bootstrap.Modal(document.getElementById('channelModal')).show();
            });
        @endif
    </script>
    @endpush
@endsection