<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Textarea extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public string $name,
        public string $label = "",
        public bool $disabled = false,
        public string $value = "",
        public int $rows = 3
    )
    {
        //
    }

    /**
     * Decides if input should be disabled.
     */
    public function isDisabled(): bool
    {
        return $this->disabled;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.textarea');
    }
}
