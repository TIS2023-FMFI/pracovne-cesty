@props(['color' => 'dark', 'modal' => ''])

<button
    {{ $attributes->merge(['class' => 'btn btn-' . $color . ' py-2 px-4 rounded-0']) }}
    @if($modal != '')
        data-toggle="modal"
        data-target="#{{ $modal }}"
    @endif>
    <span class="text-uppercase font-weight-bold">
        {{ $slot }}
    </span>
</button>

