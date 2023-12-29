<a
    class="btn btn-dark text-uppercase text-decoration-none py-2 px-5 rounded-0 font-weight-bold"
    {{ $href != "" ? 'href='.$href : '' }}
    >
    {{ $slot }}
</a>


