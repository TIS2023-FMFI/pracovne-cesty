<fieldset {{ $attributes->merge(['class' => 'border-bottom pb-4 my-4']) }}>
    <b>{{ $title }}</b>
    <div class="row mt-3">
        {{ $slot }}
    </div>
</fieldset>
