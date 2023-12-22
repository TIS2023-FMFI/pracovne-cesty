<div class="col-md-6">
    <label for="{{ $name }}">{{ $label }}</label>

    <select class="form-control" name="{{ $name }}" id="{{ $name }}" {{ $isDisabled() ? 'disabled' : '' }}>
        @foreach ($values as $value)
            <option value="{{ $value }}" {{ $isSelected($value) ? 'selected' : '' }}>
                {{ $value }}
            </option>
        @endforeach
    </select>
</div>

