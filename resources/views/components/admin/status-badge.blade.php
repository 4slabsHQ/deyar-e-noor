@props([
    'active' => true,
    'label' => null,
])

@php
    $text = $label ?? ($active ? 'Active' : 'Inactive');
@endphp

<span {{ $attributes->merge(['class' => 'badge light badge-'.($active ? 'success' : 'secondary')]) }}>
    {{ $text }}
</span>
