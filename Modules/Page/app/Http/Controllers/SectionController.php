<?php

namespace Modules\Page\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Modules\CarInfo\Models\VehicleInfo;
use Modules\Page\Models\Section;
use Modules\Page\Http\Requests\AddSectionRequest;
use Modules\Page\Http\Requests\UpdateSectionRequest;
use Modules\Page\Repositories\Contracts\SectionInterface;
use Modules\Page\Models\Page;

class SectionController extends Controller
{
    protected $sectionRepository;

    public function __construct(SectionInterface $sectionRepository)
    {
        $this->sectionRepository = $sectionRepository;
    }

    public function index(Request $request): JsonResponse
    {
        $orderBy = $request->input('order_by', 'asc');
        $sortBy = $request->input('sort_by', 'id');
        $themeId = $request->theme_id;

        $sections = $this->sectionRepository->getAllSections($orderBy, $sortBy, $themeId);

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

        $authuser = Auth::user();

        if (!$authuser) {
            return response()->json([
                'code' => 401,
                'message' => __('Unauthorized. User not found.'),
            ], 401);
        }

        $languageId = $authuser->language_id ?? null;

        if (!$languageId) {
            return response()->json([
                'code' => 400,
                'message' => __('Language ID not found for the user.'),
            ], 400);
        }

        $allowedNames = ['Banner One', 'Why Choose Us', 'Banner Two', 'Best Vehicle', 'Banner Three'];
        
        $sections = $this->sectionRepository->getFilteredSections($orderBy, $sortBy, $allowedNames);

        $data = [];
        $baseUrl = asset('storage');

        foreach ($sections as $section) {
            $sectionData = $this->sectionRepository->getSectionData($section->id, $languageId);
            $decodedDatas = $sectionData ? json_decode($sectionData, true) : [];

            if (!empty($decodedDatas['thumbnail_image_one'])) {
                $decodedDatas['thumbnail_image_one'] = $baseUrl . '/' . $decodedDatas['thumbnail_image_one'];
            }

            if (!empty($decodedDatas['thumbnail_image_two'])) {
                $decodedDatas['thumbnail_image_two'] = $baseUrl . '/' . $decodedDatas['thumbnail_image_two'];
            }

            if (!empty($decodedDatas['thumbnail_image_four'])) {
                $decodedDatas['thumbnail_image_four'] = $baseUrl . '/' . $decodedDatas['thumbnail_image_four'];
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

    public function store(AddSectionRequest $request): JsonResponse
    {
        $authuser = Auth::user();
        if (!$authuser) {
            return response()->json([
                'code' => 401,
                'message' => __('Unauthorized. User not found.'),
            ], 401);
        }

        $languageId = $authuser->language_id;
        $sectionId = $request->section_id;

        $existingData = $this->sectionRepository->getSectionData($sectionId, $languageId);
        $existingData = $existingData ? json_decode($existingData, true) : [];

        $data = $this->processSectionData($request, $existingData);

        $this->updateSectionTitle($request, $sectionId);

        try {
            $this->sectionRepository->updateOrCreateSectionData($sectionId, $languageId, $data);
            return response()->json(['code' => 200, 'message' => __('admin.cms.section_update_success')], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => __('admin.common.default_update_error'), 'error' => $e->getMessage()], 500);
        }
    }

    public function update(UpdateSectionRequest $request): JsonResponse
    {
        $authuser = Auth::user();
        if (!$authuser) {
            return response()->json([
                'code' => 401,
                'message' => __('Unauthorized. User not found.'),
            ], 401);
        }

        $languageId = $authuser->language_id;
        $sectionId = $request->section_id;

        $existingData = $this->sectionRepository->getSectionData($sectionId, $languageId);
        $existingData = $existingData ? json_decode($existingData, true) : [];

        $data = $this->processSectionData($request, $existingData);

        $this->updateSectionTitle($request, $sectionId);

        try {
            $this->sectionRepository->updateOrCreateSectionData($sectionId, $languageId, $data);
            return response()->json(['code' => 200, 'message' => __('admin.cms.section_update_success')], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => __('admin.common.default_update_error'), 'error' => $e->getMessage()], 500);
        }
    }

    public function delete(Request $request): JsonResponse
    {
        try {
            $this->sectionRepository->deletePage($request->id);
            return response()->json([
                'status' => 'success',
                'code' => 200,
                'message' => 'Page deleted successfully.'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'code' => 500,
                'message' => 'An error occurred while deleting page!'
            ], 500);
        }
    }

    protected function processSectionData($request, $existingData)
    {
        $data = [];
        $sectionId = $request->section_id;

        if ($sectionId == 1) {
            $thumbnailPath = $existingData['thumbnail_image_one'] ?? null;
            if ($request->hasFile('thumbnail_image_one')) {
                $thumbnailPath = uploadFile($request->file('thumbnail_image_one'), 'general');
            }

            $data = [
                'label_one' => $request->label_one,
                'line_one' => $request->line_one,
                'line_two' => $request->line_two,
                'description_one' => $request->description_one,
                'thumbnail_image_one' => $thumbnailPath,
            ];
        } elseif ($sectionId == 29) {
            $thumbnailPath = $existingData['thumbnail_image_two'] ?? null;
            if ($request->hasFile('thumbnail_image_two')) {
                $thumbnailPath = uploadFile($request->file('thumbnail_image_two'), 'general');
            }

            $data = [
                'label_two' => $request->label_two,
                'description_two' => $request->description_two,
                'thumbnail_image_two' => $thumbnailPath,
            ];
        } elseif ($sectionId == 43) {
            $thumbnailPath = $existingData['thumbnail_image_four'] ?? null;
            if ($request->hasFile('thumbnail_image_four')) {
                $thumbnailPath = uploadFile($request->file('thumbnail_image_four'), 'general');
            }

            $data = [
                'label_three_one' => $request->label_three_one,
                'label_three_two' => $request->label_three_two,
                'label_three_three' => $request->label_three_three,
                'description_three' => $request->description_three,
                'thumbnail_image_four' => $thumbnailPath,
            ];
        } elseif ($sectionId == 42) {
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
        } elseif ($sectionId == 26) {
            $data = [
                'why_label_1' => $request->why_label_1,
                'why_dis_1' => $request->why_dis_1,
                'why_icon_1' => $this->processIcon($request, 'why_icon_1', $existingData['why_icon_1'] ?? null),
                'why_label_2' => $request->why_label_2,
                'why_dis_2' => $request->why_dis_2,
                'why_icon_2' => $this->processIcon($request, 'why_icon_2', $existingData['why_icon_2'] ?? null),
                'why_label_3' => $request->why_label_3,
                'why_dis_3' => $request->why_dis_3,
                'why_icon_3' => $this->processIcon($request, 'why_icon_3', $existingData['why_icon_3'] ?? null),
            ];
        }

        return $data;
    }

    protected function processIcon($request, $fieldName, $existingValue)
    {
        if ($request->hasFile($fieldName)) {
            return uploadFile($request->file($fieldName), 'general');
        }
        return $existingValue;
    }

    protected function updateSectionTitle($request, $sectionId)
    {
        $titleFieldMap = [
            1 => 'section_title_one',
            29 => 'section_title_two',
            42 => 'section_title_three',
            43 => 'section_title_five',
            26 => 'section_title_four',
        ];

        if (isset($titleFieldMap[$sectionId]) && $request->has($titleFieldMap[$sectionId])) {
            $this->sectionRepository->updateSectionTitle($sectionId, $request->{$titleFieldMap[$sectionId]});
        }
    }
}