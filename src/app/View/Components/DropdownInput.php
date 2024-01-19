<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class DropdownInput extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public string $name,
        public array $values,
        public string $label ='',
        public string $selected = '',
        public bool $disabled = false
    )
    {
        //
    }

    public function isSelected(string $option): bool
    {
        return $option === $this->selected;
    }

    public function isDisabled(): bool
    {
        return $this->disabled;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.dropdown-input');
    }
}