@props(['control'])

<div x-show="{{ $control }}" class="col-md-12 row mt-3">
    {{ $slot }}
</div>
