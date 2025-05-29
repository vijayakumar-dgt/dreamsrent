<?php

namespace Modules\GeneralSetting\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\GeneralSetting\Repositories\Contracts\EmailTemplateRepositoryInterface;
use Modules\GeneralSetting\Repositories\Contracts\GeneralSettingInterface;
use Modules\GeneralSetting\Repositories\Contracts\InsuranceSettingInterface;
use Modules\GeneralSetting\Repositories\Contracts\LocalizationInterface;
use Modules\GeneralSetting\Repositories\Contracts\SignatureSettingInterface;
use Modules\GeneralSetting\Repositories\Eloquent\EmailTemplateSettingRepository;
use Modules\GeneralSetting\Repositories\Eloquent\GeneralSettingRepository;
use Modules\GeneralSetting\Repositories\Eloquent\InsuranceSettingRepository;
use Modules\GeneralSetting\Repositories\Eloquent\LocalizationRepository;
use Modules\GeneralSetting\Repositories\Eloquent\SignatureSettingRepository;


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
        $this->app->bind(GeneralSettingInterface::class, GeneralSettingRepository::class);
        $this->app->bind(EmailTemplateRepositoryInterface::class, EmailTemplateSettingRepository::class);
        $this->app->bind(InsuranceSettingInterface::class, InsuranceSettingRepository::class);
        $this->app->bind(SignatureSettingInterface::class, SignatureSettingRepository::class);
        $this->app->bind(LocalizationInterface::class, LocalizationRepository::class);
    }    
}
