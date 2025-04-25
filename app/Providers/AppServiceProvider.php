<?php

namespace App\Providers;

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

            if (!isset($modulesStatus['Installer']) || $modulesStatus['Installer'] !== true) {
                $this->globalViews();
                $this->shareThemeAndLayout();
                $this->shareHeader();
                $this->shareFooter();
            }
        } else {
            $this->globalViews();
            $this->shareThemeAndLayout();
            $this->shareHeader();
            $this->shareFooter();
        }
    }

    public function globalViews()
    {
        $allLanguages = Language::select('languages.id', 'languages.rtl', 'translation_languages.code', 'translation_languages.name')
            ->join('translation_languages', 'languages.language_id', '=', 'translation_languages.id')
            ->where('languages.status', 1)
            ->get();

        view()->composer('*', function ($view) use ($allLanguages) {
            $user = Auth::user();
            $userDetails = null;

            if ($user) {
                $userDetails = DB::table('users')
                    ->select(
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

                if ($userDetails && $userDetails->profile_image) {
                    $filePath = storage_path('app/public/' . $userDetails->profile_image);
                    if (!File::exists($filePath)) {
                        $userDetails->profile_image = null;
                    }
                }
            }

            $permissions = getUserPermissions();
            $appLanguage = App::getLocale();
            $languageId    = getLanguageId($appLanguage);
            $copyright   = null;
            if ($languageId) {
               $key = 'copy_right_' . $languageId;
               $copyright = GeneralSetting::where('key', $key)->where('language_id', $languageId)->pluck('value')->first();
            }
            $view->with([
                'allLanguages' => $allLanguages,
                'userDetails' => $userDetails,
                'permissions' => $permissions,
                'copyright' => $copyright
            ]);
        });
    }

    public function shareThemeAndLayout()
    {
        view()->composer('*', function ($view) {
            $defaultTheme = GeneralSetting::where('key', 'default_theme')->first();
            $companyPhoneNumber = GeneralSetting::where('key', 'company_phone')->first();
            $companyEmail = GeneralSetting::where('key', 'company_email')->first();
            $companyName = GeneralSetting::where('key', 'organization_name')->first();

            $companyPhoneNumber = $companyPhoneNumber ? $companyPhoneNumber->value : '';
            $companyEmail = $companyEmail ? $companyEmail->value : '';
            $companyName = $companyName ? $companyName->value : '';
            $theme = $defaultTheme ? $defaultTheme->value : 1;

            $logoSetting = GeneralSetting::where('group_id',16)->pluck('value','key')->toArray();
            $logo = uploadedAsset(($logoSetting['logo_image'] ?? null), 'default_logo');
            $favicon = uploadedAsset(($logoSetting['favicon_image'] ?? null), 'default_favicon');
            $smallLogo = uploadedAsset(($logoSetting['small_image'] ?? null), 'default_small_logo');
            $view->with([
                'theme' => $theme,
                'layout' => "theme_{$theme}.app",
                'companyPhoneNumber' => $companyPhoneNumber,
                'companyEmail' => $companyEmail,
                'companyName' => $companyName,
                'logo' => $logo,
                'favicon' => $favicon,
                'smallLogo' => $smallLogo
            ]);
        });
    }

    public function shareHeader()
    {
        view()->composer(["theme_1.header", "theme_2.header"], function ($view) {
            $appLanguage = App::getLocale();
            $languageId = getLanguageId($appLanguage);

            $headers = Menu::where(['menu_type' => 'header', 'status' => 1, 'language_id' => $languageId])
                ->get(['id', 'name', 'menus']);

            $headers->transform(function ($header) {
                $menus = !empty($header->menus) ? json_decode($header->menus, true) : [];
                $filteredMenus = collect($menus)->where('status', true)->values()->all();
                $header->menus = $filteredMenus;
                return $header;
            });

            $view->with([
                'headers' => $headers
            ]);
        });
    }

    public function shareFooter()
    {
        view()->composer(["theme_1.footer", "theme_2.footer"], function ($view) {
            $appLanguage = App::getLocale();
            $languageId    = getLanguageId($appLanguage);

            $footers = Menu::where(['menu_type' => 'footer', 'status' => 1, 'language_id' => $languageId])
                ->get(['id', 'name', 'menus']);

            $footers->transform(function ($footer) {
                $menus = !empty($footer->menus) ? json_decode($footer->menus, true) : [];
                $filteredMenus = collect($menus)->where('status', true)->values()->all();
                $footer->menus = $filteredMenus;
                return $footer;
            });

            $view->with([
                'footers' => $footers,
            ]);
        });
    }

}
