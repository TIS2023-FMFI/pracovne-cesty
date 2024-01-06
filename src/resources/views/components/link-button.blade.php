<a
    class="btn btn-dark text-uppercase text-decoration-none py-2 px-4 rounded-0 font-weight-bold"
    {{ $href != "" ? 'href='.$href : '' }}
    >
    {{ $slot }}
</a>


