@props([
    'user',
    'size' => 32,
    'class' => '',
])

@php
    $photoUrl = $user?->photo_url ?? asset('images/user.jpg');
@endphp

<img
    src="{{ $photoUrl }}"
    width="{{ $size }}"
    height="{{ $size }}"
    alt="{{ $user?->name ?? 'User' }}"
    {{ $attributes->class(['user-avatar', $class]) }}
>
