<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Checkbox extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public string $name,
        public string $label = "",
        public bool $checked = false,
        public string $control = ""
    )
    {
        //
    }

    /**
     * Decides if checkbox should be checked.
     */
    public function isChecked(): bool
    {
        return $this->checked;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.checkbox');
    }
}
