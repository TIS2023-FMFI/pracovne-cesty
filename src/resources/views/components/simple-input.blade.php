<div class="col-md-6 form-group row">
    <label for="{{ $name }}" class="col-sm-4 col-form-label col-form-label-sm text-right">{{ $label }}</label>
    <div class="col-sm-8">
        <input class="form-control form-control-sm" type="{{ $type }}" id="{{ $name }}" name="{{ $name }}" value="{{ $value == '' ? old($name) : $value }}" {{ $isReadOnly() ? 'readonly' : '' }} />
        @error($name)
        <p>{{$message}}</p>
        @enderror
    </div>
</div>
