<div
    x-data="{ {{ $control }}: false}"
    x-on:{{ $event }}.window="{{ $control }} = true">
    <div
        style="position: fixed; z-index: 10"
        role="dialog"
        tabindex="-1"
        x-show="{{ $control }}"
        x-on:click.away="{{ $control }} = false"
    >
        <div>
            <div>
                <h3>{{ $title }}</h3>
            </div>
            <p>
                {{ $slot }}
            </p>
        </div>
    </div>
</div>
