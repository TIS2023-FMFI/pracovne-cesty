@props(['color' => 'dark', 'href' => ''])

<a
    class="btn btn-{{ $color }} text-uppercase text-decoration-none py-2 px-4 rounded-0 font-weight-bold"
    {{ $href != '' ? 'href='.$href : '' }}
    >
    {{ $slot }}
</a>


