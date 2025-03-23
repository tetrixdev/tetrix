<?php

namespace Tetrix\Components\Classes;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ButtonPrimary extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(public string $type = 'button')
    {
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('tx::button-primary');
    }
}