<?php

use Illuminate\Support\Facades\Route;
use Modules\GeneralSetting\Http\Controllers\Admin\AdminProfileController;
use Modules\GeneralSetting\Http\Controllers\Admin\BlogsController;
use Modules\GeneralSetting\Http\Controllers\Admin\CommunicationSettingController;
use Modules\GeneralSetting\Http\Controllers\Admin\CurrencyController;
use Modules\GeneralSetting\Http\Controllers\Admin\DbbackupController;
use Modules\GeneralSetting\Http\Controllers\Admin\EmailTemplateController;
use Modules\GeneralSetting\Http\Controllers\Admin\AiConfigurationController;
use Modules\GeneralSetting\Http\Controllers\Admin\CompanySettingsController;
use Modules\GeneralSetting\Http\Controllers\Admin\CookiesSettingsController;
use Modules\GeneralSetting\Http\Controllers\Admin\FaqController;
use Modules\GeneralSetting\Http\Controllers\Admin\InsuranceController;
use Modules\GeneralSetting\Http\Controllers\Admin\InvoiceSettingsController;
use Modules\GeneralSetting\Http\Controllers\Admin\LanguageController;
use Modules\GeneralSetting\Http\Controllers\Admin\LocalizationController;
use Modules\GeneralSetting\Http\Controllers\Admin\LogoSettingsController;
use Modules\GeneralSetting\Http\Controllers\Admin\MaintenanceSettingsController;
use Modules\GeneralSetting\Http\Controllers\Admin\NotificationSettingsController;
use Modules\GeneralSetting\Http\Controllers\Admin\OtpSettingsController;
use Modules\GeneralSetting\Http\Controllers\Admin\PaymentSettingsController;
use Modules\GeneralSetting\Http\Controllers\Admin\PrefixSettingsController;
use Modules\GeneralSetting\Http\Controllers\Admin\RentalSettingsController;
use Modules\GeneralSetting\Http\Controllers\Admin\SecuritySettingsController;
use Modules\GeneralSetting\Http\Controllers\Admin\SeoSettingsController;
use Modules\GeneralSetting\Http\Controllers\Admin\SignatureSettingsController;
use Modules\GeneralSetting\Http\Controllers\Admin\SitemapController;
use Modules\GeneralSetting\Http\Controllers\Admin\StorageSettingsController;
use Modules\GeneralSetting\Http\Controllers\Admin\TaxRateController;
use Modules\GeneralSetting\Http\Controllers\Admin\TestimonialController;
use Modules\GeneralSetting\Http\Controllers\Admin\ThemeSettingsController;

