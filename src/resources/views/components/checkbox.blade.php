<input
    type="checkbox"
    id="{{ $name }}"
    name="{{ $name }}"
    {{ $isChecked() ? 'checked' : '' }}
    {{ $control != "" ? 'x-model='.$control : '' }}
/>
<label for="{{ $name }}">{{ $label }}</label>
