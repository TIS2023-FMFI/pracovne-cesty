<div class="col-md-6 form-group">
    <div class="row">
        <label for="{{ $name }}" class="col-sm-4 col-form-label col-form-label-sm text-right">{{ $label }}</label>
        <div class="col-sm-8">
            <select class="custom-select custom-select-sm" name="{{ $name }}" id="{{ $name }}" {{ $isDisabled() ? 'disabled' : '' }}>
                @foreach ($values as $id => $name)
                    <option value="{{ $id }}" {{ $isSelected($id) ? 'selected' : '' }}>
                        {{ $name }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>

</div>
