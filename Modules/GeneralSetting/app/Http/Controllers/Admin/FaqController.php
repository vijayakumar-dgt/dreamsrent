<?php

namespace Modules\GeneralSetting\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\GeneralSetting\Http\Requests\CopyrightListRequest;
use Modules\GeneralSetting\Http\Requests\CopyrightUpdateRequest;
use Modules\GeneralSetting\Http\Requests\FaqStoreRequest;
use Modules\GeneralSetting\Http\Requests\FaqUpdateRequest;
use Modules\GeneralSetting\Http\Requests\HowItWorksListRequest;
use Modules\GeneralSetting\Http\Requests\HowItWorksStoreRequest;
use Modules\GeneralSetting\Models\Language;
use Modules\GeneralSetting\Repositories\Contracts\FaqInterface;
use Modules\GeneralSetting\Repositories\Contracts\SettingsManagementInterface;
use Modules\GeneralSetting\Repositories\Contracts\SettingsRetrievalInterface;

class FaqController extends Controller
{
    public function faq(): View
    {
        $languages = Language::with('transLang')->get();

        return view('generalsetting::cms.faq', compact('languages'));
    }

    public function howItWorks(): View
    {
        $languages = Language::with('transLang')->get();

        return view('generalsetting::cms.how-it-work', compact('languages'));
    }

    public function howItWorksUpdate(HowItWorksStoreRequest $request, SettingsManagementInterface $settingsManager): JsonResponse
    {
        try {
            $settingsManager->storeHowItWorks($request->validated());

            return response()->json([
                'code'    => 200,
                'success' => true,
                'message' => __('admin.cms.how_it_works_update_success'),
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'code'    => 500,
                'success' => false,
                'message' => __('admin.common.default_update_error'),
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function howItWorksList(HowItWorksListRequest $request, SettingsRetrievalInterface $settingsRetriever): JsonResponse
    {
        try {
            $data = $settingsRetriever->getHowItWorks($request->validated());

            return response()->json([
                'status'  => 'success',
                'code'    => 200,
                'message' => __('admin.common.default_retrieve_success'),
                'data'    => $data,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status'  => 'error',
                'code'    => 500,
                'message' => __('admin.common.default_retrieve_error'),
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function copyright(): View
    {
        $languages = Language::with('transLang')->get();

        return view('generalsetting::cms.copyright', compact('languages'));
    }

    public function copyrightUpdate(CopyrightUpdateRequest $request, SettingsManagementInterface $settingsManager): JsonResponse
    {
        try {
            $settingsManager->updateCopyright($request->validated());

            return response()->json([
                'code'    => 200,
                'success' => true,
                'message' => __('admin.cms.copyright_update_success'),
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'code'    => 500,
                'success' => false,
                'message' => __('admin.common.default_update_error'),
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function copyrightList(CopyrightListRequest $request, SettingsRetrievalInterface $settingsRetriever): JsonResponse
    {
        try {
            $data = $settingsRetriever->getCopyright($request->validated());

            return response()->json([
                'status'  => 'success',
                'code'    => 200,
                'message' => __('admin.common.default_retrieve_success'),
                'data'    => $data
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status'  => 'error',
                'code'    => 500,
                'message' => __('admin.common.default_retrieve_error'),
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    public function faqStore(FaqStoreRequest $request, FaqInterface $repository): JsonResponse
    {
        try {
            $faq = $repository->store($request->validated());

            return response()->json([
                'code'    => 200,
                'message' => __('admin.cms.faq_create_success'),
                'data'    => $faq
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code'    => 500,
                'message' => __('admin.common.default_create_error'),
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function faqList(FaqInterface $repository): JsonResponse
    {
        try {
            $faqs = $repository->list(request()->all());

            return response()->json([
                'code'    => 200,
                'message' => __('admin.cms.faq_list_success'),
                'data'    => $faqs
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code'    => 500,
                'message' => __('admin.common.default_retrieve_error'),
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function faqUpdate(FaqUpdateRequest $request, FaqInterface $repository): JsonResponse
    {
        try {
            $faq = $repository->update($request->id, $request->validated());

            return response()->json([
                'code'    => 200,
                'message' => __('admin.cms.faq_update_success'),
                'data'    => $faq
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code'    => 500,
                'message' => __('admin.common.default_update_error'),
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function faqDelete(FaqInterface $repository): JsonResponse
    {
        $id = request('id');

        if (!$id) {
            return response()->json(['code' => 400, 'message' => __('admin.cms.faq_id_required')], 400);
        }

        try {
            $repository->delete($id);

            return response()->json([
                'code'    => 200,
                'message' => __('admin.cms.faq_delete_success'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code'    => 500,
                'message' => __('admin.common.default_delete_error'),
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}
