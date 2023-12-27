<fieldset {{ $attributes->merge(['class' => 'border-bottom pb-4 m-4']) }}>
    <b>{{ $title }}</b>
    <div class="mt-3 px-5">
        @if($description != "")
            <div class="mb-4">{{ $description }}</div>
        @endif

        <div class="row ">
            {{ $slot }}
        </div>
    </div>

</fieldset>
