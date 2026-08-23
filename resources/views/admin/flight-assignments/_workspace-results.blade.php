<form method="POST"
      action="{{ route('admin.flight-assignments.store', $flight) }}"
      class="flight-assignment-bulk-form"
      id="flight-assignment-bulk-form">
    @csrf
    <input type="hidden" name="action" value="assign" id="bulk-action-input">
    <input type="hidden" name="select_all" value="0" class="select-all-input">
    @foreach ($filters as $key => $value)
        @if(filled($value))
            <input type="hidden" name="{{ $key }}" value="{{ $value }}" data-bulk-filter-input>
        @endif
    @endforeach

    @include('admin.flight-assignments._workspace-table')
</form>
