<?php

namespace Modules\GeneralSetting\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Crypt;
use Modules\GeneralSetting\Models\Language;
use Modules\GeneralSetting\Models\TranslationLanguage;
use RecursiveArrayIterator;
use RecursiveIteratorIterator;

class LanguageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $translationLanguages = TranslationLanguage::where('status', 1)->get();
        $data = [
            'translationLanguages' => $translationLanguages
        ];
        return view('generalsetting::website_settings.languages', $data);
    }

    public function addLanguage(Request $request)
    {
        $languageTranslation = TranslationLanguage::find($request->lang_id);

        if (!$languageTranslation) {
            return response()->json([
                'status'  => 'error',
                'code'    => 422,
                'message' =>  __('admin.general_settings.language_not_found'),
            ], 422);
        }

        // Check if the language is already added
        if (Language::where('language_id', $languageTranslation->id)->exists()) {
            return response()->json([
                'status'  => 'error',
                'code'    => 422,
                'message' =>  __('admin.general_settings.language_already_exist'),
            ]);
        }

        try {
            // Add new language
            $language = new Language();
            $language->language_id = $languageTranslation->id;
            $language->save();

            $langPath = base_path('resources/lang/' . $languageTranslation->code);

            // Create directory if not exists
            if (!file_exists($langPath)) {
                mkdir($langPath, 0777, true);
            }

            // Copy default files from English
            $defaultLang = 'en';
            $langDefaultFiles = ['admin.php', 'app.php', 'web.php'];

            foreach ($langDefaultFiles as $file) {
                $sourcePath = base_path("resources/lang/{$defaultLang}/{$file}");
                $destinationPath = "{$langPath}/{$file}";

                if (file_exists($sourcePath) && !file_exists($destinationPath)) {
                    $translations = include $sourcePath;

                    // Remove values, keep structure
                    $clearedTranslations = array_map(function ($module) {
                        return array_map(function () {
                            return '';
                        }, $module);
                    }, $translations);

                    $exportedTranslations = var_export($clearedTranslations, true);
                    $exportedTranslations = str_replace("array (", "[", $exportedTranslations);
                    $exportedTranslations = str_replace(")", "]", $exportedTranslations);

                    file_put_contents($destinationPath, "<?php\nreturn " . $exportedTranslations . ";\n");
                }
            }
            return response()->json([
                'status'  => 'success',
                'code'    => 200,
                'message' =>  __('admin.general_settings.language_added_successfully'),
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'code'    => 422,
                'message' => __('admin.general_settings.retrive_error'),
                'error'   => $e->getMessage()
            ], 422);
        }
    }

    public function getLanguages(Request $request)
    {
        // $languages = Language::with('transLang')->get();
        $languages = Language::query();
        if($request->has('search') && $request->search != ""){
            $languages->where(function ($query) use ($request) {
                $search = $request->search;
                $query->whereHas('transLang', function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%");
                });
            });
        }

        $languages = $languages->with('transLang')->get();
        $langDefaultFiles = ['admin.php', 'app.php', 'web.php'];
        $responseArray = [];

        // Set English as the base language
        $defaultLang = 'en';
        $totalKeys = 0;

        // Calculate total keys in the default language
        foreach ($langDefaultFiles as $file) {
            $filePath = base_path("resources/lang/{$defaultLang}/{$file}");
            if (file_exists($filePath)) {
                $defaultTranslations = include $filePath;
                if (is_array($defaultTranslations)) {
                    $iterator = new RecursiveIteratorIterator(new RecursiveArrayIterator($defaultTranslations));
                    $totalKeys += iterator_count($iterator);
                }
            }
        }

        // Compare each language against the English base
        foreach ($languages as $language) {
            $langCode = $language->transLang->code;
            $translatedCount = 0;

            foreach ($langDefaultFiles as $file) {
                $filePath = base_path("resources/lang/{$langCode}/{$file}");
                if (file_exists($filePath)) {
                    $translatedKeys = include $filePath;
                    if (is_array($translatedKeys)) {
                        $iterator = new RecursiveIteratorIterator(new RecursiveArrayIterator($translatedKeys));
                        foreach ($iterator as $key => $value) {
                            if (!empty($value)) {
                                $translatedCount++;
                            }
                        }
                    }
                }
            }

            $progress = $totalKeys > 0 ? round(($translatedCount / $totalKeys) * 100, 2) : 0;
            $responseArray[$langCode] = [
                'id'            => $language->id,
                'language_name' => $language->transLang->name,
                'lang_img'      => url('assets/img/flags/' . $langCode . '.png'),
                'lang_code'     => $langCode,
                'lang_rtl'      => $language->rtl,
                'default'       => $language->default,
                'status'        => $language->status,
                'total_keys' => $totalKeys,
                'translated_keys' => $translatedCount,
                'progress' => round($progress)
            ];
        }

        return response()->json([
            'status'  => 'success',
            'code'    => 200,
            'message' => __('admin.general_settings.language_fetched_successfully'),
            'data'    => $responseArray
        ], 200);
    }

    public function updateLanguageSettings(Request $request)
    {
        try {
            $language = Language::find($request->id);
            $languageCode = $language->transLang->code;
            if (!$language) {
                return response()->json([
                    'status'  => 'error',
                    'code'    => 404,
                    'message' => __('admin.general_settings.language_not_found')
                ], 404);
            }

            // Update field dynamically
            $field = $request->field;
            if($field == 'default'){
                Language::where('default', 1)->update(['default' => 0]);
                session()->forget('app_locale');
                session()->forget('app_locale_user');
                session(['app_locale' => $languageCode]);
                session(['app_locale_user' => $languageCode]);
                if(Auth::guard('admin')->check()){
                    $user = Auth::guard('admin')->user();
                    $user->language_id = $language->language_id;
                    $user->save();
                }
            }
            $language->$field = $request->value;
            $language->save();

            return response()->json([
                'status'  => 'success',
                'code'    => 200,
                'message' =>__('admin.general_settings.language_updated_successfully'),
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status'  => 'error',
                'code'    => 422,
                'message' => __('admin.general_settings.retrive_error'),
                'error'   => $th->getMessage()
            ], 422);
        }
    }

    public function changeLanguage(Request $request)
    {
        $language = TranslationLanguage::where('code', $request->language_code)->first();

        if (!$language) {
            return response()->json([
                'status' => 'error',
                'message' =>__('admin.general_settings.language_not_found'),
            ], 404);
        }

        session(['app_locale' => $request->language_code]);
        if (Auth::guard('admin')->check()) {
            $user = Auth::guard('admin')->user();
            $user->language_id = $language->id;
            $user->save();
        }
        app()->setLocale($request->language_code);

        return response()->json([
            'status' => 'success',
            'message' => __('admin.general_settings.language_changed_successfully'),
        ]);
    }

    public function userFlagChangeLanguage(Request $request)
    {
        $language = TranslationLanguage::where('code', $request->language_code)->first();

        if (!$language) {
            return response()->json([
                'status' => 'error',
                'message' => __('admin.general_settings.language_not_found'),
            ], 404);
        }

        session(['app_locale_user' => $request->language_code]);
        if(Auth::guard('web')->check()){
            $user = Auth::guard('web')->user();
            $user->language_id = $language->id;
            $user->save();
        }

        return response()->json([
            'status' => 'success',
            'message' => __('admin.general_settings.language_changed_successfully'),
        ]);
    }

    public function language(Request $request)
    {
        $langDefaultFiles = ['admin', 'app', 'web'];

        if (!in_array($request->type, $langDefaultFiles)) {
            return abort(404);
        }

        $language = Language::with('transLang')
            ->whereHas('transLang', fn($query) => $query->where('code', $request->code))
            ->firstOrFail();

        $flag = asset("assets/img/flags/{$language->transLang->code}.png");
        $tab = $request->type;

        return view('generalsetting::website_settings.language_details', compact('language', 'flag', 'tab'));
    }


    public function getLanguageModules(Request $request)
    {
        $validTabs = ['admin', 'app', 'web'];
        $tab = $request->tab;

        if (!in_array($tab, $validTabs)) {
            return response()->json([
                'status'  => 'error',
                'code'    => 422,
                'message' => 'Invalid tab provided'
            ], 422);
        }

        $language = Language::whereHas('transLang', function ($query) use ($request) {
            $query->where('code', $request->code);
        })->with('transLang')->first();

        if (!$language) {
            return response()->json([
                'status'  => 'error',
                'code'    => 404,
                'message' =>__('admin.general_settings.language_not_found'),
            ], 404);
        }

        $langCode = $language->transLang->code;
        $defaultLang = 'en';
        $filePath = base_path("resources/lang/{$defaultLang}/{$tab}.php");
        $translatedPath = base_path("resources/lang/{$langCode}/{$tab}.php");
        $responseArray = [];

        // Load default translations
        $defaultTranslations = file_exists($filePath) ? include $filePath : [];
        $translatedTranslations = file_exists($translatedPath) ? include $translatedPath : [];

        // Recursive function to count keys
        $countKeys = function ($array, $checkEmpty = false) use (&$countKeys) {
            $count = 0;
            foreach ($array as $value) {
                if (is_array($value)) {
                    $count += $countKeys($value, $checkEmpty);
                } elseif (!$checkEmpty || !empty($value)) {
                    $count++;
                }
            }
            return $count;
        };

        // Loop through each module
        foreach ($defaultTranslations as $module => $keys) {
            // if request has search ? filter module
            if($request->has('search') && $request->search != ""){
                $search = $request->search;
                if(!str_contains($module, $search)){
                    continue;
                }
            }
            $totalKeys = $countKeys($keys);
            $translatedCount = isset($translatedTranslations[$module]) ? $countKeys($translatedTranslations[$module], true) : 0;

            // Calculate progress
            $progress = $totalKeys > 0 ? round(($translatedCount / $totalKeys) * 100) : 0;

            // Add module progress to response array
            $responseArray[] = [
                'module_name' => ucfirst(str_replace('_', ' ', $module)),
                'module_key' => $module,
                'total_keys' => $totalKeys,
                'translated_keys' => $translatedCount,
                'progress' => $progress
            ];
        }
        return response()->json([
            'status'  => 'success',
            'code'    => 200,
            'message' => __('admin.general_settings.module_fetched_success'),
            'data'    => $responseArray
        ], 200);
    }

    public function editModuleLanguage(Request $request)
    {
        $code = $request->code;
        $tab = $request->tab;
        $module = $request->module;
        $keyword = $request->keyword;
        if (!in_array($tab, ['admin', 'app', 'web'])) {
            return response()->json([
                'status'  => 'error',
                'code'    => 422,
                'message' => __('admin.general_settings.invalid_tab'),
            ], 422);
        }

        $language = Language::whereHas('transLang', function ($query) use ($code) {
            $query->where('code', $code);
        })->with('transLang')->first();

        if (!$language) {
            return response()->json([
                'status'  => 'error',
                'code'    => 404,
                'message' => __('admin.general_settings.language_not_found'),
            ], 404);
        }

        $langCode = $language->transLang->code;
        $defaultLang = 'en';

        // Paths for translations
        $filePath = base_path("resources/lang/{$defaultLang}/{$tab}.php");
        $translatedPath = base_path("resources/lang/{$langCode}/{$tab}.php");

        // Load translations
        $defaultTranslations = file_exists($filePath) ? include $filePath : [];
        $translatedTranslations = file_exists($translatedPath) ? include $translatedPath : [];

        $moduleKeys = $defaultTranslations[$module] ?? [];
        $translatedModuleKeys = $translatedTranslations[$module] ?? [];

        // Prepare response array
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
                'key'   => $key,
                'value' => $translatedValue ?? '',
            ];
        }

        // Calculate progress
        $progress = $totalKeys > 0 ? round(($translatedCount / $totalKeys) * 100) : 0;
        //color
        switch(true){
            case $progress >= 100:
                $color = "bg-success";
                break;
            case $progress >= 75:
                $color = "bg-pink";
                break;
            case $progress >= 50:
                $color = "bg-warning";
                break;
            case $progress >= 25:
                $color = "bg-danger";
                break;
            default:
                $color = "bg-danger";
                break;
        }

        // Return response
        return response()->json([
            'status'   => 'success',
            'code'     => 200,
            'message'  => 'Module keys fetched successfully',
            'data'     => $responseArray,
            'language' => $language,
            'icon'     => url('assets/img/flags/' . $language->transLang->code . '.png'),
            'progress' => $progress,
            'color'    => $color,
            'uppercaseName' => strtoupper($language->transLang->name)
        ]);
    }


    public function updateModuleLanguage(Request $request)
    {
        $code = $request->code;
        $tab = $request->tab;
        $module = $request->module;
        $key = $request->key;
        $value = $request->value;

        $language = Language::whereHas('transLang', function ($query) use ($code) {
            $query->where('code', $code);
        })->with('transLang')->first();

        if (!$language) {
            return response()->json([
                'status'  => 'error',
                'code'    => 404,
                'message' => __('admin.general_settings.language_changed_successfully'),
            ], 404);
        }

        $langCode = $language->transLang->code;
        $defaultLang = 'en';

        // Paths for translations
        $filePath = base_path("resources/lang/{$defaultLang}/{$tab}.php");
        $translatedPath = base_path("resources/lang/{$langCode}/{$tab}.php");

        // Load translations
        $defaultTranslations = file_exists($filePath) ? include $filePath : [];
        $translatedTranslations = file_exists($translatedPath) ? include $translatedPath : [];

        if (isset($translatedTranslations[$module][$key])) {
            $translatedTranslations[$module][$key] = $value;
        } else {
            $translatedTranslations[$module][$key] = $value;
        }

        // Save translations
        file_put_contents($translatedPath, '<?php return ' . var_export($translatedTranslations, true) . ';');
        $moduleKeys = $defaultTranslations[$module] ?? [];
        $translatedModuleKeys = $translatedTranslations[$module] ?? [];

        $translatedCount = 0;
        $totalKeys = count($moduleKeys);

        foreach ($moduleKeys as $key => $value) {
            $translatedValue = $translatedModuleKeys[$key] ?? '';
            if (!empty($translatedValue)) {
                $translatedCount++;
            }

            $responseArray[] = [
                'default' => $value,
                'key'   => $key,
                'value' => $translatedValue ?? '',
            ];
        }

        // Calculate progress
        $progress = $totalKeys > 0 ? round(($translatedCount / $totalKeys) * 100) : 0;
         //color
         switch(true){
            case $progress >= 100:
                $color = "bg-success";
                break;
            case $progress >= 75:
                $color = "bg-pink";
                break;
            case $progress >= 50:
                $color = "bg-warning";
                break;
            case $progress >= 25:
                $color = "bg-danger";
                break;
            default:
                $color = "bg-danger";
                break;
        }
        return response()->json([
            'status'   => 'success',
            'code'     => 200,
            'message'  => 'Module key updated successfully',
            'language' => $language,
            'icon'     => url('assets/img/flags/' . $language->transLang->code . '.png'),
            'uppercaseName' => strtoupper($language->transLang->name),
            'progress' => $progress,
            'color'    => $color
        ]);
    }

    public function deleteLanguage(Request $request)
    {
        $language = Language::find($request->id);

        if (!$language) {
            return response()->json([
                'status'  => 'error',
                'code'    => 422,
                'message' =>  __('admin.general_settings.language_not_found'),
            ], 422);
        }
        $systemLanguage = 'en';
        if($language->transLang->code == $systemLanguage){
            return response()->json([
                'status'  => 'error',
                'code'    => 422,
                'message' =>  __('admin.general_settings.language_not_found'),
            ], 422);
        }
        // Check if it's the default language
        if ($language->default == 1) {
            return response()->json([
                'status'  => 'error',
                'code'    => 422,
                'message' =>  __('admin.general_settings.language_not_found'),
            ], 422);
        }

        // Check if transLang relation exists and has code
        if ($language->transLang && $language->transLang->code) {
            $langPath = base_path('resources/lang/' . $language->transLang->code);

            // Delete language folder if it exists
            if (is_dir($langPath)) {
                $files = glob($langPath . '/*');
                foreach ($files as $file) {
                    if (is_file($file)) {
                        unlink($file);
                    }
                }
                rmdir($langPath);
            }
        }

        $language->delete();

        return response()->json([
            'status'  => 'success',
            'code'    => 200,
            'message' =>  __('admin.general_settings.language_deleted'),
        ], 200);
    }

 #user name = u474594475_homeservice
 #P7!qgmRzbty
}
