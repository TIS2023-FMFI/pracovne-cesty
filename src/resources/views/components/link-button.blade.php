@props(['color' => 'dark', 'href' => '', 'detail' => ''])

<a
    class="btn btn-{{ $color }} text-decoration-none py-2 px-4 rounded-0"
    {{ $href != '' ? 'href='.$href : '' }}
    >
    <span class="text-uppercase font-weight-bold">
        {{ $slot }}
    </span>
    @if($detail != '')
        <i> {{$detail}}</i>
    @endif
</a>


