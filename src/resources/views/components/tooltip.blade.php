@props(['text', 'icon' => 'question-circle'])

<span x-data="{ showText: false }" class="click-icon">
    <i
        class="fa-solid fa-{{ $icon }}"
        @click="showText = !showText"
        aria-hidden="true">
    </i>
    <span x-show="showText" x-cloak class="text">
        {{ $text }}
    </span>
</span>
