@props(['label' => '', 'name', 'type' => 'text', 'value' => '', 'disabled' => false, 'readonly' => false])


<div class="form-group">
            @if($label != '')
                <label for="{{ $name }}">{{ $label }}</label>
            @endif

            <input
                class="form-control"
                type="{{ $type }}"
                id="{{ $name }}"
                name="{{ $name }}"
                value="{{ old($name, $value) }}"
                @disabled($disabled)
                @readonly($readonly)
                {{ $attributes }}
            />

            @error($name)
            <p>{{ $message }}</p>
            @enderror


</div>

