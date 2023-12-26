<div class="col-md-6 form-group row">
    <label for="{{ $name }}" class="col-sm-3 col-form-label">{{ $label }}</label>
    <div class="col-sm-9">
        <input class="form-control" type="{{ $type }}" id="{{ $name }}" name="{{ $name }}" value="{{ $value == '' ? old($name) : $value }}" {{ $isReadOnly() ? 'readonly' : '' }} />
        @error($name)
        <p>{{$message}}</p>
        @enderror
    </div>
</div>
