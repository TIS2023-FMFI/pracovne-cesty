@props(['ref'])

<a
    {{ $attributes->merge(['class' => 'd-flex align-items-center justify-content-between py-1 px-3 border-bottom text-decoration-none text-dark']) }}
    href="{{ $ref }}">
    <div class="flex-grow-1">
        {{ $slot }}
    </div>
</a>
