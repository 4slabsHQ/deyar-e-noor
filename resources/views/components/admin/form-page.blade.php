@props([
    'title',
    'action',
    'method' => 'POST',
    'cancelUrl',
    'submitLabel' => 'Save',
    'enctype' => null,
])

<div class="card admin-form-page">
    <div class="card-header">
        <h4 class="card-title">{{ $title }}</h4>
    </div>
    <div class="card-body">
        <form
            action="{{ $action }}"
            method="POST"
            @if($enctype) enctype="{{ $enctype }}" @endif
            {{ $attributes->merge(['class' => 'admin-form']) }}
        >
            @csrf
            @if(! in_array(strtoupper($method), ['GET', 'POST'], true))
                @method($method)
            @endif

            {{ $slot }}

            <x-admin.form-actions :submit="$submitLabel" :cancel-url="$cancelUrl" />
        </form>
    </div>
</div>
