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
   /**
 * Register config.
 */
    protected function registerConfig(): void
    {
        $relativeConfigPath = config('modules.paths.generator.config.path');

        // Single check for both type and non-empty string
        if (!is_string($relativeConfigPath) || trim($relativeConfigPath) === '') {
            return;
        }

        $configPath = module_path($this->name, $relativeConfigPath);

        // Verify path exists and is a directory
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

        // Get component path from config and prepare namespace
        $rawComponentPath = config('modules.paths.generator.component-class.path');
        $componentPath = is_string($rawComponentPath) ? $rawComponentPath : null;
        $componentNamespace = $this->module_namespace(
            $this->name,
            $this->app_path($componentPath)
        );
        Blade::componentNamespace($componentNamespace, $this->nameLower);
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array<int, class-string>
     */
    public function provides(): array
    {
        return [];
    }

    /**
     * Get publishable view paths.
     *
     * @return array<int, string>
     */
    private function getPublishableViewPaths(): array
    {
        $paths = [];
        // Ensure view paths is an array before iterating
        $rawPaths = config('view.paths');
        $viewPaths = is_array($rawPaths) ? $rawPaths : [];

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
