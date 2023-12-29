<div class="{{ $getSize() }} form-group">
    <div class="row">
        <div class="col-sm-4 text-right">
            @if($label != '')
                <label for="{{ $name }}" class="col-form-label col-form-label-sm ">{{ $label }}</label>
            @endif
        </div>

        <div class="col-sm-8">
            <input class="form-control form-control-sm" type="{{ $type }}" id="{{ $name }}" name="{{ $name }}" value="{{ $value == '' ? old($name) : $value }}" {{ $isReadOnly() ? 'readonly' : '' }} />
            @error($name)
            <p>{{$message}}</p>
            @enderror
        </div>
    </div>
</div>
