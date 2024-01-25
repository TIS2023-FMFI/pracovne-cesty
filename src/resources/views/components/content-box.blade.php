@props(['title'])

<div class="my-2">
    <div {{ $attributes->merge(['class' => 'p-3 bg-dark text-uppercase text-white font-weight-bold']) }}>{{ $title }}</div>
    <div class="bg-white border border-top-0 p-4">
        {{ $slot }}
    </div>
</div>
