<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\ServiceProvider;
use Modules\GeneralSetting\Models\GeneralSetting;
use Modules\GeneralSetting\Models\Language;
use Modules\GeneralSetting\Models\TranslationLanguage;
use Modules\MenuManagement\Models\Menu;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Route;
Use Artesaos\SEOTools\Facades\SEOMeta;
use Artesaos\SEOTools\Facades\OpenGraph;
use Illuminate\Support\Facades\Cache;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $modulesStatusPath = base_path('modules_statuses.json');

        if (File::exists($modulesStatusPath)) {
            $modulesStatus = json_decode(File::get($modulesStatusPath), true);

            // If installer is active, skip all DB-dependent boot logic
            if (isset($modulesStatus['Installer']) && $modulesStatus['Installer'] === true) {
                return; // Do nothing, skip boot
            }
        }

        // Safe to run DB logic here
        $this->globalViews();
        $this->shareSeo();
        $this->shareThemeAndLayout();
        $this->shareHeader();
        $this->shareFooter();
    }

    public function globalViews(): void
    {
        $allLanguages = Language::select(
            'languages.id',
            'languages.rtl',
            'languages.language_id',
            'translation_languages.code',
            'translation_languages.name'
        )
            ->join('translation_languages', 'languages.language_id', '=', 'translation_languages.id')
            ->where('languages.status', 1)
            ->get();

        view()->composer('*', function ($view) use ($allLanguages) {
            $user = Auth::user();
            $userDetails = null;

            if ($user) {
                $userDetails = User::select(
                    'users.id',
                    'users.name',
                    'users.email',
                    'user_details.profile_image',
                    'user_details.first_name',
                    'user_details.last_name'
                )
                    ->leftJoin('user_details', 'users.id', '=', 'user_details.user_id')
                    ->where('users.id', $user->id)
                    ->first();

                if ($userDetails && is_string($userDetails->profile_image) && $userDetails->profile_image !== '') {
                    $userDetails->profile_image = uploadedAsset($userDetails->profile_image, 'profile');
                }
            }

            $permissions = getUserPermissions();
            $appLanguage = App::getLocale();
            $languageId = getLanguageId($appLanguage);
            $copyright = null;
            if ($languageId) {
                $key = 'copy_right_' . $languageId;
                $copyright = GeneralSetting::where('key', $key)
                    ->where('language_id', $languageId)->value('value');
            }
            $view->with([
                'allLanguages' => $allLanguages,
                'userDetails' => $userDetails,
                'permissions' => $permissions,
                'copyright' => $copyright
            ]);
        });
    }

    public function shareThemeAndLayout(): void
    {
        view()->composer('*', function ($view) {
            $defaultTheme = GeneralSetting::where('key', 'default_theme')->first();
            $companyPhoneNumber = GeneralSetting::where('key', 'company_phone')->first();
            $companyEmail = GeneralSetting::where('key', 'company_email')->first();
            $companyName = GeneralSetting::where('key', 'organization_name')->first();

            $companyPhoneNumber = $companyPhoneNumber ? $companyPhoneNumber->value : '';
            $companyEmail = $companyEmail ? $companyEmail->value : '';
            $companyName = $companyName ? $companyName->value : 'Dreams Rent';
            $theme = $defaultTheme ? $defaultTheme->value : 1;
            $language_switcher = GeneralSetting::where('group_id', 5)->where('key', 'language_switcher')->first();
            $language_switcher = $language_switcher ? $language_switcher->value : 0;
            $logoSetting = GeneralSetting::where('group_id', 16)->pluck('value', 'key')->toArray();
            $logo = uploadedAsset(($logoSetting['logo_image'] ?? null), 'default_logo');
            $favicon = uploadedAsset(($logoSetting['favicon_image'] ?? null), 'default_favicon');
            $smallLogo = uploadedAsset(($logoSetting['small_image'] ?? null), 'default_small_logo');
            $view->with([
                'theme' => $theme,
                'layout' => "frontend.theme_{$theme}.app",
                'companyPhoneNumber' => $companyPhoneNumber,
                'companyEmail' => $companyEmail,
                'companyName' => $companyName,
                'logo' => $logo,
                'favicon' => $favicon,
                'smallLogo' => $smallLogo,
                'language_switcher' => $language_switcher
            ]);
        });
    }

    public function shareSeo(): void
    {
        view()->composer('*', function ($view) {
            $seoSettings = Cache::remember('seo_settings', 86400, function () {
                return GeneralSetting::where('group_id', 6)
                    ->pluck('value', 'key')->toArray();
            });

            $seoSettings['metaTitle'] = $seoSettings['metaTitle'] ?? 'Dreams Rent';
            $seoSettings['siteDescription'] = $seoSettings['siteDescription'] ?? '';
            $seoSettings['keywords'] = $seoSettings['keywords'] ?? '';
            $seoSettings['ogmetaTitle'] = $seoSettings['ogmetaTitle'] ?? 'Dreams Rent';
            $seoSettings['metaImage'] = uploadedAsset($seoSettings['metaImage'] ?? null, 'default_seo_image');
            $seoSettings['ogsiteDescription'] = $seoSettings['ogsiteDescription'] ?? '';

            // SEO Meta
            SEOMeta::addMeta('title', $seoSettings['metaTitle'], 'name');
            SEOMeta::setDescription($seoSettings['siteDescription']);
            SEOMeta::setKeywords(explode(',', $seoSettings['keywords']));
            SEOMeta::setRobots('noindex, nofollow');

            // Open Graph
            OpenGraph::setTitle($seoSettings['ogmetaTitle']);
            OpenGraph::setDescription($seoSettings['ogsiteDescription']);
            OpenGraph::addProperty('image', $seoSettings['metaImage']);
        });
    }

    public function shareHeader(): void
    {
        view()->composer(["frontend.theme_1.partials.header", "frontend.theme_2.header"], function ($view) {
            $appLanguage = App::getLocale();
            $languageId = getLanguageId($appLanguage);

            $headers = Menu::where(['menu_type' => 'header', 'status' => 1, 'language_id' => $languageId])
                ->get(['id', 'name', 'menus']);

            $headers->transform(function ($header) {
                $menus = [];
                if (!empty($header->menus)) {
                    $decoded = json_decode($header->menus, true);
                    if (is_array($decoded)) {
                        $menus = $decoded;
                    }
                }
                $filteredMenus = collect($menus)
                    ->filter(fn($menu) => isset($menu['status']) && $menu['status'] === true)
                    ->values()
                    ->all();

                $header->menus_array = $filteredMenus;

                return $header;
            });

            $view->with([
                'headers' => $headers
            ]);
        });
    }

    public function shareFooter(): void
    {
        view()->composer(["frontend.theme_1.partials.footer", "frontend.theme_2.footer"], function ($view) {
            $appLanguage = App::getLocale();
            $languageId = getLanguageId($appLanguage);

            $footers = Menu::where(['menu_type' => 'footer', 'status' => 1, 'language_id' => $languageId])
                ->get(['id', 'name', 'menus']);

            $footers->transform(function ($footer) {
                $menus = [];
                if (!empty($footer->menus)) {
                    $decoded = json_decode($footer->menus, true);
                    if (is_array($decoded)) {
                        $menus = $decoded;
                    }
                }
                $filteredMenus = collect($menus)
                    ->filter(function ($menu) {
                        return !empty($menu['status']);
                    })
                    ->values()
                    ->all();

                $footer->parsed_menus = $filteredMenus;
                return $footer;
            });

            $view->with([
                'footers' => $footers,
            ]);
        });
    }
}
