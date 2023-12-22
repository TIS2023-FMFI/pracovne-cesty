<div class="col-md-6">
    <label for="{{ $name }}">{{ $label }}</label>
    <input class="form-control" type="{{ $type }}" id="{{ $name }}" name="{{ $name }}" value="{{ $value == "" ? old($name) : $value}}" {{ $isReadOnly() ? 'readonly' : '' }}/>
</div>

@error("{{ $name }}")
    <p>{{$message}}</p>
@enderror

