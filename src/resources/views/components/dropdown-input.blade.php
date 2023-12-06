<label for="{{ $name }}">{{ $label }}</label>

<select name="{{ $name }}" id="{{ $name }}" {{ $isDisabled() ? 'disabled' : '' }}>
    @foreach ($values as $value)
        <option value="{{ $value }}" {{ $isSelected($value) ? 'selected' : '' }}>
            {{ $value }}
        </option>
    @endforeach
</select>
