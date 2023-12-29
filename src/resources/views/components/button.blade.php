<button
    class="btn btn-dark text-uppercase py-2 px-5 rounded-0 font-weight-bold"
    @if($event != '')
        x-data="{}"
        @click="$dispatch('{{ $event }}')"
    @endif>
    {{ $slot }}
</button>

