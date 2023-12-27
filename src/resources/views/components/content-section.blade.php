<fieldset {{ $attributes->merge(['class' => 'border-bottom pb-4 m-4']) }}>
    <b>{{ $title }}</b>
    <div class="mt-3 px-5">
        <div class="mb-4">{{ $description }}</div>
        <div class="row ">
            {{ $slot }}
        </div>
    </div>

</fieldset>