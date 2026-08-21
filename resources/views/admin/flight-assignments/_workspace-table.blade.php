        <div class="flight-assignment-bulk-bar mb-3 d-none align-items-center gap-2 flex-wrap" id="flight-assignment-bulk-bar">
            <span class="text-muted small" id="flight-assignment-selected-count">0 selected</span>
            <button type="submit" class="btn btn-primary btn-sm" data-bulk-action="assign" disabled data-bulk-submit>
                Assign selected
            </button>
            <button type="submit" class="btn btn-outline-danger btn-sm" data-bulk-action="remove" disabled data-bulk-submit>
                Remove selected
            </button>
        </div>

        <div class="admin-index-table-wrap flight-assignment-hujaj-table-wrap">
            <table data-datatable
                   data-scroll-x="true"
                   data-empty-message="No hujaj match the current filters."
                   class="display flight-assignment-hujaj-table"
                   style="width:100%">
                <thead>
                    <tr>
                        <th class="no-sort assignment-check-col">
                            <input type="checkbox"
                                   class="form-check-input select-all-checkbox"
                                   aria-label="Select all actionable hujaj on this page"
                                   title="Select all hujaj that can be assigned or removed">
                        </th>
                        <th>Name</th>
                        <th>Passport</th>
                        <th>Family</th>
                        <th>Company</th>
                        <th>POD</th>
                        <th>Package</th>
                        <th class="no-sort assignment-status-col">Flight status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($pilgrims as $pilgrim)
                        <tr @class(['flight-assignment-row-blocked' => ! $pilgrim->can_assign && ! $pilgrim->can_remove])>
                            <td class="assignment-check-col">
                                @if ($pilgrim->can_assign || $pilgrim->can_remove)
                                    <input type="checkbox"
                                           class="form-check-input pilgrim-checkbox"
                                           value="{{ $pilgrim->id }}"
                                           data-can-assign="{{ $pilgrim->can_assign ? '1' : '0' }}"
                                           data-can-remove="{{ $pilgrim->can_remove ? '1' : '0' }}">
                                @else
                                    <span class="text-muted" aria-hidden="true">—</span>
                                @endif
                            </td>
                            <td class="fw-medium">{{ $pilgrim->full_name }}</td>
                            <td>{{ $pilgrim->passport_no }}</td>
                            <td>{{ $pilgrim->family_code }}</td>
                            <td>{{ $pilgrim->company?->name ?? '—' }}</td>
                            <td>{{ $pilgrim->podCity?->name ?? '—' }}</td>
                            <td>{{ $pilgrim->package?->name ?? '—' }}</td>
                            <td class="assignment-status-col">
                                <div class="flight-assignment-status">
                                    @if ($pilgrim->can_remove)
                                        <span class="badge light badge-success">{{ $pilgrim->assignment_status_label }}</span>
                                    @elseif ($pilgrim->can_assign)
                                        <span class="badge light badge-secondary">{{ $pilgrim->assignment_status_label }}</span>
                                    @else
                                        <span class="badge light badge-warning">{{ $pilgrim->assignment_status_label }}</span>
                                        <p class="flight-assignment-status-note mb-0">{{ $pilgrim->assignment_block_reason }}</p>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
