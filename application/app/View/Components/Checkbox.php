<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Checkbox extends Component
{
    public $type; 
    public $name;
    public $id;
    public $ischeck;
    public $label;  

    /**
     * Create a new component instance.
     */
    public function __construct($type, $name, $id, $ischeck, $label)
    {
        //
        $this->type = $type;
        $this->name = $name;
        $this->id = $id;
        $this->ischeck = $ischeck;
        $this->label = $label;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.checkbox');
    }
}
