@props([
    'name' => 'logo',
    'removeName' => 'remove_logo',
    'id' => null,
    'existingUrl' => null,
    'existingFilename' => null,
    'accept' => null,
    'hint' => 'Image or PDF, max 5MB',
    'uploadLabel' => 'Upload file',
    'previewAlt' => 'File preview',
])

@php
    $inputId = $id ?? $name;
    $hasExisting = filled($existingUrl);
    $existingFilename = $existingFilename ?? ($existingUrl ? basename(parse_url($existingUrl, PHP_URL_PATH) ?? '') : null);
@endphp

<div
    {{ $attributes->class(['admin-image-upload', 'is-invalid' => $errors->has($name)]) }}
    @if ($hasExisting) data-existing-url="{{ $existingUrl }}" @endif
    @if ($existingFilename) data-existing-filename="{{ $existingFilename }}" @endif
>
    <input
        type="file"
        name="{{ $name }}"
        id="{{ $inputId }}"
        @if ($accept) accept="{{ $accept }}" @endif
        class="admin-image-upload__input @error($name) is-invalid @enderror"
    >
    <input type="hidden" name="{{ $removeName }}" value="0" class="js-image-remove-flag">

    <div class="admin-image-upload__empty js-image-empty">
        <button type="button" class="btn btn-sm btn-outline-secondary js-image-upload">
            {{ $uploadLabel }}
        </button>
        @if ($hint)
            <span class="admin-image-upload__hint">{{ $hint }}</span>
        @endif
    </div>

    <div class="admin-image-upload__preview js-image-preview" hidden>
        <img src="" alt="{{ $previewAlt }}" class="admin-image-upload__image js-image-preview-img" hidden>
        <div class="admin-image-upload__file js-file-preview" hidden>
            <a href="#" target="_blank" rel="noopener" class="admin-image-upload__file-link js-file-preview-link">
                <span class="admin-image-upload__filename js-file-preview-name"></span>
            </a>
        </div>
        <div class="admin-image-upload__actions">
            <button type="button" class="btn btn-sm btn-outline-primary js-image-change">Change</button>
            <button type="button" class="btn btn-sm btn-outline-danger js-image-remove">Remove</button>
        </div>
    </div>
</div>
@error($name)
    <div class="invalid-feedback d-block">{{ $message }}</div>
@enderror
