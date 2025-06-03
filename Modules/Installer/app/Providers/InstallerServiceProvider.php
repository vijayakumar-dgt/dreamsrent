<?php

namespace Modules\Installer\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Nwidart\Modules\Traits\PathNamespace;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;

class InstallerServiceProvider extends ServiceProvider
{
    use PathNamespace;

    protected string $name = 'Installer';

    protected string $nameLower = 'installer';

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
        $this->app->register(RepositoryServiceProvider::class);
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

        if (!is_string($relativeConfigPath)) {
            return;
        }

        $configPath = module_path($this->name, $relativeConfigPath);

        if (!is_dir($configPath)) {  // Removed redundant is_string check since module_path returns string|null
            return;
        }

        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($configPath));

        /** @var SplFileInfo $file */
        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getExtension() === 'php') {
                $pathname = $file->getPathname();
                $relativePath = str_replace($configPath . DIRECTORY_SEPARATOR, '', $pathname);
                $cleanPath = str_replace([DIRECTORY_SEPARATOR, '.php'], ['.', ''], $relativePath);

                $configKey = $this->nameLower . '.' . $cleanPath;
                $key = ($relativePath === 'config.php') ? $this->nameLower : $configKey;

                $this->publishes([$pathname => config_path($relativePath)], 'config');
                $this->mergeConfigFrom($pathname, $key);
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
        if (!is_string($componentPath)) {
            return;
        }

        $componentNamespace = $this->module_namespace($this->name, $this->app_path($componentPath));
        Blade::componentNamespace($componentNamespace, $this->nameLower);
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array<string> Array of service names.
     */
    public function provides(): array
    {
        return [];
    }

    /**
     * Get the publishable view paths.
     *
     * @return array<string> Array of view paths.
     */
    private function getPublishableViewPaths(): array
    {
        $paths = [];
        $viewPaths = config('view.paths');

        if (!is_array($viewPaths)) {
            return $paths;
        }

        foreach ($viewPaths as $path) {
            if (!is_string($path)) {
                continue;
            }

            $modulePath = $path . '/modules/' . $this->nameLower;
            if (is_dir($modulePath)) {
                $paths[] = $modulePath;
            }
        }

        return $paths;
    }
}
