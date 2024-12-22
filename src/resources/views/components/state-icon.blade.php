@props(['state'])

<span
    class="state-icon"
    title="{{ $state->inSlovak() }}">
    <i class="fa-solid fa-{{ $state->icon() }}" style="color: {{ $state->iconColor() }};"></i>
</span>
