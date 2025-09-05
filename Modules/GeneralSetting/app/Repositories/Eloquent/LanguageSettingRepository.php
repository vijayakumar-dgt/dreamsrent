<?php

namespace Modules\GeneralSetting\Repositories\Eloquent;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Lang;
use Modules\GeneralSetting\Models\Language;
use Modules\GeneralSetting\Models\TranslationLanguage;
use Modules\GeneralSetting\Repositories\Contracts\LanguageSettingInterface;
use RecursiveArrayIterator;
use RecursiveIteratorIterator;

class LanguageSettingRepository implements LanguageSettingInterface
{
    public function index()
    {
        return [
            'translationLanguages' => TranslationLanguage::where('status', 1)->get()
        ];
    }

    public function addLanguage(array $data): array
    {
        $response = [];

        $languageTranslation = TranslationLanguage::find($data['lang_id']);

        if (!$languageTranslation) {
            $response = [
                'status'  => 'error',
                'code'    => 422,
                'message' => __('admin.general_settings.language_not_found')
            ];
        } elseif (Language::where('language_id', $languageTranslation->id)->exists()) {
            $response = [
                'status'  => 'error',
                'code'    => 422,
                'message' => __('admin.general_settings.language_already_exist')
            ];
        } else {
            try {
                $language = Language::create(['language_id' => $languageTranslation->id]);
                $langPath = base_path('resources/lang/' . $languageTranslation->code);

                if (!file_exists($langPath)) {
                    mkdir($langPath, 0777, true);
                }

                $this->initializeLanguageFiles($languageTranslation->code);

                $response = [
                    'status'  => 'success',
                    'code'    => 200,
                    'message' => __('admin.general_settings.language_added_successfully')
                ];
            } catch (\Exception $e) {
                $response = [
                    'status'  => 'error',
                    'code'    => 422,
                    'message' => __('admin.general_settings.retrive_error'),
                    'error'   => $e->getMessage()
                ];
            }
        }

        return $response;
    }

    public function getLanguages(array $filters = []): array
    {
        $languages = Language::query();

        if (isset($filters['search']) && $filters['search'] != "") {
            $languages->where(function ($query) use ($filters) {
                $query->whereHas('transLang', function ($query) use ($filters) {
                    $query->where('name', 'like', "%{$filters['search']}%")
                        ->orWhere('code', 'like', "%{$filters['search']}%");
                });
            });
        }

        $languages = $languages->with('transLang')->get();
        $langDefaultFiles = ['admin', 'web'];
        $responseArray = [];
        $totalKeys = $this->countTotalTranslationKeys($langDefaultFiles);

        foreach ($languages as $language) {
            if (!$language->transLang) {
                continue;
            }

            $translatedCount = $this->countTranslatedKeys($language->transLang->code, $langDefaultFiles);
            $progress = $totalKeys > 0 ? round(($translatedCount / $totalKeys) * 100, 2) : 0;

            $responseArray[$language->transLang->code] = [
                'id'              => $language->id,
                'language_name'   => $language->transLang->name,
                'lang_img'        => url('backend/assets/img/flags/' . $language->transLang->code . '.svg'),
                'lang_code'       => $language->transLang->code,
                'lang_rtl'        => $language->rtl,
                'default'         => $language->default,
                'status'          => $language->status,
                'total_keys'      => $totalKeys,
                'translated_keys' => $translatedCount,
                'progress'        => $progress
            ];
        }

        return [
            'status'  => 'success',
            'code'    => 200,
            'message' => __('admin.general_settings.language_fetched_successfully'),
            'data'    => $responseArray
        ];
    }

