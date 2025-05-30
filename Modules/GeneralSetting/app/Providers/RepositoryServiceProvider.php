<?php

namespace Modules\GeneralSetting\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\GeneralSetting\Repositories\Contracts\CommunicationSettingInterface;
use Modules\GeneralSetting\Repositories\Contracts\CurrencySettingInterface;
use Modules\GeneralSetting\Repositories\Contracts\DbbackupInterface;
use Modules\GeneralSetting\Repositories\Contracts\EmailTemplateRepositoryInterface;
use Modules\GeneralSetting\Repositories\Contracts\BlogCategoryRepositoryInterface;
use Modules\GeneralSetting\Repositories\Contracts\FaqInterface;
use Modules\GeneralSetting\Repositories\Contracts\GeneralSettingInterface;
use Modules\GeneralSetting\Repositories\Contracts\InsuranceSettingInterface;
use Modules\GeneralSetting\Repositories\Contracts\LanguageSettingInterface;
use Modules\GeneralSetting\Repositories\Contracts\LocalizationInterface;
use Modules\GeneralSetting\Repositories\Contracts\SignatureSettingInterface;
use Modules\GeneralSetting\Repositories\Contracts\SitemapSettingInterface;
use Modules\GeneralSetting\Repositories\Contracts\TaxRateSettingInterface;
use Modules\GeneralSetting\Repositories\Contracts\TestimonialInterface;
use Modules\GeneralSetting\Repositories\Eloquent\CommunicationSettingRepository;
use Modules\GeneralSetting\Repositories\Eloquent\CurrencySettingRepository;
use Modules\GeneralSetting\Repositories\Eloquent\DbbackupRepository;
use Modules\GeneralSetting\Repositories\Eloquent\EmailTemplateSettingRepository;
use Modules\GeneralSetting\Repositories\Eloquent\FaqRepository;
use Modules\GeneralSetting\Repositories\Eloquent\GeneralSettingRepository;
use Modules\GeneralSetting\Repositories\Eloquent\InsuranceSettingRepository;
use Modules\GeneralSetting\Repositories\Eloquent\LocalizationRepository;
use Modules\GeneralSetting\Repositories\Eloquent\SignatureSettingRepository;
use Modules\GeneralSetting\Repositories\Eloquent\LanguageSettingRepository;
use Modules\GeneralSetting\Repositories\Eloquent\SitemapSettingRepository;
use Modules\GeneralSetting\Repositories\Eloquent\TaxRateSettingRepository;
use Modules\GeneralSetting\Repositories\Eloquent\TestimonialRepository;
use Modules\GeneralSetting\Repositories\Eloquent\BlogCategoryRepository;


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
        $this->app->bind(LanguageSettingInterface::class, LanguageSettingRepository::class);
        $this->app->bind(EmailTemplateRepositoryInterface::class, EmailTemplateSettingRepository::class);
        $this->app->bind(InsuranceSettingInterface::class, InsuranceSettingRepository::class);
        $this->app->bind(SignatureSettingInterface::class, SignatureSettingRepository::class);
        $this->app->bind(LocalizationInterface::class, LocalizationRepository::class);
        $this->app->bind(SitemapSettingInterface::class, SitemapSettingRepository::class);
        $this->app->bind(CommunicationSettingInterface::class, CommunicationSettingRepository::class);
        $this->app->bind(TaxRateSettingInterface::class, TaxRateSettingRepository::class);
        $this->app->bind(CurrencySettingInterface::class, CurrencySettingRepository::class);
        $this->app->bind(DbbackupInterface::class, DbbackupRepository::class);
        $this->app->bind(TestimonialInterface::class, TestimonialRepository::class);
        $this->app->bind(BlogCategoryRepositoryInterface::class, BlogCategoryRepository::class);
        $this->app->bind(FaqInterface::class, FaqRepository::class);
    }    
}
