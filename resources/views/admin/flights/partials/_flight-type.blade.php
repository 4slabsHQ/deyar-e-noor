@php
    use App\Enums\FlightType;
@endphp

<x-hajj.form-section title="Flight Type">
    <div class="row compact g-2">
        <div class="col-lg-3 col-md-4 col-6">
            <label class="form-label" for="flight_type">Direct / Indirect <span class="text-danger">*</span></label>
            <select name="flight_type"
                    id="flight_type"
                    class="form-control @error('flight_type') is-invalid @enderror"
                    data-flight-type
                    required>
                @foreach (FlightType::cases() as $type)
                    <option value="{{ $type->value }}" @selected($selectedType === $type->value)>
                        {{ $type->label() }}
                    </option>
                @endforeach
            </select>
            @error('flight_type')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
</x-hajj.form-section>
