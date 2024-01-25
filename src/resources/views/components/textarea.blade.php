<div class="form-group">
        <label for="{{ $name }}">{{ $label }}</label>
        <textarea
            class="form-control"
            id="{{ $name }}"
            name="{{ $name }}"
            rows="{{ $rows }}"
            {{ $isDisabled() ? 'disabled' : '' }}
        >{{ $value == '' ? old($name) : $value }}</textarea>
</div>
