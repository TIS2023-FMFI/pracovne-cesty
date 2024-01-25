@props(['color' => 'dark', 'event' => ''])

<button
    class="btn btn-{{ $color }} text-uppercase py-2 px-4 rounded-0 font-weight-bold"
    @if($event != '')
        x-data="{}"
        @click="$dispatch('{{ $event }}')"
    @endif>
    {{ $slot }}
</button>

