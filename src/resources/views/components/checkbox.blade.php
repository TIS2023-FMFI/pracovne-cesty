<div class="col-md-12 form-check">
    <input
        type="checkbox"
        id="{{ $name }}"
        name="{{ $name }}"
        {{ $isChecked() ? 'checked' : '' }}
        {{ $control != "" ? 'x-model='.$control : '' }}
        class="form-check-input"

    />
    @if($label != '')
        <label for="{{ $name }}" class="form-check-label">{{ $label }}</label>
    @endif
</div>
