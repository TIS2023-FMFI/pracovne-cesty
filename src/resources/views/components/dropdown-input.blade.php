<div class="form-group">
    @if ($label != '')
        <label for="{{ $name }}">{{ $label }}</label>
    @endif
    <select
        class="custom-select"
        name="{{ $name }}"
        id="{{ $name }}"
        {{ $isDisabled() ? 'disabled' : '' }}
        {{ $control != "" ? 'x-model='.$control : '' }}
    >

        @foreach ($values as $id => $name)
            <option value="{{ $id }}" {{ $isSelected($id) ? 'selected' : '' }}>
                {{ $name }}
            </option>
        @endforeach
    </select>
</div>
