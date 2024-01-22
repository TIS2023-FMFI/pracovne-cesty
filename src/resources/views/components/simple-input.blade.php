<div class="form-group">
            @if($label != '')
                <label for="{{ $name }}">{{ $label }}</label>
            @endif

            <input class="form-control" type="{{ $type }}" id="{{ $name }}" name="{{ $name }}" value="{{ $value == '' ? old($name) : $value }}" {{ $isDisabled() ? 'disabled' : '' }} />
            @error($name)
            <p>{{$message}}</p>
            @enderror


</div>


{{--<div class="form-group">--}}
{{--    <div class="row">--}}
{{--        <div class="col-sm-3 text-right">--}}
{{--            @if($label != '')--}}
{{--                <label for="{{ $name }}" class="col-form-label col-form-label-sm ">{{ $label }}</label>--}}
{{--            @endif--}}
{{--        </div>--}}

{{--        <div class="col-sm-9">--}}
{{--            <input class="form-control form-control-sm" type="{{ $type }}" id="{{ $name }}" name="{{ $name }}" value="{{ $value == '' ? old($name) : $value }}" {{ $isReadOnly() ? 'readonly' : '' }} />--}}
{{--            @error($name)--}}
{{--            <p>{{$message}}</p>--}}
{{--            @enderror--}}
{{--        </div>--}}
{{--    </div>--}}
{{--</div>--}}

