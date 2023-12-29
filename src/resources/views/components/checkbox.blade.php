<div class="col-md-12 custom-control custom-checkbox">
    <input
        type="checkbox"
        id="{{ $name }}"
        name="{{ $name }}"
        {{ $isChecked() ? 'checked' : '' }}
        {{ $control != "" ? 'x-model='.$control : '' }}
        class="custom-control-input"

    />
    @if($label != '')
        <label for="{{ $name }}" class="custom-control-label">{{ $label }}</label>
    @endif
</div>
