<a
    href="trips/{{ $id }}"
    class="d-flex align-items-center justify-content-between py-1 px-3 border-bottom text-decoration-none text-dark">
    <div class="flex-grow-1">
        {{ $slot }}
    </div>
    <div class="text-right">
        {{ $date }}
    </div>
    <div style="width: 30px; height: 30px; background: #323232;">
    </div>
</a>
