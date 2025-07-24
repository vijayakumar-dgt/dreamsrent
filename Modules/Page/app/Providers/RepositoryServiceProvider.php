<?php

namespace Modules\Page\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Page\Repositories\Contracts\PageInterface;
use Modules\Page\Repositories\Contracts\SectionInterface;
use Modules\Page\Repositories\Eloquent\PageRepository;
use Modules\Page\Repositories\Eloquent\SectionRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     */
    public function register(): void
    {
        $this->registerBindings();
    }

    protected function registerBindings(): void
    {
        $this->app->bind(SectionInterface::class, SectionRepository::class);
        $this->app->bind(PageInterface::class, PageRepository::class);
    }
}
