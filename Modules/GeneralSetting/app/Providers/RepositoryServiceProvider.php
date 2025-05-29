<?php

namespace Modules\GeneralSetting\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\GeneralSetting\Repositories\Contracts\EmailTemplateRepositoryInterface;
use Modules\GeneralSetting\Repositories\Eloquent\EmailTemplateSettingRepository;


class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     */
    public function register(): void {
        $this->registerBindings();
    }

    protected function registerBindings(): void
    {
        $this->app->bind(EmailTemplateRepositoryInterface::class, EmailTemplateSettingRepository::class);
    }    
}
