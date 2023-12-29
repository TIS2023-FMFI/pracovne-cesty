@props(['control'])

<div x-show="{{ $control }}" class="col-md-12 mt-3">
    <div class="row">
        {{ $slot }}
    </div>
</div>
