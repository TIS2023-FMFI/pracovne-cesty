<label for="{{ $name }}">{{ $label }}</label>
<input type="{{ $type }}" id="{{ $name }}" name="{{ $name }}" value="{{ $value == "" ? old($name) : $value}}" {{ $isReadOnly() ? 'readonly' : '' }}/>

@error("{{ $name }}")
    <p>{{$message}}</p>
@enderror

