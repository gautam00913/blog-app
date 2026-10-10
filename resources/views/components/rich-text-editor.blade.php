@props([
    'property',
    'value' => '',
    'placeholder' => '',
])

<div
    wire:ignore
    x-data="richTextEditor(@js($property), @js($value))"
>
    <trix-editor
        x-ref="editor"
        data-placeholder="{{ $placeholder }}"
    ></trix-editor>
</div>
