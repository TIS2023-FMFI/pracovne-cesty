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
            <option
                value="{{ $id }}"
                @selected(old($name, $selected) == $id)
            >
                {{ $name }}
            </option>
        @endforeach
    </select>
</div>
