@props([
    'name',
    'label' => 'Flight No',
    'airlineSelectId',
    'prefixId',
    'inputId' => null,
    'value' => '',
    'placeholder' => 'e.g. 740',
    'required' => false,
])

@php
    $inputId = $inputId ?? $name;
@endphp

<div {{ $attributes->merge(['class' => 'col-lg-2 col-md-3 col-6']) }}>
    <label class="form-label" for="{{ $inputId }}">
        {{ $label }} @if($required)<span class="text-danger">*</span>@endif
    </label>
    <div class="flight-number-group">
        <span class="flight-number-prefix"
              id="{{ $prefixId }}"
              data-flight-number-prefix
              data-airline-select="{{ $airlineSelectId }}">—</span>
        <input type="text"
               name="{{ $name }}"
               id="{{ $inputId }}"
               value="{{ $value }}"
               class="form-control flight-number-input @error($name) is-invalid @enderror"
               placeholder="{{ $placeholder }}"
               maxlength="10"
               @if($required) required @endif>
    </div>
    @error($name)
        <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror
</div>
