<div class="form-group">
            @if($label != '')
                <label for="{{ $name }}">{{ $label }}</label>
            @endif

            <input
                class="form-control @error($name) is-invalid @enderror"
                type="{{ $type }}"
                id="{{ $name }}"
                name="{{ $name }}"
                value="{{ old($name, $value) }}"
                @disabled($disabled)
                @readonly($readonly)
            />

            @error($name)
                <p class="invalid-feedback">{{ $message }}</p>
            @enderror


</div>

