@props(['control'])

<div x-show="{{ $control }}" class="my-2">
    <div class="container p-0">
        {{ $slot }}
    </div>
</div>
