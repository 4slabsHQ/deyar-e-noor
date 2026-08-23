@props([
    'name',
    'id' => null,
    'value' => '',
    'required' => false,
    'min' => '1900-01-01',
    'max' => '2099-12-31',
])

@php
    $inputId = $id ?? $name;
    $isoValue = old($name, $value);
@endphp

<input type="date"
       name="{{ $name }}"
       id="{{ $inputId }}"
       value="{{ $isoValue }}"
       min="{{ $min }}"
       max="{{ $max }}"
       {{ $attributes->merge(['class' => 'form-control'.($errors->has($name) ? ' is-invalid' : '')]) }}
       @if ($required) required @endif>

@error($name)
    <div class="invalid-feedback d-block">{{ $message }}</div>
@enderror
