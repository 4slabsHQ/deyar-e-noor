@props([
    'packages',
    'selected' => '',
    'name' => 'package_id',
    'id' => null,
    'placeholder' => 'Select package',
    'emptyLabel' => 'Select',
    'qurbaniData' => false,
    'filterMode' => false,
])

@php
    $fieldId = $id ?? $name;
    $hasSelection = filled($selected);
    $emptyLabel = $filterMode ? 'All' : $emptyLabel;
    $placeholder = $filterMode ? 'All' : $placeholder;
@endphp

<div class="col-lg-4 col-md-6">
    <label class="form-label" for="{{ $fieldId }}">Package</label>
    <select
        name="{{ $name }}"
        id="{{ $fieldId }}"
        data-placeholder="{{ $placeholder }}"
        @if ($filterMode) data-keep-empty-label="true" @endif
        {{ $attributes->class([
            'form-control',
            'js-searchable-select',
            'js-filter-select' => $filterMode,
        ]) }}
    >
        <option value="" @selected(! $hasSelection)>{{ $emptyLabel }}</option>
        @foreach ($packages as $package)
            <option
                value="{{ $package->id }}"
                @if ($qurbaniData) data-qurbani="{{ $package->qurbani_included ? '1' : '0' }}" @endif
                @selected((string) $selected === (string) $package->id)
            >{{ $package->registrationOptionLabel() }}</option>
        @endforeach
    </select>
    {{ $slot }}
</div>
