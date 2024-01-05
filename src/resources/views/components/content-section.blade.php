<fieldset {{ $attributes->merge(['class' => 'border-bottom pb-4 mb-4']) }}>
    @if($title != '')
        <b>{{ $title }}</b>
    @endif

    <div class="mt-3 px-4">
        @if($description != "")
            <div class="mb-4">{{ $description }}</div>
        @endif

        <div>
            {{ $slot }}
        </div>
    </div>

</fieldset>
