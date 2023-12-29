<fieldset {{ $attributes->merge(['class' => 'border-bottom pb-4 m-4']) }}>
    @if($title != '')
        <b>{{ $title }}</b>
    @endif

    <div class="mt-3 px-4">
        @if($description != "")
            <div class="mb-4">{{ $description }}</div>
        @endif

            <div class="container">
            <div class="row ">
                {{ $slot }}
            </div>
        </div>
    </div>

</fieldset>
