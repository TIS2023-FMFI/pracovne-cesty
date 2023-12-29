<?php

namespace App\View\Components;

use App\Enums\TripState;
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
        public TripState $state,
        public string $place,
        public string $user,
        public string $purpose,
        public string $sofiaId,
        public string $date = '',
        public string $reference = ''
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