Route::group(['middleware' => ['setLocale', 'checkInstallerStatus', 'securityHeader']], function () {

    Route::group(['prefix' => 'admin/settings', 'middleware' => 'admin'], function () {
        Route::get('logo', [LogoSettingsController::class, 'logo'])->name('admin.logo-settings')->middleware('permission');
        Route::get('company', [CompanySettingsController::class, 'company'])->name('admin.company-settings')->middleware('permission');
        Route::post('company/store', [CompanySettingsController::class, 'store'])->name('admin.company-store-settings');
        Route::post('company/list/new', [CompanySettingsController::class, 'listCompany'])->name('admin.company-list-new-settings');
        Route::post('company/list', [CompanySettingsController::class, 'list'])->name('admin.company-list-settings');
        Route::get('profile', [AdminProfileController::class, 'adminProfile'])->name('admin.profile-settings');
        Route::get('notifications', [NotificationSettingsController::class, 'notifications'])->name('admin.notifications-settings')->middleware('permission');
        Route::post('notifications/store', [NotificationSettingsController::class, 'storeNotificationSettings'])->name('admin.storenotifications-settings');

        //seosetup
        Route::get('seosetup', [SeoSettingsController::class, 'seosetup'])->name('admin.seosetup-settings')->middleware('permission');
        Route::post('seosetup/store', [SeoSettingsController::class, 'storeSeoSetupSettings'])->name('admin.seosetup-store-settings');

        //cookies
        Route::get('gdpr-cookies', [CookiesSettingsController::class, 'gdprCookies'])->name('admin.gdpr-cookies-settings')->middleware('permission');
        Route::post('cookies/store', [CookiesSettingsController::class, 'storeCookiesSettings'])->name('admin.gdpr-cookies-store-settings');
        Route::post('cookies/list', [CookiesSettingsController::class, 'cookiesSettingsList'])->name('admin.gdpr-cookies-list-settings');

        // Security
        Route::get('security', [SecuritySettingsController::class, 'security'])->name('admin.security-settings')->middleware('permission');
        Route::post('check-current-password', [SecuritySettingsController::class, 'checkCurrentPassword'])->name('admin.check-current-password');
        Route::post('update-password', [SecuritySettingsController::class, 'updatePassword'])->name('admin.update-password');
        Route::post('check-current-phonenumber', [SecuritySettingsController::class, 'checkCurrentPhoneNumber'])->name('admin.check-current-phonenumber');
        Route::post('update-phone-number', [SecuritySettingsController::class, 'updatePhoneNumber'])->name('admin.update-phone-number');
        Route::post('update-email', [SecuritySettingsController::class, 'updateEmail'])->name('admin.update-email');
        Route::get('get-security-settings', [SecuritySettingsController::class, 'getSecuritySettings'])->name('admin.get-security-settings');
        Route::post('logout-device', [SecuritySettingsController::class, 'logoutDevice'])->name('admin.logout-device');
        Route::post('update-google-auth', [SecuritySettingsController::class, 'updateGoogleAuth'])->name('admin.update-google-auth');
        Route::get('prefixes', [PrefixSettingsController::class, 'prefixes'])->name('admin.prefixes-settings');

        // Currency
        Route::get('currencies', [CurrencyController::class, 'index'])->name('admin.currencies')->middleware('permission');
        Route::post('save_currency', [CurrencyController::class, 'saveCurrency']);
        Route::post('get_currencies', [CurrencyController::class, 'getCurrencies']);
        Route::get('edit_currency/{id}', [CurrencyController::class, 'editCurrency']);
        Route::post('delete-currency', [CurrencyController::class, 'deleteCurrency']);

        //Localization
        Route::get('localization', [LocalizationController::class, 'index'])->name('admin.localization')->middleware('permission');
        Route::get('get-timezones', [LocalizationController::class, 'getTimezones']);
        Route::post('update-localization', [LocalizationController::class, 'updateLocalization']);
        Route::get('get-timezone', [LocalizationController::class, 'getTimezone']);

        //maintenance settings
        Route::get('maintenance', [MaintenanceSettingsController::class, 'maintenance'])->name('admin.maintenance-settings')->middleware('permission');
        Route::post('maintenance/update', [MaintenanceSettingsController::class, 'storeMaintenanceSettings'])->name('admin.maintenanceupdate-settings');

        // Common general-settings list
        Route::post('list', [CompanySettingsController::class, 'list']);

        // Prefixes
        Route::get('prefixes', [PrefixSettingsController::class, 'prefixes'])->name('admin.prefixes-settings')->middleware('permission');
        Route::post('update-prefixes', [PrefixSettingsController::class, 'updatePrefixes'])->name('admin.update-prefixes');

        // AI Configuration
        Route::get('ai-configuration', [AiConfigurationController::class, 'aiConfiguration'])->name('admin.ai-configuration')->middleware('permission');
        Route::post('update-ai-configuration', [AiConfigurationController::class, 'updateAiConfiguration'])->name('admin.update-ai-configuration');

        // Insuarnce
        Route::get('insurances', [InsuranceController::class, 'index'])->name('insurance.index')->middleware('permission');
        Route::post('insurance/save', [InsuranceController::class, 'store'])->name('insurance.store');
        Route::post('insurance/list', [InsuranceController::class, 'list'])->name('insurance.list');
        Route::get('insurance/edit/{id}', [InsuranceController::class, 'edit'])->name('insurance.edit');
        Route::post('insurance/delete', [InsuranceController::class, 'delete'])->name('insurance.delete');

        //Email Templates
        Route::get('email_templates', [EmailTemplateController::class, 'index'])->name('email_templates.index')->middleware('permission');
        Route::post('save_email_template', [EmailTemplateController::class, 'store'])->name('email_templates.store');
        Route::post('get_emailtemplates', [EmailTemplateController::class, 'getEmailTemplates']);
        Route::get('get_email_template/{id}', [EmailTemplateController::class, 'getEmailTemplate']);
        Route::post('delete-emailtemplate', [EmailTemplateController::class, 'deleteEmailTemplate']);
        Route::get('get_tags/{id}', [EmailTemplateController::class, 'getTags']);

        //smsGateway
        Route::get('sms-gateway', [CommunicationSettingController::class, 'smsGateway'])->name('admin.smsGateway-settings')->middleware('permission');
        Route::post('status-update', [CommunicationSettingController::class, 'statusUpdate'])->name('admin.statusUpdate-settings');
        Route::post('sms-list', [CommunicationSettingController::class, 'smsList'])->name('admin.smsList-settings');
        Route::post('sms-store', [CommunicationSettingController::class, 'storeCommunicationSetting'])->name('admin.smsstore-settings');

        //storagesettings
        Route::get('storage', [StorageSettingsController::class, 'storage'])->name('admin.storage-settings')->middleware('permission');

        //Sitemap Settings
        Route::get('sitemap', [SitemapController::class, 'index'])->name('admin.sitemap')->middleware('permission');
        Route::post('save-sitemap-url', [SitemapController::class, 'store']);
        Route::post('get_sitemap_urls', [SitemapController::class, 'getSitemapUrls']);
        Route::post('delete-sitemapurl', [SitemapController::class, 'deleteSitemapUrl']);

        // Email settings
        Route::get('email-settings', [CommunicationSettingController::class, 'emailSettings'])->name('admin.email-settings')->middleware('permission');
        Route::post('email-settings-store', [CommunicationSettingController::class, 'storeCommunicationSetting'])->name('admin.email-settings-store');
        Route::post('email-settings-list', [CommunicationSettingController::class, 'smsList'])->name('admin.email-settings-list');
        Route::post('send-test-mail', [CommunicationSettingController::class, 'sendTestMail'])->name('admin.send-test-mail');

        // storage settings
        Route::post('storageupdate', [StorageSettingsController::class, 'storageStatusUpdate'])->name('admin.storageupdate-settings');
        Route::post('aws/store', [StorageSettingsController::class, 'storeAwsSettings'])->name('admin.storawsStoreage-settings');



        //Language
        Route::get('languages', [LanguageController::class, 'index'])->name('admin.languages')->middleware('permission');
        Route::post('add_language', [LanguageController::class, 'addLanguage']);
        Route::get('get_languages', [LanguageController::class, 'getLanguages']);
        Route::post('update_language_settings', [LanguageController::class, 'updateLanguageSettings'])->name('admin.update_language_settings');
        Route::get('language', [LanguageController::class, 'language'])->name('admin.language');
        Route::get('get-language-modules', [LanguageController::class, 'getLanguageModules'])->name('admin.get-language-modules');
        Route::post('edit-module-language', [LanguageController::class, 'editModuleLanguage'])->name('admin.edit-module-language');
        Route::post('update-module-language', [LanguageController::class, 'updateModuleLanguage'])->name('admin.update-module-language');
        Route::post('delete-language', [LanguageController::class, 'deleteLanguage'])->name('admin.delete-language');

        // Tax Rate
        Route::get('tax-rates', [TaxRateController::class, 'index'])->name('admin.tax-rates')->middleware('permission');
        Route::post('tax-rate/store', [TaxRateController::class, 'store'])->name('admin.tax-rate-store');
        Route::get('tax-rate/list', [TaxRateController::class, 'list'])->name('admin.tax-rate-list');
        Route::get('tax-rate/edit/{id}', [TaxRateController::class, 'edit'])->name('admin.tax-rate-edit');
        Route::post('tax-rate/delete', [TaxRateController::class, 'delete'])->name('admin.tax-rate-delete');
        Route::get('get-tax-rates', [TaxRateController::class, 'getTaxRates']);

        // Tax Group
        Route::post('tax-group/store', [TaxRateController::class, 'taxGroupStore'])->name('admin.tax-group-store')->middleware('permission');
        Route::get('tax-group/list', [TaxRateController::class, 'taxGroupList'])->name('admin.tax-group-list');
        Route::get('tax-group/edit/{id}', [TaxRateController::class, 'taxGroupEdit'])->name('admin.tax-group-edit');
        Route::post('tax-group/delete', [TaxRateController::class, 'taxGroupDelete'])->name('admin.tax-group-delete');

        //signature
        Route::get('signature', [SignatureSettingsController::class, 'signature'])->name('admin.signature-settings')->middleware('permission');
        Route::post('signatures/store', [SignatureSettingsController::class, 'store'])->name('signatures.store');
        Route::post('signatures/update', [SignatureSettingsController::class, 'update'])->name('signatures.update');
        Route::get('signatures/list', [SignatureSettingsController::class, 'index'])->name('signatures.index');
        Route::post('signatures/delete', [SignatureSettingsController::class, 'destroy']);

        //clear cache
        Route::get('clear-cache', [SignatureSettingsController::class, 'clearCache'])->name('admin.clearCache-settings');
        Route::post('clear', [SignatureSettingsController::class, 'clear'])->name('admin.clear-cache');

        // Payment settings
        Route::get('payment-methods', [PaymentSettingsController::class, 'paymentIndex'])->name('admin.paymentIndex-settings')->middleware('permission');
        Route::post('updatepaymentSettings', [PaymentSettingsController::class, 'updatepaymentSettings'])->name('admin.updatepayment-settings');
        Route::post('updatepaymentStatus', [PaymentSettingsController::class, 'updatepaymentStatus'])->name('admin.updatepaymentStatus-settings');
        Route::get('payment-list', [PaymentSettingsController::class, 'paymentList'])->name('admin.paymentList-settings');

        // Invoice settings
        Route::get('invoice-settings', [InvoiceSettingsController::class, 'invoiceSettings'])->name('admin.invoiceSettings-settings')->middleware('permission');
        Route::post('invoice-settings/store', [InvoiceSettingsController::class, 'storeInvoiceSettings'])->name('admin.storeInvoiceSettings-settings');

        // Theme Settings
        Route::get('theme', [ThemeSettingsController::class, 'themeSettings'])->name('admin.theme-settings');
        Route::post('update-theme-settings', [ThemeSettingsController::class, 'updateThemeSettings']);

        // otp settings
        Route::get('otp-settings', [OtpSettingsController::class, 'otpSettings'])->name('admin.otp-settings')->middleware('permission');
        Route::post('otp/update', [OtpSettingsController::class, 'storeOtpSettings'])->name('admin.otpstore-settings');

        //rental Setting
        Route::get('rental-settings', [RentalSettingsController::class, 'rentalSettings'])->name('admin.rental-settings');
        Route::post('rental/update', [RentalSettingsController::class, 'storeRentalSettings'])->name('admin.rentalstore-settings');

        //database Settings
        Route::get('database-settings', [DbbackupController::class, 'datebaseSettings'])->name('admin.database-settings')->middleware('permission');
        Route::get('/dbbackups', [DbbackupController::class, 'listBackups'])->name('listBackups');
        Route::post('/backups/delete', [DbbackupController::class, 'deleteBackup']);

        //system Settings
        Route::get('system-backup-settings', [DbbackupController::class, 'systemBackupSettings'])->name('admin.system-backup-settings')->middleware('permission');
        Route::get('system-backup/list', [DbbackupController::class, 'listSystemBackups'])->name('listSystemBackups');
        Route::post('system-backup/delete', [DbbackupController::class, 'deleteSystemBackup']);

        //logo-setting
        Route::get('logo-settings', [LogoSettingsController::class, 'logoSettings'])->name('admin.logo-settings');
        Route::post('logo/store', [LogoSettingsController::class, 'storeLogoSettings'])->name('admin.logostore-settings');
    });

    Route::group(['prefix' => 'admin', 'middleware' => 'admin'], function () {
        Route::post('update_profile', [AdminProfileController::class, 'updateProfile'])->name('admin.updateprofile-settings');
        Route::get('profile/{id}', [AdminProfileController::class, 'getProfile']);
        Route::post('delete-account/{id}', [AdminProfileController::class, 'deleteAccount']);

        //Faq
        Route::get('faq', [FaqController::class, 'faq'])->name('admin.faq')->middleware('permission');
        Route::post('faq/store', [FaqController::class, 'faqStore'])->name('admin.faqstore');
        Route::get('faq/list', [FaqController::class, 'faqList'])->name('admin.faqlist');
        Route::post('faq/update', [FaqController::class, 'faqUpdate'])->name('admin.faqupdate');
        Route::post('faq/delete', [FaqController::class, 'faqDelete'])->name('admin.faqdelete');

        //how it works
        Route::get('how-it-works', [FaqController::class, 'howItWorks'])->name('admin.howItWorks')->middleware('permission');
        Route::post('how-it-works/update', [FaqController::class, 'howItWorksUpdate'])->name('admin.howItWorksUpdate');
        Route::post('how-it-works/list', [FaqController::class, 'howItWorksList'])->name('admin.howItWorksList');

        //copyright
        Route::get('copy-right', [FaqController::class, 'copyright'])->name('admin.copyright')->middleware('permission');
        Route::post('copyright/update', [FaqController::class, 'copyrightUpdate'])->name('admin.copyrightUpdate');
        Route::post('copyright/list', [FaqController::class, 'copyrightList'])->name('admin.copyrightList');

        //testimoials
        Route::get('testimonials', [TestimonialController::class, 'testimonials'])->name('admin.testimoials')->middleware('permission');
        Route::post('testimonials/store', [TestimonialController::class, 'testimoialStore'])->name('admin.testimoialsStore');
        Route::get('testimonials/list', [TestimonialController::class, 'testimoiallist'])->name('admin.testimoialslist');
        Route::post('testimonials/update', [TestimonialController::class, 'updateTestimonial'])->name('admin.testimoialsupdate');
        Route::post('testimonials/delete', [TestimonialController::class, 'deleteTestimonial'])->name('admin.testimoialsdelete');
    });

    Route::group(['prefix' => 'admin/content', 'middleware' => 'admin'], function () {
        //category
        Route::get('/blog-category', [BlogsController::class, 'blogCategory'])->name('admin.blog-category')->middleware('permission');
        Route::post('/categories', [BlogsController::class, 'categoryStore'])->name('categories.store');
        Route::put('/categories/{id}', [BlogsController::class, 'categoryUpdate'])->name('categories.update');
        Route::post('/categories/{id}', [BlogsController::class, 'categoryDestroy'])->name('categories.destroy');

        //Tags
        Route::get('/blog-tags', [BlogsController::class, 'blogTags'])->name('admin.blog-tags')->middleware('permission');
        Route::post('/tags', [BlogsController::class, 'tagStore'])->name('tags.store');
        Route::put('/tags/{id}', [BlogsController::class, 'tagUpdate'])->name('tags.update');
        Route::post('/tags/{id}', [BlogsController::class, 'tagDestroy'])->name('tags.destroy');

        //Blog Comments
        Route::get('/blog-comments', [BlogsController::class, 'blogComments'])->name('admin.blog-comments')->middleware('permission');

        //Blog Post
        Route::get('/blogs', [BlogsController::class, 'blogs'])->name('admin.blogs')->middleware('permission');
        Route::get('/add-blog', [BlogsController::class, 'blogAdd'])->name('admin.blog-add')->middleware('permission');
        Route::post('/blog-store', [BlogsController::class, 'blogStore'])->name('blog.store');
        Route::get('/blogs/{id}', [BlogsController::class, 'blogEdit'])->name('blog.edit')->middleware('permission');
        Route::put('/blog/{id}', [BlogsController::class, 'blogUpdate'])->name('blog.update');
        Route::post('/blog/{id}', [BlogsController::class, 'blogDestroy'])->name('blog.destroy');
        Route::get('/blog-details/{id}', [BlogsController::class, 'blogDetails'])->name('blog.details')->middleware('permission');
    });

    Route::post('get-vehicle-insurances', [InsuranceController::class, 'getVehicleInsurances']);
});
