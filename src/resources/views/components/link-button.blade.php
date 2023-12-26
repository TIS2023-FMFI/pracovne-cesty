<div class="btn-group">
    <a
        class="p-1 bg-dark d-flex justify-content-center text-decoration-none"
        {{ $href != "" ? 'href='.$href : '' }}
        role="button">
    <span class="text-center text-white fs-6 fw-bold text-uppercase px-3 py-2">
        {{ $slot }}
    </span>
    </a>
</div>


