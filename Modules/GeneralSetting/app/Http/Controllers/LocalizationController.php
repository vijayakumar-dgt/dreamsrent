<?php

namespace Modules\GeneralSetting\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\GeneralSetting\Http\Requests\UpdateLocalizationRequest;
use Modules\GeneralSetting\Models\Currency;
use Modules\GeneralSetting\Models\DateFormat;
use Modules\GeneralSetting\Models\TimeFormat;
use Modules\GeneralSetting\Models\Language;
use Modules\GeneralSetting\Models\TranslationLanguage;
use Modules\GeneralSetting\Repositories\Contracts\LocalizationInterface;

class LocalizationController extends Controller
{
    protected LocalizationInterface $repository;

    public function __construct(LocalizationInterface $repository)
    {
        $this->repository = $repository;
    }

    public function index(): View
    {
        $timeformats = TimeFormat::get();
        $dateformats = DateFormat::get();
        $currencies = Currency::where('status', 1)->get();
        $weekdays = ["sunday", "monday", "tuesday", "wednesday", "thursday", "friday", "saturday"];
        $availableLanguages = Language::where('status', 1)->pluck('language_id');
        $languages = TranslationLanguage::whereIn('id', $availableLanguages)->where('status', 1)->get();
        
        return view('generalsetting::website_settings.localization', [
            'page_title' => 'Localization',
            'timezones' => $this->repository->getTimezones(),
            'timeformats' => $timeformats,
            'dateformats' => $dateformats,
            'weekdays' => $weekdays,
            'currencies' => $currencies,
            'languages' => $languages
        ]);
    }

    public function getTimezones(Request $request): JsonResponse
    {
        $timezones = $this->repository->searchTimezones($request->search);
        
        return response()->json([
            'status' => 'success',
            'code' => 200,
            'data' => $timezones,
            'message' => __('admin.general_settings.timezone_success'),
        ]);
    }

    public function updateLocalization(UpdateLocalizationRequest $request): JsonResponse
    {
        try {
            $localizationArray = [
                'timezone' => $request->timezone,
                'week_start_day' => $request->week_start_day,
                'date_format' => $request->date_format,
                'time_format' => $request->time_format,
                'default_language' => $request->default_language ?? null,
                'currency' => $request->currency,
                'currency_symbol' => $request->currency_symbol,
                'currency_position' => $request->currency_position,
                'decimal_seperator' => $request->decimal_seperator,
                'thousand_seperator' => $request->thousand_seperator,
                'currency_switcher' => $request->has('currency_switcher') ? 1 : 0,
                'language_switcher' => $request->has('language_switcher') ? 1 : 0
            ];

            $this->repository->updateLocalization($localizationArray);
            
            $timezone = $this->repository->getTimezoneById($request->timezone);
            $timezoneName = $timezone ? $timezone->name : 'UTC';
            config(['app.timezone' => $timezoneName]);

            return response()->json([
                'status' => 'success',
                'code' => 200,
                'message' => __('admin.general_settings.localization_update_success')
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'code' => 500,
                'message' => $th->getMessage()
            ]);
        }
    }

    public function getTimezone(): JsonResponse
    {
        $settingTimezone = $this->repository->getCurrentTimezone();
        
        if ($settingTimezone) {
            $timezones = $this->repository->getTimezoneById($settingTimezone->value);
            return response()->json([
                'status' => 'success',
                'code' => 200,
                'data' => $timezones,
                'message' => __('admin.general_settings.timezone_success'),
            ]);
        }
        
        return response()->json([
            'status' => 'error',
            'code' => 500,
            'message' => __('admin.general_settings.timezone_not_found'),
        ]);
    }
}