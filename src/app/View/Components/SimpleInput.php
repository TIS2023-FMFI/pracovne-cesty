<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class SimpleInput extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public string $name,
        public string $label = '',
        public string $type = 'text',
        public bool $readOnly = false,
        public string $value = '',
        public string $size = 'short'
    )
    {
        //
    }

    public function isReadOnly(): bool
    {
        return $this->readOnly;
    }

    public function getSize(): string
    {
        return $this->size == 'short' ? 'col-md-6' : ($this->size == 'long' ? 'col-md-12' : '');
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.simple-input');
    }
}
