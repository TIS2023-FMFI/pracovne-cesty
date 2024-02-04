<div class="form-group">
        <label for="{{ $name }}">{{ $label }}</label>

    <textarea
        class="form-control"
        id="{{ $name }}"
        name="{{ $name }}"
        value="{{ old($name, $value) }}"
        rows="{{ $rows }}"
        {{ $isDisabled() ? 'disabled' : '' }}
    >

    </textarea>
</div>
