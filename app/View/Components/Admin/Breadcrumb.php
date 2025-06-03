<?php

namespace App\View\Components\Admin;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Breadcrumb extends Component
{
    public $title, $breadcrumbs, $buttonText, $buttonId, $modalId, $permissionKey;
    /**
     * Create a new component instance.
     */
    public function __construct(
        $title,
        $breadcrumbs = [],
        $buttonText = '',
        $buttonId = '',
        $modalId = '',
        $permissionKey = ''
    ) {
        $this->title = $title;
        $this->breadcrumbs = $breadcrumbs;
        $this->buttonText = $buttonText;
        $this->buttonId = $buttonId;
        $this->modalId = $modalId;
        $this->permissionKey = $permissionKey;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.admin.breadcrumb');
    }
}
