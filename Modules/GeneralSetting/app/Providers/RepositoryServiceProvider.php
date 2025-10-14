<?php

namespace Modules\GeneralSetting\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\GeneralSetting\Repositories\Contracts\AdminProfileInterface;
use Modules\GeneralSetting\Repositories\Contracts\BlogCategoryRepositoryInterface;
use Modules\GeneralSetting\Repositories\Contracts\CommunicationSettingInterface;
use Modules\GeneralSetting\Repositories\Contracts\CurrencySettingInterface;
use Modules\GeneralSetting\Repositories\Contracts\DbbackupInterface;
use Modules\GeneralSetting\Repositories\Contracts\EmailTemplateRepositoryInterface;
use Modules\GeneralSetting\Repositories\Contracts\AppearanceSettingRepositoryInterface;
use Modules\GeneralSetting\Repositories\Contracts\BusinessSettingRepositoryInterface;
use Modules\GeneralSetting\Repositories\Contracts\CompanySettingRepositoryInterface;
use Modules\GeneralSetting\Repositories\Contracts\ContentSettingRepositoryInterface;
use Modules\GeneralSetting\Repositories\Contracts\FaqInterface;
use Modules\GeneralSetting\Repositories\Contracts\PaymentSettingRepositoryInterface;
use Modules\GeneralSetting\Repositories\Contracts\SecuritySettingRepositoryInterface;
use Modules\GeneralSetting\Repositories\Contracts\InsuranceSettingInterface;
use Modules\GeneralSetting\Repositories\Contracts\LanguageSettingInterface;
use Modules\GeneralSetting\Repositories\Contracts\LocalizationInterface;
use Modules\GeneralSetting\Repositories\Contracts\SignatureSettingInterface;
use Modules\GeneralSetting\Repositories\Contracts\SitemapSettingInterface;
use Modules\GeneralSetting\Repositories\Contracts\TaxRateSettingInterface;
use Modules\GeneralSetting\Repositories\Contracts\TestimonialInterface;
use Modules\GeneralSetting\Repositories\Eloquent\AdminProfileRepository;
use Modules\GeneralSetting\Repositories\Eloquent\BlogCategoryRepository;
use Modules\GeneralSetting\Repositories\Eloquent\CommunicationSettingRepository;
use Modules\GeneralSetting\Repositories\Eloquent\CurrencySettingRepository;
use Modules\GeneralSetting\Repositories\Eloquent\DbbackupRepository;
use Modules\GeneralSetting\Repositories\Eloquent\EmailTemplateSettingRepository;
use Modules\GeneralSetting\Repositories\Eloquent\AppearanceSettingRepository;
use Modules\GeneralSetting\Repositories\Eloquent\BusinessSettingRepository;
use Modules\GeneralSetting\Repositories\Eloquent\CompanySettingRepository;
use Modules\GeneralSetting\Repositories\Eloquent\ContentSettingRepository;
use Modules\GeneralSetting\Repositories\Eloquent\FaqRepository;
use Modules\GeneralSetting\Repositories\Eloquent\PaymentSettingRepository;
use Modules\GeneralSetting\Repositories\Eloquent\SecuritySettingRepository;
use Modules\GeneralSetting\Repositories\Eloquent\InsuranceSettingRepository;
use Modules\GeneralSetting\Repositories\Eloquent\LanguageSettingRepository;
use Modules\GeneralSetting\Repositories\Eloquent\LocalizationRepository;
use Modules\GeneralSetting\Repositories\Eloquent\SignatureSettingRepository;
use Modules\GeneralSetting\Repositories\Eloquent\SitemapSettingRepository;
use Modules\GeneralSetting\Repositories\Eloquent\TaxRateSettingRepository;
use Modules\GeneralSetting\Repositories\Eloquent\TestimonialRepository;

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
        $this->app->bind(AdminProfileInterface::class, AdminProfileRepository::class);
        $this->app->bind(CompanySettingRepositoryInterface::class, CompanySettingRepository::class);
        $this->app->bind(AppearanceSettingRepositoryInterface::class, AppearanceSettingRepository::class);
        $this->app->bind(BusinessSettingRepositoryInterface::class, BusinessSettingRepository::class);
        $this->app->bind(SecuritySettingRepositoryInterface::class, SecuritySettingRepository::class);
        $this->app->bind(ContentSettingRepositoryInterface::class, ContentSettingRepository::class);
        $this->app->bind(PaymentSettingRepositoryInterface::class, PaymentSettingRepository::class);
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
