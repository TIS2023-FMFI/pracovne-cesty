<div class="{{ $getSize() }} form-group">
    <div class="row">
        <label for="{{ $name }}" class="col-sm-4 col-form-label col-form-label-sm text-right">{{ $label }}</label>
        <div class="col-sm-8">
        <textarea
            class="form-control form-control-sm"
            id="{{ $name }}"
            name="{{ $name }}"
            {{ $isReadOnly() ? 'readonly' : '' }}
        >{{ $value == '' ? old($name) : $value }}</textarea>
        </div>
    </div>
</div>
