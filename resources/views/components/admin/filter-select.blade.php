@props([
    'name',
    'label',
    'selected' => '',
    'id' => null,
    'column' => 'col-lg-2 col-md-4',
    'searchable' => true,
])

@php
    $fieldId = $id ?? $name;
@endphp

<div class="{{ $column }}">
    <label for="{{ $fieldId }}" class="admin-form-label">{{ $label }}</label>
    <select
        name="{{ $name }}"
        id="{{ $fieldId }}"
        data-placeholder="All"
        {{ $attributes->class([
            'form-control',
            'js-filter-select' => $searchable,
            'js-searchable-select' => $searchable,
        ]) }}
    >
        <option value="" @selected(! filled($selected))>All</option>
        {{ $slot }}
    </select>
</div>
