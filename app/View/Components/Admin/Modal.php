<?php

namespace App\View\Components\Admin;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Modal extends Component
{
    public string $id;
    public bool $isHeader;
    public string $title;
    public string $modalTitleId;
    public string $className;
    public string $dialogClassName;
    public string $dialogPosition;
    public string $formId;
    public string $method;
    public string $action;
    public string $enctype;
    public string $modalBodyClass = '';

    /**
     * Create a new component instance.
     */
    public function __construct(
        string $id,
        bool $isHeader = true,
        string $title = '',
        string $modalTitleId = '',
        string $className = '',
        string $dialogClassName = 'modal-md',
        string $dialogPosition = 'modal-dialog-centered',
        string $formId = '',
        string $method = '',
        string $action = '',
        string $enctype = '',
        string $modalBodyClass = '',
    ) {
        $this->id = $id;
        $this->isHeader = $isHeader;
        $this->title = $title;
        $this->modalTitleId = $modalTitleId;
        $this->className = $className;
        $this->dialogClassName = $dialogClassName;
        $this->dialogPosition = $dialogPosition;
        $this->formId = $formId;
        $this->method = $method;
        $this->action = $action;
        $this->enctype = $enctype;
        $this->modalBodyClass = $modalBodyClass;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.admin.modal');
    }
}