    public function updateLanguageSettings(int $id, array $data): array
    {
        try {
            $language = Language::findOrFail($id);
            $languageCode = $language->transLang->code ?? null;
            $field = $data['field'];

            if ($field == 'default') {
                Language::where('default', 1)->update(['default' => 0]);
                session()->forget(['app_locale', 'app_locale_user']);
                session(['app_locale' => $languageCode, 'app_locale_user' => $languageCode]);

                if (Auth::guard('admin')->check()) {
                    Auth::guard('admin')->user()->update(['language_id' => $language->language_id]);
                }
            }

            $language->update([$field => $data['value']]);

            return [
                'status'  => 'success',
                'code'    => 200,
                'message' => __('admin.general_settings.language_updated_successfully')
            ];
        } catch (\Throwable $th) {
            return [
                'status'  => 'error',
                'code'    => 422,
                'message' => __('admin.general_settings.retrive_error'),
                'error'   => $th->getMessage()
            ];
        }
    }

    public function changeLanguage(string $languageCode): array
    {
        $language = TranslationLanguage::where('code', $languageCode)->first();

        if (!$language) {
            return [
                'status'  => 'error',
                'message' => __('admin.general_settings.language_not_found')
            ];
        }

        session(['app_locale' => $languageCode]);
        if (Auth::guard('admin')->check()) {
            Auth::guard('admin')->user()->update(['language_id' => $language->id]);
        }
        app()->setLocale($languageCode);

        return [
            'status'  => 'success',
            'message' => __('admin.general_settings.language_changed_successfully')
        ];
    }

    public function userFlagChangeLanguage(string $languageCode): array
    {
        $language = TranslationLanguage::where('code', $languageCode)->first();

        if (!$language) {
            return [
                'status'  => 'error',
                'message' => __('admin.general_settings.language_not_found')
            ];
        }

        session(['app_locale_user' => $languageCode]);
        if (Auth::guard('web')->check()) {
            Auth::guard('web')->user()->update(['language_id' => $language->id]);
        }

        return [
            'status'  => 'success',
            'message' => __('admin.general_settings.language_changed_successfully')
        ];
    }

    public function languageDetails(string $code, string $type): array
    {
        $validTabs = ['admin', 'web'];
        if (!in_array($type, $validTabs)) {
            abort(404);
        }

        $language = Language::with('transLang')
            ->whereHas('transLang', fn ($query) => $query->where('code', $code))
            ->firstOrFail();

        $langCode = $language->transLang->code ?? null;
        $flag = asset("backend/assets/img/flags/{$langCode}.svg");

        return [
            'language' => $language,
            'flag'     => $flag,
            'tab'      => $type
        ];
    }

    public function getLanguageModules(string $code, string $tab, ?string $search = null): array
    {
        $validTabs = ['admin', 'web'];
        if (!in_array($tab, $validTabs)) {
            return [
                'status'  => 'error',
                'code'    => 422,
                'message' => 'Invalid tab provided'
            ];
        }

        $language = Language::whereHas('transLang', fn ($query) => $query->where('code', $code))
            ->with('transLang')->first();

        if (!$language) {
            return [
                'status'  => 'error',
                'code'    => 404,
                'message' => __('admin.general_settings.language_not_found')
            ];
        }

        $langCode = $language->transLang->code ?? null;
        $defaultLang = 'en';
        $defaultTranslations = Lang::get($tab, [], $defaultLang);
        $translatedTranslations = Lang::get($tab, [], $langCode);
        $responseArray = [];

        foreach ($defaultTranslations as $module => $keys) {
            if ($search && !str_contains($module, $search)) {
                continue;
            }

            $totalKeys = $this->countKeys($keys);
            $translatedCount = isset($translatedTranslations[$module]) && is_array($translatedTranslations[$module])
                ? $this->countKeys($translatedTranslations[$module], true)
                : 0;

            $progress = $totalKeys > 0 ? round(($translatedCount / $totalKeys) * 100, 2) : 0;

            $responseArray[] = [
                'module_name'     => ucfirst(str_replace('_', ' ', $module)),
                'module_key'      => $module,
                'total_keys'      => $totalKeys,
                'translated_keys' => $translatedCount,
                'progress'        => $progress,
            ];
        }

        return [
            'status'  => 'success',
            'code'    => 200,
            'message' => __('admin.general_settings.module_fetched_success'),
            'data'    => $responseArray
        ];
    }

