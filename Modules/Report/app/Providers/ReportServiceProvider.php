<?php

namespace Modules\Report\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Nwidart\Modules\Traits\PathNamespace;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Modules\Report\Repositories\Contracts\ReportRepositoryInterface;
use Modules\Report\Repositories\Eloquent\ReportRepository;

class ReportServiceProvider extends ServiceProvider
{
    use PathNamespace;

    protected string $name = 'Report';

    protected string $nameLower = 'report';

    /**
     * Boot the application events.
     */
    public function boot(): void
    {
        $this->registerCommands();
        $this->registerCommandSchedules();
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
        $this->loadMigrationsFrom(module_path($this->name, 'database/migrations'));
    }

    /**
     * Register the service provider.
     */
    public function register(): void
    {
        $this->app->register(EventServiceProvider::class);
        $this->app->register(RouteServiceProvider::class);
        $this->app->bind(ReportRepositoryInterface::class, ReportRepository::class);
    }

    /**
     * Register commands in the format of Command::class
     */
    protected function registerCommands(): void
    {
    }

    /**
     * Register command Schedules.
     */
    protected function registerCommandSchedules(): void
    {
    }

    /**
     * Register translations.
     */
    public function registerTranslations(): void
    {
        $langPath = resource_path('lang/modules/' . $this->nameLower);

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, $this->nameLower);
            $this->loadJsonTranslationsFrom($langPath);
        } else {
            $this->loadTranslationsFrom(module_path($this->name, 'lang'), $this->nameLower);
            $this->loadJsonTranslationsFrom(module_path($this->name, 'lang'));
        }
    }

    /**
     * Register config.
     */
    protected function registerConfig(): void
    {
        $relativeConfigPath = config('modules.paths.generator.config.path');
        $configPath = module_path($this->name, $relativeConfigPath);

        if (is_dir($configPath)) {
            $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($configPath));

            foreach ($iterator as $file) {
                if ($file->isFile() && $file->getExtension() === 'php') {
                    $relativePath = str_replace($configPath . DIRECTORY_SEPARATOR, '', $file->getPathname());

                    // Ensure the result is a string to avoid Larastan error
                    $sanitized = str_replace(
                        [DIRECTORY_SEPARATOR, '.php'],
                        ['.', ''],
                        $relativePath
                    );

                    if (is_array($sanitized)) {
                        $sanitized = implode('', $sanitized); // fallback safety, though it shouldn't be an array
                    }

                    $configKey = $this->nameLower . '.' . (string) $sanitized;
                    $key = ($relativePath === 'config.php') ? $this->nameLower : $configKey;

                    $this->publishes([$file->getPathname() => config_path($relativePath)], 'config');
                    $this->mergeConfigFrom($file->getPathname(), $key);
                }
            }
        }
    }

    /**
     * Register views.
     */
    public function registerViews(): void
    {

        $nameLower = (string)$this->nameLower;
        $viewPath = resource_path('views/modules/' . $nameLower);
        $sourcePath = module_path($this->name, 'resources/views');
        $this->publishes([$sourcePath => $viewPath], ['views', $nameLower . '-module-views']);
        $this->loadViewsFrom(array_merge($this->getPublishableViewPaths(), [$sourcePath]), $nameLower);
        $componentPath = config('modules.paths.generator.component-class.path');


        if (is_array($componentPath)) {
            $componentPath = implode('', $componentPath);
        }

        $componentPath = (string)$componentPath;
        $componentNamespace = $this->module_namespace($this->name, $componentPath);

        Blade::componentNamespace($componentNamespace, $nameLower);
    }





/**
 * Get the publishable view paths.
 *
 * @return string[]  Array of view paths.
 */
    public function provides(): array
    {
        return [];
    }
/**
 * Get the publishable view paths.
 *
 * @return string[]  Array of view paths.
 */
    private function getPublishableViewPaths(): array
    {
        $paths = [];
        foreach (config('view.paths') as $path) {
            if (is_dir($path . '/modules/' . $this->nameLower)) {
                $paths[] = $path . '/modules/' . $this->nameLower;
            }
        }

        return $paths;
    }
}
