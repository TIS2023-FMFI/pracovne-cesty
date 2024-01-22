<div class="form-group">
            @if($label != '')
                <label for="{{ $name }}">{{ $label }}</label>
            @endif

            <input class="form-control" type="{{ $type }}" id="{{ $name }}" name="{{ $name }}" value="{{ $value == '' ? old($name) : $value }}" {{ $isDisabled() ? 'disabled' : '' }} />
            @error($name)
            <p>{{$message}}</p>
            @enderror


</div>

