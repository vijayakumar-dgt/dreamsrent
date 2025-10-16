<?php

namespace App\View\Components\Admin;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Modal extends Component
{
    protected string $id;
    protected bool $isHeader;
    protected string $title;
    protected string $modalTitleId;
    protected string $className;
    protected string $dialogClassName;
    protected string $dialogPosition;
    protected string $formId;
    protected string $method;
    protected string $action;
    protected string $enctype;
    protected string $modalBodyClass = '';

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
        return view('components.admin.modal', [
            'id' => $this->id,
            'isHeader' => $this->isHeader,
            'title' => $this->title,
            'modalTitleId' => $this->modalTitleId,
            'className' => $this->className,
            'dialogClassName' => $this->dialogClassName,
            'dialogPosition' => $this->dialogPosition,
            'formId' => $this->formId,
            'method' => $this->method,
            'action' => $this->action,
            'enctype' => $this->enctype,
            'modalBodyClass' => $this->modalBodyClass,
        ]);
    }
}
