<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ContentItem extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public int $id,
        public string $date,
        public int $state,
        public string $reference = ""
    )
    {
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.content-item');
    }
}