@props(['title'])

<div {{ $attributes }} class="d-flex flex-column">
    <div class="p-3 bg-dark text-uppercase text-white font-weight-bold">{{ $title }}</div>
    <div class="flex-grow-1 bg-white border border-top-0">
        {{ $slot }}
    </div>
</div>
