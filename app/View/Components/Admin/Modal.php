<?php

namespace App\View\Components\Admin;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Modal extends Component
{
    private const DEFAULT_CONFIG = [
        'className' => '',
        'dialogClassName' => 'modal-md',
        'dialogPosition' => 'modal-dialog-centered',
        'formId' => '',
        'method' => '',
        'action' => '',
        'enctype' => '',
        'modalBodyClass' => '',
    ];

    protected string $id;
    protected bool $isHeader;
    protected string $title;
    protected string $modalTitleId;
    protected array $config;

    /**
     * Create a new component instance.
     */
    public function __construct(
        string $id,
        bool $isHeader = true,
        string $title = '',
        string $modalTitleId = '',
        array $config = [],
    ) {
        $this->id = $id;
        $this->isHeader = $isHeader;
        $this->title = $title;
        $this->modalTitleId = $modalTitleId;
        $this->config = array_merge(self::DEFAULT_CONFIG, $config);
    }

    /**
     * Resolve the configuration options for the modal.
     */
    protected function resolveConfig(): array
    {
        $attributeOverrides = [];

        foreach (array_keys(self::DEFAULT_CONFIG) as $key) {
            if ($this->attributes->has($key)) {
                $attributeOverrides[$key] = $this->attributes->get($key);
            }
        }

        return array_merge(self::DEFAULT_CONFIG, $this->config, $attributeOverrides);
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        $config = $this->resolveConfig();

        return view('components.admin.modal', [
            'id' => $this->id,
            'isHeader' => $this->isHeader,
            'title' => $this->title,
            'modalTitleId' => $this->modalTitleId,
            'className' => $config['className'],
            'dialogClassName' => $config['dialogClassName'],
            'dialogPosition' => $config['dialogPosition'],
            'formId' => $config['formId'],
            'method' => $config['method'],
            'action' => $config['action'],
            'enctype' => $config['enctype'],
            'modalBodyClass' => $config['modalBodyClass'],
        ]);
    }
}
