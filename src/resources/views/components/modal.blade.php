<div x-data="{ {{ $control }}: false }" x-on:{{ $event }}.window="{{ $control }} = true">
    <div x-show="{{ $control }}" style="position: fixed; z-index: 10; top: 50%; left: 50%; transform: translate(-50%, -50%);" role="dialog" tabindex="-1" x-on:click.away="{{ $control }} = false">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ $title }}</h5>
                    <button type="button" class="btn-close" x-on:click="{{ $control }} = false"></button>
                </div>
                <div class="modal-body">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </div>
</div>
