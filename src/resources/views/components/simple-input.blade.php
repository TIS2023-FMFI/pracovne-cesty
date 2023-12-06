<div>
    <label for="{{ $name }}">{{ $label }}</label>
    <input type="{{ $type }}" id="{{ $name }}" name="{{ $name }}" value="{{ $value }}" {{ $isReadOnly() ? 'readonly' : '' }}/>
</div>

