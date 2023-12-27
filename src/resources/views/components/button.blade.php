<div class="btn-group">
    <button
        class="p-1 bg-dark d-flex justify-content-center"
        @if($event != '')
            x-data="{}"
        @click="$dispatch('{{ $event }}')"
        @endif>
    <span class="text-center text-white fs-6 fw-bold text-uppercase px-3 py-2">
        {{ $slot }}
    </span>
    </button>
</div>
