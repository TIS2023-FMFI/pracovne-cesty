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

    public function getIcon(): string {
        switch ($this->state) {
            case TripState::NEW:
                return 'sun';
            case TripState::CONFIRMED:
                return 'file-circle-question';
            case TripState::UPDATED:
                return 'file-circle-plus';
            case TripState::COMPLETED:
                return 'file-circle-check';
            case TripState::CLOSED:
                return 'circle-check';
            case TripState::CANCELLATION_REQUEST:
                return 'file-circle-xmark';
            case TripState::CANCELLED:
                return 'circle-xmark';
        }
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.content-item');
    }
}
