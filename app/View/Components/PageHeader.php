<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class PageHeader extends Component
{
    public $title;
    public $desc;
    public $breadcrumbs;


    /**
     * Create a new component instance.
     */
    public function __construct($title, $desc = '', $breadcrumbs = [])
    {
        //These is for the heading part

        $this->title = $title;
        $this->desc = $desc;
        $this->breadcrumbs = $breadcrumbs;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.page-header');
    }
}
