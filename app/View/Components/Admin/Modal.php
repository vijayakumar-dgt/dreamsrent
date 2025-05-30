<?php

namespace App\View\Components\Admin;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Modal extends Component
{
    public string $id;
    public string $title;
    public string $className;
    public string $dialogClassName;
    public string $dialogPosition;
    public string $formId;
    public string $method;
    public string $action;

    /**
     * Create a new component instance.
     */
    public function __construct(
        string $id,
        string $title,
        string $className = '',
        string $dialogClassName = '',
        string $dialogPosition = 'modal-dialog-centered',
        string $formId = '',
        string $method = '',
        string $action = ''
    ) {
        $this->id = $id;
        $this->title = $title;
        $this->className = $className;
        $this->dialogClassName = $dialogClassName;
        $this->dialogPosition = $dialogPosition;
        $this->formId = $formId;
        $this->method = $method;
        $this->action = $action;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.admin.modal');
    }
}
