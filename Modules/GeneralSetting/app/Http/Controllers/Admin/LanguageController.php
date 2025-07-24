<?php

namespace Modules\GeneralSetting\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\GeneralSetting\Repositories\Contracts\LanguageSettingInterface;

class LanguageController extends Controller
{
    protected $languageRepository;

    public function __construct(LanguageSettingInterface $languageRepository)
    {
        $this->languageRepository = $languageRepository;
    }

    public function index(): View
    {
        $data = $this->languageRepository->index();
        return view('generalsetting::website_settings.languages', $data);
    }

    public function addLanguage(Request $request): JsonResponse
    {
        $response = $this->languageRepository->addLanguage($request->all());
        return response()->json($response, $response['code']);
    }

    public function getLanguages(Request $request): JsonResponse
    {
        $filters = $request->only('search');
        $response = $this->languageRepository->getLanguages($filters);
        return response()->json($response, $response['code']);
    }

    public function updateLanguageSettings(Request $request): JsonResponse
    {
        $response = $this->languageRepository->updateLanguageSettings(
            $request->id,
            $request->only('field', 'value')
        );
        return response()->json($response, $response['code']);
    }

    public function changeLanguage(Request $request): JsonResponse
    {
        $response = $this->languageRepository->changeLanguage($request->language_code);
        return response()->json($response, $response['code'] ?? 200);
    }

    public function userFlagChangeLanguage(Request $request): JsonResponse
    {
        $response = $this->languageRepository->userFlagChangeLanguage($request->language_code);
        return response()->json($response, $response['code'] ?? 200);
    }

    public function language(Request $request): View
    {
        $data = $this->languageRepository->languageDetails($request->code, $request->type);
        return view('generalsetting::website_settings.language_details', $data);
    }

    public function getLanguageModules(Request $request): JsonResponse
    {
        $response = $this->languageRepository->getLanguageModules(
            $request->code,
            $request->tab,
            $request->search
        );
        return response()->json($response, $response['code']);
    }

    public function editModuleLanguage(Request $request): JsonResponse
    {
        $response = $this->languageRepository->editModuleLanguage(
            $request->code,
            $request->tab,
            $request->module,
            $request->keyword
        );
        return response()->json($response, $response['code']);
    }

    public function updateModuleLanguage(Request $request): JsonResponse
    {
        $response = $this->languageRepository->updateModuleLanguage(
            $request->code,
            $request->tab,
            $request->module,
            $request->key,
            $request->value
        );
        return response()->json($response, $response['code']);
    }

    public function deleteLanguage(Request $request): JsonResponse
    {
        $response = $this->languageRepository->deleteLanguage($request->id);
        return response()->json($response, $response['code']);
    }
}
