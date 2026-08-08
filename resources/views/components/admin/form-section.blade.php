@props(['title'])

<section {{ $attributes->merge(['class' => 'admin-form-section']) }}>
    <h5 class="admin-form-section-title">{{ $title }}</h5>
    {{ $slot }}
</section>
