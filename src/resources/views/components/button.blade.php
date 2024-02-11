@props(['color' => 'dark', 'modal' => ''])

<button
    class="btn btn-{{ $color }} text-uppercase py-2 px-4 rounded-0 font-weight-bold"
    @if($modal != '')
        data-toggle="modal"
        data-target="#{{ $modal }}"
    @endif>
    {{ $slot }}
</button>

