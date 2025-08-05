<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\Contracts\View\View;

class MenuCategoryGrid extends Component
{
    public $key;
    public $label;
    public $items;

    public function __construct($key, $label, $items)
    {
        $this->key = $key;
        $this->label = $label;
        $this->items = $items;
    }

    public function render(): View
    {
        return view('components.menu-category-grid');
    }

    
}
