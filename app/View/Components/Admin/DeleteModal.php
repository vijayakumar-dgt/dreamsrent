<?php

namespace App\View\Components\Admin;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class DeleteModal extends Component
{
    protected array $config;

    public function __construct(array $config = [])
    {
        // Default values
        $defaults = [
            'className'       => '',
            'id'              => '',
            'formId'          => '',
            'action'          => '',
            'method'          => 'POST',
            'formClass'       => '',
            'hiddenInputs'    => [],
            'title'           => '',
            'description'     => '',
            'deleteBtnType'   => 'submit',
            'deleteBtnId'     => '',
            'deleteBtnText'   => __('admin.common.yes_delete'),
            'modalIconClass'  => 'ti ti-trash-x fs-26',
        ];

        // Merge user options with defaults
        $this->config = array_merge($defaults, $config);
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.admin.delete-modal', $this->config);
    }
}
