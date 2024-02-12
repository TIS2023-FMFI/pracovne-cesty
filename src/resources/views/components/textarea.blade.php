<div class="form-group">
        <label for="{{ $name }}">{{ $label }}</label>

    <textarea
        class="form-control @error($name) is-invalid @enderror"
        id="{{ $name }}"
        name="{{ $name }}"
        rows="{{ $rows }}"
        {{ $isDisabled() ? 'disabled' : '' }}
    >{{ old($name, $value) }}</textarea>

    @error($name)
    <p class="invalid-feedback">{{ $message }}</p>
    @enderror
</div>
