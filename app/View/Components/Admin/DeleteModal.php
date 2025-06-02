<?php

namespace App\View\Components\Admin;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class DeleteModal extends Component
{
    /**
     * Create a new component instance.
     */
    public string $className;
    public string $id;
    public string $formId;
    public string $action;
    public string $method;
    public string $formClass;
    public array $hiddenInputs;
    public string $title;
    public string $description;
    public string $deleteBtnType;
    public string $deleteBtnId;
    
    public function __construct($className = '', $id = '', $formId = '', $action = '', $method = '', $formClass = '', $hiddenInputs = [], $title = '', $description = '', $deleteBtnType = 'submit', $deleteBtnId = '')
    {
        $this->className = $className;
        $this->id = $id;
        $this->formId = $formId;
        $this->action = $action;
        $this->method = $method;
        $this->formClass = $formClass;
        $this->hiddenInputs = $hiddenInputs;
        $this->title = $title;
        $this->description = $description;
        $this->deleteBtnType = $deleteBtnType;
        $this->deleteBtnId = $deleteBtnId;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.admin.delete-modal');
    }
}
