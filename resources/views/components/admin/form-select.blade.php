@props([
    'name',
    'id' => null,
    'required' => false,
    'placeholder' => 'Select',
    'searchable' => false,
    'value' => null,
])

@php
    $fieldId = $id ?? $name;
    $selected = old($name, $value);
@endphp

<select
    name="{{ $name }}"
    id="{{ $fieldId }}"
    @if($required) required @endif
    @if($searchable) data-placeholder="{{ $placeholder }}" @endif
    {{ $attributes->class([
        'form-control',
        'js-searchable-select' => $searchable,
        'is-invalid' => $errors->has($name),
    ]) }}
>
    @if(! $searchable)
        <option value="" disabled @selected($selected === null || $selected === '')>{{ $placeholder }}</option>
    @endif
    {{ $slot }}
</select>
@error($name)
    <div class="invalid-feedback">{{ $message }}</div>
@enderror
