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
        <div class="admin-image-upload__card">
            <button type="button"
                    class="admin-image-upload__remove js-image-remove"
                    title="Remove"
                    aria-label="Remove file">
                <i class="fa fa-trash"></i>
            </button>
            <button type="button" class="admin-image-upload__change js-image-change" title="Change file">
                <img src="" alt="{{ $previewAlt }}" class="admin-image-upload__image js-image-preview-img" hidden>
                <div class="admin-image-upload__file js-file-preview" hidden>
                    <i class="fas fa-file-alt admin-image-upload__file-icon" aria-hidden="true"></i>
                    <span class="admin-image-upload__filename js-file-preview-name"></span>
                </div>
            </button>
        </div>
    </div>
</div>
@error($name)
    <div class="invalid-feedback d-block">{{ $message }}</div>
@enderror
