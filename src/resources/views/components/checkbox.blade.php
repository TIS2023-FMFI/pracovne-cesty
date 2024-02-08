@props(['name', 'label' => '', 'control' => ''])

<div class="custom-control custom-checkbox">
        <input
            type="checkbox"
            id="{{ $name }}"
            name="{{ $name }}"
            value="{{ $name }}"
            {{ $control != "" ? 'x-model='.$control : '' }}
            {{ $attributes->merge(['class'=>'custom-control-input'])}}
            @checked(old($name, $checked ))
        />
@if($label != '')
    <label for="{{ $name }}" class="custom-control-label">{{ $label }}</label>
        @endif

</div>
