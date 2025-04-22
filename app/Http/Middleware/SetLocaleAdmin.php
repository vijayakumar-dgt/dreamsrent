<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Modules\GeneralSetting\Models\Language;
use Modules\GeneralSetting\Models\TranslationLanguage;
use Symfony\Component\HttpFoundation\Response;

class SetLocaleAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $languageId = '';
        if ($user = Auth::guard('admin')->user()) {
            $languageId = $user->language_id;
        }

        if ($languageId) {
            $language = TranslationLanguage::where('id', $languageId)->first();
            $languageCode = $language->code ?? 'en';
            app()->setLocale($languageCode);
        } elseif (Session::has('app_locale')) {
            app()->setLocale(Session::get('app_locale'));
        } else {
            $language = Language::select('translation_languages.code')
                ->join('translation_languages', 'languages.language_id', '=', 'translation_languages.id')
                ->where('languages.default', 1)
                ->first();

            $languageCode = $language->code ?? 'en';

            Session::put('app_locale', $languageCode);
            app()->setLocale($languageCode);
        }

        return $next($request);
    }
}
