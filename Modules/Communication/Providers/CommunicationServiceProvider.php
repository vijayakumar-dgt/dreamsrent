<?php

namespace Modules\Communication\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Nwidart\Modules\Traits\PathNamespace;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;

class CommunicationServiceProvider extends ServiceProvider
{
    use PathNamespace;

    protected string $name = 'Communication';
    protected string $nameLower = 'communication';

    // ... [previous methods remain unchanged until registerConfig]

    /**
     * Register config.
     */
    protected function registerConfig(): void
    {
        $relativeConfigPath = config('modules.paths.generator.config.path');

        // Ensure the path is a non-empty string
        if (!is_string($relativeConfigPath)) {
            return;
        }

        $configPath = module_path($this->name, $relativeConfigPath);

        // module_path() always returns string, so we only need to check if it's a valid directory
        if (!is_dir($configPath)) {
            return;
        }

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($configPath, RecursiveDirectoryIterator::SKIP_DOTS)
        );

        /** @var SplFileInfo $file */
        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getExtension() === 'php') {
                $relativePath = str_replace($configPath . DIRECTORY_SEPARATOR, '', $file->getPathname());
                $pathKey = str_replace([DIRECTORY_SEPARATOR, '.php'], ['.', ''], $relativePath);
                $configKey = $this->nameLower . '.' . $pathKey;
                $key = ($relativePath === 'config.php') ? $this->nameLower : $configKey;

                $this->publishes([$file->getPathname() => config_path($relativePath)], 'config');
                $this->mergeConfigFrom($file->getPathname(), $key);
            }
        }
    }

    /**
     * Register views.
     */
    public function registerViews(): void
    {
        $viewPath = resource_path('views/modules/' . $this->nameLower);
        $sourcePath = module_path($this->name, 'resources/views');

        $this->publishes([$sourcePath => $viewPath], ['views', $this->nameLower . '-module-views']);
        $this->loadViewsFrom(array_merge($this->getPublishableViewPaths(), [$sourcePath]), $this->nameLower);

        $componentPath = config('modules.paths.generator.component-class.path');
        $componentNamespace = $this->module_namespace(
            $this->name,
            $this->app_path(is_string($componentPath) ? $componentPath : null)
        );
        Blade::componentNamespace($componentNamespace, $this->nameLower);
    }

    /**
     * Get publishable view paths.
     *
     * @return array<string>
     */
    private function getPublishableViewPaths(): array
    {
        $paths = [];
        $viewPaths = config('view.paths', []);

        if (!is_array($viewPaths)) {
            return $paths;
        }

        foreach ($viewPaths as $path) {
            if (is_string($path)) {
                $modulePath = $path . '/modules/' . $this->nameLower;
                if (is_dir($modulePath)) {
                    $paths[] = $modulePath;
                }
            }
        }

        return $paths;
    }

    // ... [remaining methods stay unchanged]
}