    public function editModuleLanguage(string $code, string $tab, string $module, ?string $keyword = null): array
    {
        if (!in_array($tab, ['admin', 'web'])) {
            return [
                'status'  => 'error',
                'code'    => 422,
                'message' => __('admin.general_settings.invalid_tab')
            ];
        }

        $language = Language::whereHas('transLang', fn ($query) => $query->where('code', $code))
            ->with('transLang')->first();

        if (!$language) {
            return [
                'status'  => 'error',
                'code'    => 404,
                'message' => __('admin.general_settings.language_not_found')
            ];
        }

        $langCode = $language->transLang->code ?? null;
        $defaultLang = 'en';
        $defaultTranslations = Lang::get($tab, [], $defaultLang);
        $translatedTranslations = Lang::get($tab, [], $langCode);
        $moduleKeys = $defaultTranslations[$module] ?? [];
        $translatedModuleKeys = $translatedTranslations[$module] ?? [];
        $responseArray = [];
        $translatedCount = 0;
        $totalKeys = count($moduleKeys);

        if (!empty($keyword)) {
            $moduleKeys = array_filter($moduleKeys, function ($key, $value) use ($keyword) {
                return stripos($key, $keyword) !== false || stripos($value, $keyword) !== false;
            }, ARRAY_FILTER_USE_BOTH);
            $totalKeys = count($moduleKeys);
        }

        foreach ($moduleKeys as $key => $value) {
            $translatedValue = $translatedModuleKeys[$key] ?? '';
            if (!empty($translatedValue)) {
                $translatedCount++;
            }

            $responseArray[] = [
                'default' => $value,
                'key'     => $key,
                'value'   => $translatedValue,
            ];
        }

        $progress = $totalKeys > 0 ? round(($translatedCount / $totalKeys) * 100, 2) : 0;
        $color = $this->getProgressColor($progress);

        return [
            'status'        => 'success',
            'code'          => 200,
            'message'       => 'Module keys fetched successfully',
            'data'          => $responseArray,
            'language'      => $language,
            'icon'          => url('backend/assets/img/flags/' . $langCode . '.svg'),
            'progress'      => $progress,
            'color'         => $color,
            'uppercaseName' => strtoupper($language->transLang->name ?? '')
        ];
    }

    public function updateModuleLanguage(string $code, string $tab, string $module, string $key, string $value): array
    {
        // Fetch the language
        $language = Language::whereHas('transLang', fn($query) => $query->where('code', $code))
            ->with('transLang')->first();

        if (!$language) {
            return [
                'status'  => 'error',
                'code'    => 404,
                'message' => __('admin.general_settings.language_not_found')
            ];
        }

        $langCode = $language->transLang->code ?? null;
        $translatedPath = base_path("resources/lang/{$langCode}/{$tab}.php");

        $translatedTranslations = [];
        if (file_exists($translatedPath)) {
            $translatedTranslations = Lang::get($tab, [], $langCode);
        }

        if (!isset($translatedTranslations[$module]) || !is_array($translatedTranslations[$module])) {
            $translatedTranslations[$module] = [];
        }

        $translatedTranslations[$module][$key] = $value;
        file_put_contents($translatedPath, "<?php\n\nreturn " . var_export($translatedTranslations, true) . ";\n");

        $progress = $this->calculateModuleProgress($langCode, $tab, $module);
        $color = $this->getProgressColor($progress);

        return [
            'status'        => 'success',
            'code'          => 200,
            'message'       => 'Module key updated successfully',
            'language'      => $language,
            'icon'          => url('backend/assets/img/flags/' . $langCode . '.svg'),
            'uppercaseName' => strtoupper($language->transLang->name ?? ''),
            'progress'      => $progress,
            'color'         => $color
        ];
    }

