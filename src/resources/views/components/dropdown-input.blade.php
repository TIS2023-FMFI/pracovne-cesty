<div class="col-md-6 form-group row">
    <label for="{{ $name }}" class="col-sm-3 col-form-label">{{ $label }}</label>
    <div class="col-sm-9">
        <select class="form-control" name="{{ $name }}" id="{{ $name }}" {{ $isDisabled() ? 'disabled' : '' }}>
            @foreach ($values as $value)
                <option value="{{ $value }}" {{ $isSelected($value) ? 'selected' : '' }}>
                    {{ $value }}
                </option>
            @endforeach
        </select>
    </div>
</div>
