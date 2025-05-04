<?php

namespace Modules\GeneralSetting\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\GeneralSetting\Models\Testimonial;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Modules\GeneralSetting\Models\Language;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Throwable;

class TestimonialController extends Controller
{
    public function testimoials(Request $request):View
    {
        $languages = Language::with('transLang')->get();

        return view('generalsetting::cms.testimoials', compact('languages'));
    }

    public function testimoialStore(Request $request):JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'customer_name'   => 'required|string|max:255',
            'customer_rating' => 'required|integer|min:1|max:5',
            'customer_review' => 'required|string',
            'testimonial_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors'  => $validator->errors()
            ], 422);
        }

        $imagePath = null;
        if ($request->hasFile('testimonial_image')) {
            $imagePath = $request->file('testimonial_image')->store('testimonials', 'public');
        }

        $testimonial = Testimonial::create([
            'customer_name' => $request->customer_name,
            'ratings'       => $request->customer_rating,
            'review'        => $request->customer_review,
            'image'         => $imagePath,
        ]);

        return response()->json([
            'code' => 200,
            'success' => true,
            'message' =>  __('admin.cms.testimonial_create_success'),
            'data'    => $testimonial
        ], 200);
    }

    public function testimoiallist(Request $request): JsonResponse
    {
        try {
            $query = Testimonial::query();

            // Handle search
            if ($request->has('search') && !empty($request->search)) {
                $searchTerm = $request->search;
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('customer_name', 'like', '%' . $searchTerm . '%')
                    ->orWhere('review', 'like', '%' . $searchTerm . '%');
                });
            }

            // Handle sorting
            switch (strtolower($request->input('sort'))) {
                case 'ascending':
                    $query->orderBy('created_at', 'asc');
                    break;
                case 'last month':
                    $query->whereBetween('created_at', [now()->subMonth(), now()]);
                    break;
                case 'last 7 days':
                    $query->whereBetween('created_at', [now()->subDays(7), now()]);
                    break;
                case 'descending':
                case 'latest':
                default:
                    $query->orderBy('created_at', 'desc');
            }

            // Handle ratings filter
            if ($request->has('ratings') && is_array($request->ratings)) {
                $query->whereIn('ratings', $request->ratings);
            }

            $testimonials = $query->get();

            return response()->json([
                'success' => true,
                'message' =>  __('admin.general_settings.testimonial_retrive_success'),
                'data'    => $testimonials
            ], 200);
        } catch (Throwable $e) {
            return response()->json([
                'success' => false,
                'message' =>  __('admin.general_settings.fail_retrive_testimonial'),
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    public function updateTestimonial(Request $request):JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:testimonials,id',
            'customer_name' => 'required|string|min:3',
            'customer_rating' => 'required|integer|min:1|max:5',
            'customer_review' => 'required|string|min:10',
            'status' => 'required|boolean',
            'testimonial_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:5120', // 5MB
        ]);

        if ($validator->fails()) {
            return response()->json([
                'code' => 422,
                'errors' => $validator->errors()
            ], 422);
        }

        /** @var \Modules\GeneralSetting\Models\Testimonial $testimonial */
        $testimonial = Testimonial::findOrFail($request->id);

        if ($request->hasFile('testimonial_image')) {
            if ($testimonial->image) {
                Storage::disk('public')->delete($testimonial->image);
            }

            $imagePath = $request->file('testimonial_image')->store('testimonials', 'public');
            $testimonial->image = $imagePath;
        }

        $testimonial->customer_name = $request->customer_name;
        $testimonial->ratings = $request->customer_rating;
        $testimonial->review = $request->customer_review;
        $testimonial->status = $request->status;
        $testimonial->save();

        // Return success response
        return response()->json([
            'code' => 200,
            'message' => __('admin.cms.testimonial_update_success'),
            'testimonial' => $testimonial
        ]);
    }

    public function deleteTestimonial(Request $request): JsonResponse
    {
        $id = $request->id;

        if (!$id) {
            return response()->json(['code' => 400, 'message' => 'Testimonial ID is required.'], 400);
        }

        try {
            /** @var \Modules\GeneralSetting\Models\Testimonial $testimonial */
            $testimonial = Testimonial::findOrFail($id);
            if ($testimonial->testimonial_image) {
                Storage::delete('public/testimonials/' . $testimonial->testimonial_image);
            }
            $testimonial->delete();

            return response()->json([
                'code' => 200,
                'message' => __('admin.cms.testimonial_delete_success'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => __('admin.common.default_delete_error'),
                'error' => $e->getMessage(),
            ]);
        }
    }
}