    public function deleteLanguage(int $id): array
    {
        $language = Language::findOrFail($id);
        $systemLanguage = 'en';
        $langCode = $language->transLang->code ?? null;

        if ($langCode == $systemLanguage) {
            return [
                'status'  => 'error',
                'code'    => 422,
                'message' => __('admin.general_settings.cannot_delete_default_language')
            ];
        }

        if ($language->default == 1) {
            return [
                'status'  => 'error',
                'code'    => 422,
                'message' => __('admin.general_settings.cannot_delete_default_language')
            ];
        }

        if ($language->transLang && $language->transLang->code) {
            $langPath = base_path('resources/lang/' . $language->transLang->code);
            if (is_dir($langPath)) {
                File::deleteDirectory($langPath);
            }
        }

        $language->delete();

        return [
            'status'  => 'success',
            'code'    => 200,
            'message' => __('admin.general_settings.language_deleted')
        ];
    }

    // Helper Methods

    protected function initializeLanguageFiles(string $langCode): void
    {
        $defaultLang = 'en';
        $langDefaultFiles = ['admin', 'web'];

        foreach ($langDefaultFiles as $file) {
            $translations = Lang::get($file, [], $defaultLang);

            if (is_array($translations) && !empty($translations)) {
                // Clear all values recursively
                $clearedTranslations = $this->clearTranslations($translations);

                // Prepare PHP array string for saving
                $exportedTranslations = "<?php\n\nreturn " . var_export($clearedTranslations, true) . ";\n";

                // Save to new language file
                $destinationPath = base_path("resources/lang/{$langCode}/{$file}.php");
                file_put_contents($destinationPath, $exportedTranslations);
            }
        }
    }

    /**
     * Recursively clear translation values
     */
    protected function clearTranslations(array $translations): array
    {
        return array_map(function ($value) {
            if (is_array($value)) {
                return $this->clearTranslations($value);
            }
            return ''; // clear string
        }, $translations);
    }

    protected function countTotalTranslationKeys(array $files): int
    {
        $totalKeys = 0;
        $defaultLang = 'en';

        foreach ($files as $file) {
            $defaultTranslations = Lang::get($file, [], $defaultLang);

            if (is_array($defaultTranslations)) {
                $iterator = new RecursiveIteratorIterator(new RecursiveArrayIterator($defaultTranslations));
                $totalKeys += iterator_count($iterator);
            }
        }

        return $totalKeys;
    }

    protected function countTranslatedKeys(string $langCode, array $files): int
    {
        $translatedCount = 0;

        foreach ($files as $file) {
            $translatedKeys = Lang::get($file, [], $langCode);
            if (is_array($translatedKeys)) {
                $iterator = new RecursiveIteratorIterator(new RecursiveArrayIterator($translatedKeys));
                foreach ($iterator as $value) {
                    if (!empty($value)) {
                        $translatedCount++;
                    }
                }
            }
        }

        return $translatedCount;
    }

    protected function countKeys(array $array, bool $checkEmpty = false): int
    {
        $count = 0;
        foreach ($array as $value) {
            if (is_array($value)) {
                $count += $this->countKeys($value, $checkEmpty);
            } elseif (!$checkEmpty || !empty($value)) {
                $count++;
            }
        }
        return $count;
    }

    protected function getProgressColor(float $progress): string
    {
        $color = 'bg-danger'; // default for <50 (and 25–49)

        if ($progress >= 100) {
            $color = 'bg-success';
        } elseif ($progress >= 75) {
            $color = 'bg-pink';
        } elseif ($progress >= 50) {
            $color = 'bg-warning';
        }

        return $color;
    }

    protected function calculateModuleProgress(string $langCode, string $tab, string $module): float
    {
        $defaultLang = 'en';
        $defaultTranslations = Lang::get($tab, [], $defaultLang);
        $translatedTranslations = Lang::get($tab, [], $langCode);

        $moduleKeys = $defaultTranslations[$module] ?? [];
        $translatedModuleKeys = $translatedTranslations[$module] ?? [];

        $translatedCount = 0;
        $totalKeys = count($moduleKeys);

        foreach ($moduleKeys as $key => $value) {
            if (!empty($translatedModuleKeys[$key] ?? '')) {
                $translatedCount++;
            }
        }

        return $totalKeys > 0 ? round(($translatedCount / $totalKeys) * 100, 2) : 0;
    }
}
