<fieldset {{ $attributes->merge(['class' => 'border-bottom pb-4 m-4']) }}>
    <b>{{ $title }}</b>
    <div>{{ $description }}</div>
    <div class="row mt-3 px-5">
        {{ $slot }}
    </div>
</fieldset>
