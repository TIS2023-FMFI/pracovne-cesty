@props(['text', 'icon' => 'question-circle'])

<span class="hover-icon">
    <i class="fa-solid fa-{{ $icon }}" aria-hidden="false"></i>
    <span class="tooltip">
        {{ $text }}
    </span>
</span>
