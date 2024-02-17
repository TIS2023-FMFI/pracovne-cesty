@props(['name', 'label' => '', 'disabled' => false, 'value' => '', 'rows' => 3])

<div class="form-group">
        <label for="{{ $name }}">{{ $label }}</label>

    <textarea
        class="form-control @error($name) is-invalid @enderror"
        id="{{ $name }}"
        name="{{ $name }}"
        rows="{{ $rows }}"
        @disabled($disabled)
    >{{ old($name, $value) }}</textarea>

    @error($name)
    <p class="invalid-feedback">{{ $message }}</p>
    @enderror
</div>
