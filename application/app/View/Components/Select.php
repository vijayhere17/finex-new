<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Select extends Component
{
    public $name;
    public $id;
    public $label;
    public $options;
    
    /**
     * Create a new component instance.
     */
    public function __construct($name, $id, $label, $options)
    {
        //
        $this->name = $name;
        $this->id = $id;
        $this->label = $label;
        $this->options = $options;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.select');
    }
}
