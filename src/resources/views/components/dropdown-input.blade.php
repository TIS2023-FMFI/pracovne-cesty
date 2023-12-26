<div class="col-md-6 form-group row">
    <label for="{{ $name }}" class="col-sm-4 col-form-label col-form-label-sm text-right">{{ $label }}</label>
    <div class="col-sm-8">
        <select class="form-control form-control-sm" name="{{ $name }}" id="{{ $name }}" {{ $isDisabled() ? 'disabled' : '' }}>
            @foreach ($values as $value)
                <option value="{{ $value }}" {{ $isSelected($value) ? 'selected' : '' }}>
                    {{ $value }}
                </option>
            @endforeach
        </select>
    </div>
</div>
