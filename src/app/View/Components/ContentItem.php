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
        public string $content,
        public string $date,
        public string $reference = "",
        public int $state = -1
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
