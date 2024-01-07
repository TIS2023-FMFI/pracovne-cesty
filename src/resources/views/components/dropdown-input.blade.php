<div class="form-group">
    @if ($label != '')
        <label for="{{ $name }}">{{ $label }}</label>
    @endif
    <select class="custom-select" name="{{ $name }}" id="{{ $name }}" {{ $isDisabled() ? 'disabled' : '' }}>
        @foreach ($values as $id => $name)
            <option value="{{ $id }}" {{ $isSelected($id) ? 'selected' : '' }}>
                {{ $name }}
            </option>
        @endforeach
    </select>
</div>

{{--<div class="form-group">--}}
{{--    <div class="row">--}}
{{--        <label for="{{ $name }}" class="col-sm-3 col-form-label col-form-label-sm text-right">{{ $label }}</label>--}}
{{--        <div class="col-sm-9">--}}
{{--            <select class="custom-select custom-select-sm" name="{{ $name }}" id="{{ $name }}" {{ $isDisabled() ? 'disabled' : '' }}>--}}
{{--                @foreach ($values as $id => $name)--}}
{{--                    <option value="{{ $id }}" {{ $isSelected($id) ? 'selected' : '' }}>--}}
{{--                        {{ $name }}--}}
{{--                    </option>--}}
{{--                @endforeach--}}
{{--            </select>--}}
{{--        </div>--}}
{{--    </div>--}}
{{--</div>--}}
