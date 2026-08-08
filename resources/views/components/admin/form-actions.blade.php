@props([
    'submit' => 'Save',
    'cancelUrl',
])

<div {{ $attributes->merge(['class' => 'admin-form-actions']) }}>
    <button type="submit" class="btn btn-primary">{{ $submit }}</button>
    <a href="{{ $cancelUrl }}" class="btn btn-light">Cancel</a>
</div>
