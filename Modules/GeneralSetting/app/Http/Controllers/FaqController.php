<?php

namespace Modules\GeneralSetting\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Modules\GeneralSetting\Models\Faq;
use Modules\GeneralSetting\Models\GeneralSetting;
use Modules\GeneralSetting\Models\Language;
use Illuminate\View\View;

class FaqController extends Controller
{
    public function faq(Request $request):View
    {
        $languages = Language::with('transLang')->get();

        return view('generalsetting::cms.faq', compact('languages'));
    }

    public function howItWorks(Request $request):View
    {
        $languages = Language::with('transLang')->get();

        return view('generalsetting::cms.how-it-work', compact('languages'));
    }

    public function howItWorksUpdate(Request $request):JsonResponse
    {
        $request->validate([
            'group_id' => 'required|integer',
            'language' => 'required|integer',
            'howitwork_description' => 'required|string|min:10',
        ]);

        try {
            GeneralSetting::updateOrCreate(
                [
                    'key' => 'how_it_works_' . $request->language,
                    'group_id' => $request->group_id,
                ],
                [
                    'value' => $request->howitwork_description,
                    'language_id' => $request->language
                ]
            );

            return response()->json([
                'code' => 200,
                'success' => true,
                'message' => __('admin.cms.how_it_works_update_success'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'success' => false,
                'message' => __('admin.common.default_update_error'),
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function howItWorksList(Request $request):JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'group_id'    => 'required|integer',
            'language_id' => 'nullable|integer'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'code'    => 422,
                'message' => 'Validation failed!',
                'errors'  => $validator->errors()
            ], 422);
        }

        try {
            $languageId = $request->language_id ?? Language::where('default', 1)->value('language_id');

            $settings = GeneralSetting::where('group_id', $request->group_id)
                ->where('key', 'how_it_works_' . $languageId)
                ->first();

            return response()->json([
                'status'  => 'success',
                'code'    => 200,
                'message' => __('admin.common.default_retrieve_success'),
                'data'    => $settings
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'code'    => 500,
                'message' => __('admin.common.default_retrieve_error'),
                'error'   => $e->getMessage()
            ], 500);
        }
    }


    public function copyright(Request $request):View
    {
        $languages = Language::with('transLang')->get();

        return view('generalsetting::cms.copyright', compact('languages'));
    }

    public function copyrightUpdate(Request $request):JsonResponse
    {
        $request->validate([
            'group_id' => 'required|integer',
            'language' => 'required|integer',
            'copy_right_description' => 'required|string|min:10',
        ]);

        try {
            GeneralSetting::updateOrCreate(
                [
                    'key' => 'copy_right_' . $request->language,
                    'group_id' => $request->group_id,
                ],
                [
                    'value' => $request->copy_right_description,
                    'language_id' => $request->language
                ]
            );

            return response()->json([
                'code' => 200,
                'success' => true,
                'message' => __('admin.cms.copyright_update_success'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'success' => false,
                'message' => __('admin.common.default_update_error'),
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function copyrightList(Request $request):JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'group_id'    => 'required|integer',
            'language_id' => 'nullable|integer'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'code'    => 422,
                'message' => 'Validation failed!',
                'errors'  => $validator->errors()
            ], 422);
        }

        try {
            $languageId = $request->language_id ?? Language::where('default', 1)->value('language_id');

            $settings = GeneralSetting::where('group_id', $request->group_id)
                ->where('key', 'copy_right_' . $languageId)
                ->first();

            return response()->json([
                'status'  => 'success',
                'code'    => 200,
                'message' => __('admin.common.default_retrieve_success'),
                'data'    => $settings
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'code'    => 500,
                'message' => __('admin.common.default_retrieve_error'),
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    public function faqStore(Request $request): JsonResponse
    {
        $rules = [
            'question' => 'required|string|max:255|unique:faqs,question',
            'answer' => 'required|string',
            'language' => 'required|integer|exists:translation_languages,id',
            'status' => 'nullable|boolean'
        ];

        $messages = [
            'question.required' => 'The question field is required.',
            'question.unique' => 'This FAQ question already exists.',
            'answer.required' => 'The answer field is required.',
            'language.required' => 'The language field is required.',
            'language.exists' => 'The selected language is invalid.',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return response()->json(['code' => 422, 'errors' => $validator->errors()], 422);
        }

        try {
            $lastOrder = Faq::max('order_by') ?? 0;
            $orderBy = $lastOrder + 1;

            $faq = Faq::create([
                'question' => $request->question,
                'answer' => $request->answer,
                'status' => $request->status ?? 1,
                'order_by' => $orderBy,
                'language_id' => $request->language ?? 1
            ]);

            return response()->json([
                'code' => 200,
                'message' => __('admin.cms.faq_create_success'),
                'data' => $faq
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => __('admin.common.default_create_error'),
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function faqList(Request $request): JsonResponse
    {
        try {
            // Get the default language ID
            $defaultLanguage = Language::where('default', 1)->value('language_id');

            $faqs = Faq::when($request->language_id, function ($query) use ($request) {
                    return $query->where('language_id', $request->language_id);
            }, function ($query) use ($defaultLanguage) {
                return $query->where('language_id', $defaultLanguage);
            })
                ->when($request->has('status'), function ($query) use ($request) {
                    return $query->where('status', $request->status);
                })
                ->when($request->sort_by, function ($query) use ($request) {
                    switch ($request->sort_by) {
                        case 'asc':
                            return $query->orderBy('order_by', 'asc');
                        case 'desc':
                            return $query->orderBy('order_by', 'desc');
                        case 'last_7_days':
                            return $query->where('created_at', '>=', now()->subDays(7));
                        case 'last_month':
                            return $query->where('created_at', '>=', now()->subMonth());
                        default:
                            return $query->orderBy('order_by', 'desc'); // Default sorting
                    }
                })
                ->get();

            return response()->json([
                'code' => 200,
                'message' => 'FAQ list retrieved successfully!',
                'data' => $faqs
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => __('admin.common.default_retrieve_error'),
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function faqUpdate(Request $request): JsonResponse
    {
        $id = $request->id;

        if (!$id) {
            return response()->json(['code' => 400, 'message' => 'FAQ ID is required.'], 400);
        }

        $rules = [
            'question' => 'required|string|max:255|unique:faqs,question,' . $id,
            'answer' => 'required|string',
            'language' => 'required|integer|exists:translation_languages,id',
            'status' => 'nullable|boolean'
        ];

        $messages = [
            'question.required' => __('admin.cms.question_required'),
            'question.unique' => __('admin.cms.question_unique'),
            'answer.required' => __('admin.cms.answer_required'),
            'language.required' => __('admin.cms.language_required'),
            'language.exists' => __('admin.cms.language_exists'),
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return response()->json(['code' => 422, 'errors' => $validator->errors()], 422);
        }

        try {
            $faq = Faq::findOrFail($id);
            $faq->update([
                'question' => $request->question,
                'answer' => $request->answer,
                'status' => $request->status ?? 1,
                'language_id' => $request->language
            ]);

            return response()->json([
                'code' => 200,
                'message' => __('admin.cms.faq_update_success'),
                'data' => $faq
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => __('admin.common.default_update_error'),
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function faqDelete(Request $request): JsonResponse
    {
        $id = $request->id;

        if (!$id) {
            return response()->json(['code' => 400, 'message' => 'FAQ ID is required.'], 400);
        }

        try {
            $faq = Faq::findOrFail($id);
            $faq->delete();

            return response()->json([
                'code' => 200,
                'message' => __('admin.cms.faq_delete_success'),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => __('admin.common.default_delete_error'),
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
