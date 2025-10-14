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

    /**
     * Create a new component instance.
     */
    public function __construct(
        string $id,
        bool $isHeader = true,
        string $title = '',
    ) {
        $this->id = $id;
        $this->isHeader = $isHeader;
        $this->title = $title;
    }

    /**
     * Resolve an optional attribute from the component attribute bag.
     */
    private function optionalAttribute(string $key, string $default = ''): string
    {
        $value = $this->attributes->get($key);

        if ($value === null) {
            return $default;
        }

        return (string) $value;
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
            'modalTitleId' => $this->optionalAttribute('modalTitleId'),
            'className' => $this->optionalAttribute('className'),
            'dialogClassName' => $this->optionalAttribute('dialogClassName', 'modal-md'),
            'dialogPosition' => $this->optionalAttribute('dialogPosition', 'modal-dialog-centered'),
            'formId' => $this->optionalAttribute('formId'),
            'method' => $this->optionalAttribute('method'),
            'action' => $this->optionalAttribute('action'),
            'enctype' => $this->optionalAttribute('enctype'),
            'modalBodyClass' => $this->optionalAttribute('modalBodyClass'),
        ]);
    }
}
