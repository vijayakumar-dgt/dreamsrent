<?php

namespace Modules\GeneralSetting\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Modules\GeneralSetting\Http\Requests\TestimonialAddRequest;
use Modules\GeneralSetting\Http\Requests\TestimonialEditRequest;
use Modules\GeneralSetting\Repositories\Contracts\TestimonialInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;
use Modules\GeneralSetting\Models\Language;
use Illuminate\Http\UploadedFile;

class TestimonialController extends Controller
{
    protected $testimonialRepository;

    public function __construct(TestimonialInterface $testimonialRepository)
    {
        $this->testimonialRepository = $testimonialRepository;
    }

    public function testimoials(Request $request): View
    {
        $languages = Language::with('transLang')->get();
        return view('generalsetting::cms.testimoials', compact('languages'));
    }

    public function testimoialStore(TestimonialAddRequest $request): JsonResponse
    {
        $imagePath = null;
        if ($request->hasFile('testimonial_image')) {
            $file = $request->file('testimonial_image');
            if ($file instanceof UploadedFile) {
                $imagePath = uploadFile($file, 'testimonials');
            }
        }

        $testimonial = $this->testimonialRepository->create([
            'customer_name' => $request->customer_name,
            'ratings' => $request->customer_rating,
            'review' => $request->customer_review,
            'image' => $imagePath,
            'status' => true // default status
        ]);

        return response()->json([
            'code' => 200,
            'success' => true,
            'message' => __('admin.cms.testimonial_create_success'),
            'data' => $testimonial
        ]);
    }

    public function testimoiallist(Request $request): JsonResponse
    {
        try {
            $filters = [
                'search' => $request->search,
                'sort' => $request->sort,
                'ratings' => $request->ratings
            ];

            $testimonials = $this->testimonialRepository->all($filters)
                ->map(function ($testimonial) {
                    $testimonial->created_date = formatDateTime($testimonial->created_at, false);
                    unset($testimonial->created_at);
                    return $testimonial;
                });

            return response()->json([
                'success' => true,
                'message' => __('admin.general_settings.testimonial_retrive_success'),
                'data' => $testimonials
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('admin.general_settings.fail_retrive_testimonial'),
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function updateTestimonial(TestimonialEditRequest $request): JsonResponse
    {
        try {
            $testimonial = $this->testimonialRepository->find($request->id);
            if (!$testimonial) {
                return response()->json([
                    'code' => 404,
                    'message' => __('admin.common.not_found')
                ], 404);
            }

            $data = [
                'customer_name' => $request->customer_name,
                'ratings' => $request->customer_rating,
                'review' => $request->customer_review,
                'status' => $request->status
            ];

            if ($request->hasFile('testimonial_image')) {
                $oldImage = $testimonial->image ?? '';
                $file = $request->file('testimonial_image');
                if ($file instanceof UploadedFile) {
                    $data['image'] = uploadFile($file, 'testimonials', $oldImage);
                }
            }

            $this->testimonialRepository->update($request->id, $data);

            return response()->json([
                'code' => 200,
                'message' => __('admin.cms.testimonial_update_success'),
                'testimonial' => $this->testimonialRepository->find($request->id)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => __('admin.common.default_error'),
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function deleteTestimonial(Request $request): JsonResponse
    {
        try {
            $this->testimonialRepository->delete($request->id);

            return response()->json([
                'code' => 200,
                'message' => __('admin.cms.testimonial_delete_success'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => __('admin.common.default_delete_error'),
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
