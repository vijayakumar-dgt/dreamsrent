<?php

namespace Modules\Page\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Modules\CarInfo\Models\VehicleInfo;
use Modules\Page\Models\Section;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Modules\Page\Models\Page;

class SectionController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $orderBy = $request->input('order_by', 'asc');
        $sortBy = $request->input('sort_by', 'id');

        $sections = Section::orderBy($sortBy, $orderBy)
            ->where("theme_id", $request->theme_id)
            ->where("status", 1)
            ->get();

        $data = [];
        $baseUrl = asset('storage/uploads');
        $theme = ['theme_id' => null];

        foreach ($sections as $section) {
            $decodedDatas = json_decode($section->datas ?? '{}', true);

            if (isset($decodedDatas['background_image'])) {
                $decodedDatas['background_image'] = $baseUrl . '/background_image_banner/' . $decodedDatas['background_image'];
            }

            if (isset($decodedDatas['thumbnail_image'])) {
                $decodedDatas['thumbnail_image'] = $baseUrl . '/thumbnail_image_banner/' . $decodedDatas['thumbnail_image'];
            }

            $data[] = array_merge([
                'id' => $section->id,
                'name' => $section->title,
                'status' => $section->status,
            ], $decodedDatas);

            $theme = [
                'theme_id' => $section->theme_id,
            ];
        }

        return response()->json([
            'code' => 200,
            'message' => __('Section details retrieved successfully.'),
            'data' => $data,
            'theme' => $theme
        ], 200);
    }

    public function indexListSection(Request $request): JsonResponse
    {
        $orderBy = $request->input('order_by', 'asc');
        $sortBy = $request->input('sort_by', 'id');

        $authuser = auth()->user();

        if (!$authuser) {
            return response()->json([
                'code' => 401,
                'message' => __('Unauthorized. User not found.'),
            ], 401);
        }

        $language_id = $authuser->language_id ?? null;

        if (!$language_id) {
            return response()->json([
                'code' => 400,
                'message' => __('Language ID not found for the user.'),
            ], 400);
        }

        $allowedNames = ['Banner One', 'Why Choose Us', 'Banner Two', 'Best Vehicle'];

        $sections = Section::orderBy($sortBy, $orderBy)
            ->where('status', 1)
            ->whereIn('name', $allowedNames)
            ->get();

        $data = [];
        $baseUrl = asset('storage');

        foreach ($sections as $section) {
            $sectionData = DB::table('section_datas')
                ->where('section_id', $section->id)
                ->where('language_id', $language_id)
                ->value('datas');

            $decodedDatas = $sectionData ? json_decode($sectionData, true) : [];

            if (!empty($decodedDatas['thumbnail_image_one'])) {
                $decodedDatas['thumbnail_image_one'] = $baseUrl . '/' . $decodedDatas['thumbnail_image_one'];
            }

            if (!empty($decodedDatas['thumbnail_image_two'])) {
                $decodedDatas['thumbnail_image_two'] = $baseUrl . '/' . $decodedDatas['thumbnail_image_two'];
            }

            $data[] = array_merge([
                'id' => $section->id,
                'theme_id' => $section->theme_id,
                'title' => $section->title,
                'name' => $section->name,
                'status' => $section->status,
            ], $decodedDatas);
        }

        return response()->json([
            'code' => 200,
            'message' => __('Section details retrieved successfully.'),
            'data' => $data
        ], 200);
    }

    public function indexSection(): View
    {
        $vehicles = VehicleInfo::select("id", "name")->get();

        return view('page::section.index', compact("vehicles"));
    }


    public function store(Request $request): JsonResponse
    {

        $authuser = auth()->user();
        if (!$authuser) {
            return response()->json([
                'code' => 401,
                'message' => __('Unauthorized. User not found.'),
            ], 401);
        }

        $language_id = $authuser->language_id;
        $rules = [];

        if ($request->section_id == 1) {
            $rules['section_title_one'] = 'required';
            $rules['description_one'] = 'required';
            $rules['label_one'] = 'required';
            $rules['line_one'] = 'required';
            $rules['line_two'] = 'required';
        } elseif ($request->section_id == 29) {
            $rules['description_two'] = 'required';
            $rules['label_two'] = 'required';
        } elseif ($request->section_id == 42) {
            $rules['vehicle_id'] = 'required';
            $rules['label_1'] = 'required|max:50';
            $rules['dis_1'] = 'required|max:100';
            $rules['label_2'] = 'required|max:50';
            $rules['dis_2'] = 'required|max:100';
            $rules['label_3'] = 'required|max:50';
            $rules['dis_3'] = 'required|max:100';
            $rules['label_4'] = 'required|max:50';
            $rules['dis_4'] = 'required|max:100';
            $rules['label_5'] = 'required|max:50';
            $rules['dis_5'] = 'required|max:100';
            $rules['label_6'] = 'required|max:50';
            $rules['dis_6'] = 'required|max:100';
        } elseif ($request->section_id == 26) {
            $rules['why_label_1'] = 'required|max:50';
            $rules['why_dis_1']   = 'required|max:200';
            $rules['why_label_2'] = 'required|max:50';
            $rules['why_dis_2']   = 'required|max:200';
            $rules['why_label_3'] = 'required|max:50';
            $rules['why_dis_3']   = 'required|max:200';
        } else {
            return response()->json(['message' => 'Invalid section ID'], 400);
        }

        $messages = [
            'how_it_work.required' => __('The how it work field is required.'),
        ];
        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $data = [];

        $id = $request->section_id;

        $existingData = DB::table('section_datas')
            ->where('section_id', $id)
            ->where('language_id', $language_id)
            ->value('datas');

        $existingData = $existingData ? json_decode($existingData, true) : [];

        // Process section data based on section_id
        if ($request->section_id == 1) {
            $thumbnailPath = $existingData['thumbnail_image_one'] ?? null;

            if ($request->hasFile('thumbnail_image_one') && $request->file('thumbnail_image_one') instanceof \Illuminate\Http\UploadedFile) {
                $thumbnailPath = uploadFile($request->file('thumbnail_image_one'), 'general');
            }

            $data = [
                'label_one' => $request->label_one,
                'line_one' => $request->line_one,
                'line_two' => $request->line_two,
                'description_one' => $request->description_one,
                'thumbnail_image_one' => $thumbnailPath,
            ];
        } elseif ($request->section_id == 29) {
            $thumbnailPath = $existingData['thumbnail_image_two'] ?? null;

            if ($request->hasFile('thumbnail_image_two') && $request->file('thumbnail_image_two') instanceof \Illuminate\Http\UploadedFile) {
                $thumbnailPath = uploadFile($request->file('thumbnail_image_two'), 'general');
            }

            $data = [
                'label_two' => $request->label_two,
                'description_two' => $request->description_two,
                'thumbnail_image_two' => $thumbnailPath,
            ];
        } elseif ($request->section_id == 42) {
            $data = [
                'vehicle_id' => $request->vehicle_id,
                'label_1' => $request->label_1,
                'dis_1' => $request->dis_1,
                'label_2' => $request->label_2,
                'dis_2' => $request->dis_2,
                'label_3' => $request->label_3,
                'dis_3' => $request->dis_3,
                'label_4' => $request->label_4,
                'dis_4' => $request->dis_4,
                'label_5' => $request->label_5,
                'dis_5' => $request->dis_5,
                'label_6' => $request->label_6,
                'dis_6' => $request->dis_6,
            ];
        } elseif ($request->section_id == 26) {
            $thumbnail1 = $thumbnail2 = $thumbnail3 = null;

            if ($request->hasFile('why_icon_1') && $request->file('why_icon_1')->isValid()) {
                $thumbnail1 = uploadFile($request->file('why_icon_1'), 'general');
            }

            if ($request->hasFile('why_icon_2') && $request->file('why_icon_2')->isValid()) {
                $thumbnail2 = uploadFile($request->file('why_icon_2'), 'general');
            }

            if ($request->hasFile('why_icon_3') && $request->file('why_icon_3')->isValid()) {
                $thumbnail3 = uploadFile($request->file('why_icon_3'), 'general');
            }

            $data = [
                'why_label_1' => $request->why_label_1,
                'why_dis_1'   => $request->why_dis_1,
                'why_icon_1'  => $thumbnail1 ?? ($existingData['why_icon_1'] ?? null),

                'why_label_2' => $request->why_label_2,
                'why_dis_2'   => $request->why_dis_2,
                'why_icon_2'  => $thumbnail2 ?? ($existingData['why_icon_2'] ?? null),

                'why_label_3' => $request->why_label_3,
                'why_dis_3'   => $request->why_dis_3,
                'why_icon_3'  => $thumbnail3 ?? ($existingData['why_icon_3'] ?? null),
            ];
        }

        if ($request->section_id == 1 && $request->has('section_title_one')) {
            $section = Section::find($request->section_id);

            if ($section) {
                $section->title = $request->section_title_one;
                $section->save();
            } else {
                return response()->json(['error' => 'Section not found.'], 404);
            }
        }

        if ($request->section_id == 29 && $request->has('section_title_two')) {
            $section = Section::find($request->section_id);

            if ($section) {
                $section->title = $request->section_title_two;
                $section->save();
            } else {
                return response()->json(['error' => 'Section not found.'], 404);
            }
        }

        if ($request->section_id == 42 && $request->has('section_title_three')) {
            $section = Section::find($request->section_id);

            if ($section) {
                $section->title = $request->section_title_three;
                $section->save();
            } else {
                return response()->json(['error' => 'Section not found.'], 404);
            }
        }

        if ($request->section_id == 26 && $request->has('section_title_four')) {
            $section = Section::find($request->section_id);

            if ($section) {
                $section->title = $request->section_title_four;
                $section->save();
            } else {
                return response()->json(['error' => 'Section not found.'], 404);
            }
        }

        try {
            // Try update first
            $updated = DB::table('section_datas')
                ->where('section_id', $id)
                ->where('language_id', $language_id)
                ->update(['datas' => json_encode($data)]);

            // If not updated, insert
            if ($updated === 0) {
                DB::table('section_datas')->insert([
                    'section_id' => $id,
                    'language_id' => $language_id,
                    'datas' => json_encode($data),
                ]);
            }

            return response()->json(['code' => 200, 'message' => __('admin.cms.section_update_success')], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => __('admin.common.default_update_error'), 'error' => $e->getMessage()], 500);
        }
    }

    public function delete(Request $request): JsonResponse
    {
        try {
            $id = $request->id;

            Page::where('id', $id)->delete();

            return response()->json([
                'status' => 'success',
                'code'   => 200,
                'message' => 'Page deleted successfully.'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'code'   => 500,
                'message' => 'An error occured while deleting page!'
            ], 500);
        }
    }
}
