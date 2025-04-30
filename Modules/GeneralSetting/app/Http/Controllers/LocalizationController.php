<?php

namespace Modules\GeneralSetting\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\GeneralSetting\Models\Currency;
use Modules\GeneralSetting\Models\DateFormat;
use Modules\GeneralSetting\Models\GeneralSetting;
use Modules\GeneralSetting\Models\TimeFormat;
use Modules\GeneralSetting\Models\Timezone;
use Modules\GeneralSetting\Models\TranslationLanguage;
use Modules\GeneralSetting\Models\Language;

class LocalizationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $timezones = Timezone::get();
        $timeformats = TimeFormat::get();
        $dateformats = DateFormat::get();
        $currencies  = Currency::where('status', 1)->get();
        $weekdays    = ["sunday","monday","tuesday","wednesday","thursday","friday","saturday"];
        $availableLanguages = Language::where('status', 1)->pluck('language_id');
        $languages   = TranslationLanguage::whereIn('id', $availableLanguages)->where('status', 1)->get();
        $data = [
            'page_title'  => 'Localization',
            'timezones'   => $timezones,
            'timeformats' => $timeformats,
            'dateformats' => $dateformats,
            'weekdays'    => $weekdays,
            'currencies'  => $currencies,
            'languages'   => $languages
        ];
        return view('generalsetting::website_settings.localization', $data);
    }

    public function getTimezones(Request $request)
    {
        //search
        $search = $request->search;
        $timezones = Timezone::where('name', 'like', "%$search%")->take(10)->get()->map(function ($timezone) {
            return [
                'id' => $timezone->id,
                'text' => $timezone->name
            ];
        });
        return response()->json([
            'status' => 'success',
            'code'   => 200,
            'data'   => $timezones,
            'message' =>  __('admin.general_settings.timezone_success'),
        ]);
    }

    public function setEnvValue($key, $value)
    {
        $path = base_path('.env');

        if (file_exists($path)) {
            $escaped = preg_quote('=' . $value, '/');

            if (strpos(file_get_contents($path), "{$key}=") !== false) {
                file_put_contents($path, preg_replace(
                    "/^{$key}=.*/m",
                    "{$key}=\"{$value}\"",
                    file_get_contents($path)
                ));
            } else {
                file_put_contents($path, PHP_EOL . "{$key}=\"{$value}\"", FILE_APPEND);
            }
        }
    }

    public function updateLocalization(Request $request)
    {
        DB::beginTransaction();
        try {
            $groupId = 5;
            $localizationArray = [
                'timezone' => $request->timezone,
                'week_start_day' => $request->week_start_day,
                'date_format'    => $request->date_format,
                'time_format'    => $request->time_format,
                'default_language' => $request->default_language ?? null,
                'currency'       => $request->currency,
                'currency_symbol' => $request->currency_symbol,
                'currency_position' => $request->currency_position,
                'decimal_seperator' => $request->decimal_seperator,
                'thousand_seperator' => $request->thousand_seperator,
                'currency_switcher' => $request->currency_switcher == 'on' ? 1 : 0,
                'language_switcher' => $request->language_switcher == 'on' ? 1 : 0
            ];

            foreach ($localizationArray as $k => $v) {
                GeneralSetting::updateOrCreate(
                    ['group_id' => $groupId, 'key' => $k],
                    ['value' => $v]
                );
            }

            $timezone = Timezone::where('id', $request->timezone)->first();
            if (!empty($timezone)) {
                $timezoneName = $timezone->name;
            } else {
                $timezoneName = 'UTC';
            }

            config(['app.timezone' => $timezoneName]);
            // $this->setEnvValue('APP_TIMEZONE', $timezoneName);
            $authUser = Auth::guard('admin')->user();
            $refresh = false;
            if (!empty($authUser) && $authUser->language_id != $request->default_language) {
                $authUser->language_id = $request->default_language;
                $authUser->save();
                $refresh = true;
            }
            DB::commit();

            return response()->json([
                'status' => 'success',
                'code'   => 200,
                'refresh' => $refresh,
                'message' => __('admin.general_settings.localization_update_success')
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'code'   => 500,
                'message' => $th->getMessage()
            ]);
        }
    }



    public function getTimezone(Request $request)
    {
        $settingTimezone = GeneralSetting::where('group_id', 5)->where('key', 'timezone')->first();
        if (!empty($settingTimezone)) {
            $timezones = Timezone::where('id', $settingTimezone->value)->first();
            return response()->json([
                'status' => 'success',
                'code'   => 200,
                'data'   => $timezones,
                'message' => __('admin.general_settings.timezone_success'),
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'code'   => 500,
                'message' => __('admin.general_settings.timezone_not_found'),
            ]);
        }
    }
}
