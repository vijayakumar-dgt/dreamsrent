<?php

namespace App\View\Components\Admin;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Breadcrumb extends Component
{
    protected $title;
    protected $breadcrumbs;
    protected $buttonText;
    protected $buttonId;
    protected $modalId;
    protected $permissionKey;
    protected $permissionModule;

    /**
     * Create a new component instance.
     */
    public function __construct(
        $title,
        $breadcrumbs = [],
        $buttonText = '',
        $buttonId = '',
        $modalId = '',
        $permissionKey = 'create',
        $permissionModule = '',
    ) {
        $this->title = $title;
        $this->breadcrumbs = $breadcrumbs;
        $this->buttonText = $buttonText;
        $this->buttonId = $buttonId;
        $this->modalId = $modalId;
        $this->permissionKey = $permissionKey;
        $this->permissionModule = $permissionModule;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.admin.breadcrumb',[
            'title' => $this->title,
            'breadcrumbs' => $this->breadcrumbs,
            'buttonText' => $this->buttonText,
            'buttonId' => $this->buttonId,
            'modalId' => $this->modalId,
            'permissionKey' => $this->permissionKey,
            'permissionModule' => $this->permissionModule
        ]);
    }
}
