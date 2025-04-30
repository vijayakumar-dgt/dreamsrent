<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Http\JsonResponse;


class TranslationController extends Controller
{
    public function getFileTranslations($file, $modules): JsonResponse
    {
        $locale = App::getLocale();

        $validFiles = ['web', 'admin', 'app'];
        if (!in_array($file, $validFiles)) {
            return response()->json(['error' => 'Invalid translation file'], 400);
        }

        $moduleArray = array_map('trim', explode(',', $modules));
        $translations = [$file => []];

        foreach ($moduleArray as $module) {
            if (\Illuminate\Support\Facades\Lang::has("$file.$module", $locale)) {
                $translations[$file][$module] = trans("$file.$module", [], $locale);
            } else {
                $translations[$file][$module] = [];
            }
        }

        return response()->json($translations);
    }
}
