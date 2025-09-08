<?php

namespace Modules\Page\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\User;
use App\Models\UserDetail;
use App\Models\Wishlist;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Modules\CarInfo\Models\Brand;
use Modules\CarInfo\Models\CarModel;
use Modules\CarInfo\Models\Cartype;
use Modules\CarInfo\Models\Location;
use Modules\CarInfo\Models\VehicleInfo;
use Modules\CarInfo\Models\VehicleMeta;
use Modules\GeneralSetting\Models\BlogCategory;
use Modules\GeneralSetting\Models\Currency;
use Modules\GeneralSetting\Models\GeneralSetting;
use Modules\GeneralSetting\Models\Language;
use Modules\GeneralSetting\Models\TranslationLanguage;
use Modules\Page\Http\Requests\PageRequest;
use Modules\Page\Models\Page;
use Modules\Page\Repositories\Contracts\PageInterface;

class PageController extends Controller
{
    protected $pageRepository;

    public function __construct(PageInterface $pageRepository)
    {
        $this->pageRepository = $pageRepository;
    }

    public function index(): View
    {
        $authUser = current_user();
        $languages = Language::with('transLang')->get();
        return view('page::page.index', compact("authUser", "languages"));
    }

    public function addPage(): View
    {
        $authUser = current_user();
        return view('page::page.add.index', compact("authUser"));
    }

    public function editPage(string $slug, Request $request): View
    {
        $languageId = $request->query('language_id');
        $language = TranslationLanguage::find($languageId);

        $slugsToTry = [$slug, Str::start($slug, 'pages/')];

        $query = Page::whereIn('slug', $slugsToTry)
            ->when($languageId, fn ($q) => $q->where('language_id', $languageId))
            ->first();
        if (!$query && $languageId) {
            $basePage = Page::whereIn('slug', $slugsToTry)
                ->whereNull('parent_id')
                ->first();

            if ($basePage) {
                $query = Page::where('parent_id', $basePage->id)
                    ->where('language_id', $languageId)
                    ->first();
                if (!$query) {
                    $query = new Page([
                        'language_id' => $languageId,
                        'parent_id'   => $basePage->id,
                        'theme_id'    => $basePage->theme_id,
                    ]);
                }
            }
        }

        if (!$query) {
            $basePage = Page::whereIn('slug', $slugsToTry)->first();

            if ($basePage) {
                $parentId = $basePage->parent_id ?? $basePage->id;
                $query = Page::where('parent_id', $parentId)
                    ->where('language_id', $languageId)
                    ->first();
                if (!$query) {
                    $query = new Page([
                        'language_id' => $languageId,
                        'parent_id'   => $parentId,
                        'theme_id'    => $basePage->theme_id,
                    ]);
                }
            }
        }

        if ($language) {
            app()->setLocale($language->code);
        }

        return view('page::page.edit.index', compact('query', 'languageId'));
    }

    public function getPageInfo(Request $request): JsonResponse
    {
        try {
            $pageSlug = $request->get('page_slug');
            $page = $this->pageRepository->findBySlug($pageSlug);

            if (!$page) {
                $fallbackSlug = 'pages/' . ltrim($pageSlug, '/');
                $page = $this->pageRepository->findBySlug($fallbackSlug);
            }

            if (!$page) {
                return response()->json(['exists' => 'no'], 404);
            }

            return response()->json(['exists' => 'yes']);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
    }

    public function pageContent(Request $request): JsonResponse
    {
        $pageId = $request->page_id;

        if (!$pageId) {
            return response()->json([
                'success' => false,
                'message' => 'Page ID is required'
            ], 400);
        }

        $page = $this->pageRepository->findById($pageId);

        if (!$page) {
            return response()->json([
                'success' => false,
                'message' => 'Page not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => [
                'page_content' => $page->page_content
            ]
        ], 200);
    }

    public function pageStore(PageRequest $request): JsonResponse
    {
        $authUser = current_user();

        if (!$authUser) {
            return response()->json([
                'code'    => 401,
                'message' => __('User is not authenticated')
            ], 401);
        }

        if (empty($request->page_content) || count($request->page_content) === 0) {
            return response()->json([
                'code'    => 422,
                'message' => __('Please add at least one section!'),
                'errors'  => ['page_content' => [__('Please add at least one section!')]]
            ], 422);
        }

        $sections = $this->prepareSections($request);
        $slug = Str::slug($request->slug);

        $data = [
            'page_title'      => $request->title,
            'slug'            => $slug,
            'page_content'    => json_encode($sections),
            'seo_tag'         => $request->meta_key,
            'seo_title'       => $request->mete_title,
            'seo_description' => $request->meta_description,
            'keywords'        => $request->meta_key,
            'canonical_url'   => $request->canonical_url,
            'og_title'        => $request->og_title,
            'og_description'  => $request->og_description,
            'language_id'     => $authUser->language_id ?? null,
            'status'          => 1,
        ];

        try {
            $this->pageRepository->create($data);
            return response()->json([
                'code'    => 200,
                'message' => __('page_create_success'),
                'data'    => []
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'code'    => 500,
                'message' => __('Something went wrong while saving!')
            ], 500);
        }
    }

    public function pageUpdate(PageRequest $request): JsonResponse
    {
        $sections = $this->prepareSections($request);
        $slug = Str::slug($request->slug);

        $data = [
            'page_title'      => $request->title,
            'parent_id'       => $request->parent_id,
            'page_content'    => json_encode($sections),
            'seo_tag'         => $request->meta_key,
            'seo_title'       => $request->mete_title,
            'seo_description' => $request->meta_description,
            'keywords'        => $request->meta_key,
            'canonical_url'   => $request->canonical_url,
            'og_title'        => $request->og_title,
            'og_description'  => $request->og_description,
            'status'          => 1,
        ];
        if ($request->read !== 'static') {
            $data['slug'] = $slug;
        }

        if ($request->filled('language_id')) {
            $data['language_id'] = $request->language_id;
        }

        if ($request->filled('page_id')) {
            $page = $this->pageRepository->update($request->page_id, $data);
            return response()->json([
                'code'    => 200,
                'message' => __('Page updated successfully'),
                'data'    => $page
            ]);
        } else {
            $data['language_id'] = $request->language_id;
            $page = $this->pageRepository->create($data);
            return response()->json([
                'code'    => 200,
                'message' => __('Page created successfully'),
                'data'    => $page
            ]);
        }
    }

    public function indexBuilderList(Request $request): JsonResponse
    {
        $filters = [
            'search'      => $request->input('search'),
            'status'      => $request->input('status'),
            'sort'        => $request->input('sort'),
            'language_id' => $request->input('language_id') ?? $request->input('lang_id'),
        ];

        $pages = $this->pageRepository->getPagesWithFilters($filters);

        $data = [];
        foreach ($pages as $page) {
            $data[] = [
                'id'           => $page->id,
                'page_title'   => $page->page_title,
                'read'         => $page->read,
                'slug'         => $page->slug,
                'page_content' => $page->page_content,
                'status'       => $page->status,
                'updated_date' => formatDateTime($page->updated_at, false),
            ];
        }

        return response()->json([
            'status' => 'success',
            'code'   => 200,
            'data'   => $data,
        ]);
    }

    protected function prepareSections(Request $request): array
    {
        $sections = [];
        $titles = $request->input('section_title', []);
        $labels = $request->input('section_label', []);
        $contents = $request->input('page_content', []);
        $statuses = $request->input('page_status', []);

        for ($i = 0; $i < count($titles); $i++) {
            $sections[] = [
                'section_title'   => $titles[$i] ?? '',
                'section_label'   => $labels[$i] ?? '',
                'section_content' => $contents[$i] ?? '',
                'status'          => isset($statuses[$i]) ? 1 : 0,
            ];
        }

        return $sections;
    }

    public function pageBuilderApi(Request $request): View|JsonResponse
    {
        $defaultThemeValue = GeneralSetting::where('key', 'default_theme')->first();

        $themeId = $defaultThemeValue ? intval($defaultThemeValue->value) : 1;

        $slug = $request->slug;

        $authUser = current_user();

        $lang_id = null;

        if ($authUser && !empty($authUser->language_id)) {
            $lang_id = $authUser->language_id;
        } elseif (App::getLocale()) {
            $currentLocale = App::getLocale();
            $language = TranslationLanguage::where('code', $currentLocale)->first();
            $lang_id = $language->id ?? null;
        } else {
            $defaultLang = Language::select("language_id")->where("default", 1)->first();
            $lang_id = $defaultLang->language_id ?? 1;
        }
        if (in_array($themeId, [1, 2, 3, 4], true)) {
            if ($slug === null || $slug === '/') {
                $slug = match ((int)$themeId) {
                    1 => 'home-screen-one',
                    2 => 'home-screen-two',
                    3 => 'home-screen-three',
                    4 => 'home-screen-four',
                };
            }
        }

        if (!$slug) {
            return response()->json(["status" => "error", "message" =>  __('Slug must be specified')]);
        }
        $page = Page::where('slug', $slug)->where('theme_id', $themeId)->where('language_id', $lang_id)->first();

        if (!$page) {
            $basePage = Page::where('slug', $slug)->whereNull('parent_id')->first();

            if ($basePage) {
                $page = Page::where('parent_id', $basePage->id)->where('language_id', $lang_id)->where('theme_id', $themeId)->first();
            }
        }

        if (!$page) {
            return response()->json(['code' => 404, 'message' => __('Page not found.'), 'data' => []], 404);
        }

        $pageContentSections = json_decode($page->page_content ?? '[]', true) ?? [];

        if (empty($pageContentSections) || !collect((array)$pageContentSections)->contains(fn ($section) => $section['status'] == 1)) {
            $pageContentSections = [];
        } else {
            foreach ($pageContentSections as &$section) {
                // Banner One
                if (is_array($section) && ($section['status'] ?? 0) == 1) {
                    $content = $section['section_content'] ?? '';

                    if (is_string($content) && strpos($content, '[banner_one') !== false) {
                        preg_match('/limit=(\d+)\s+viewall=(yes|no)\s+order=(asc|desc)/', $content, $matches);

                        $limit = (int)($matches[1] ?? 10);
                        $order = $matches[3] ?? 'asc';

                        $banners = DB::table('sections')
                            ->join('section_datas', function ($join) use ($lang_id) {
                                $join->on('sections.id', '=', 'section_datas.section_id')
                                    ->where('section_datas.language_id', '=', $lang_id);
                            })
                            ->select('sections.id', 'section_datas.datas')
                            ->where('sections.name', 'Banner One')
                            ->orderBy('sections.id', $order)
                            ->limit($limit)
                            ->get();

                        foreach ($banners as &$banner) {
                            $decodedData = json_decode($banner->datas, true);
                            $decodedData = is_array($decodedData) ? $decodedData : [];

                            $banner->label = $decodedData['label_one'] ?? null;
                            $banner->line_one = $decodedData['line_one'] ?? null;
                            $banner->line_two = $decodedData['line_two'] ?? null;
                            $banner->description = $decodedData['description_one'] ?? null;

                            $relativePath = 'storage/' . ($decodedData['thumbnail_image_one'] ?? '');
                            $defaultImage = asset('frontend/assets/img/banner/placeholder-banner.jpg');
                            $thumbnailKey = 'thumbnail_image_one';

                            $banner->thumbnail_image = (
                                isset($decodedData[$thumbnailKey]) &&
                                !empty($decodedData[$thumbnailKey]) &&
                                file_exists(public_path($relativePath))
                            ) ? asset($relativePath) : $defaultImage;

                            unset($banner->datas);
                        }

                        $section['section_type'] = 'banner';
                        $section['type'] = 'banner';
                        $section['design'] = 'banner_one';
                        $section['section_content'] = $banners;
                    }
                }

                // Banner Two
                if ($section['status'] == 1) {
                    if (isset($section['section_content']) && strpos($section['section_content'], '[banner_two') !== false) {
                        preg_match('/limit=(\d+)\s+viewall=(yes|no)\s+order=(asc|desc)/', $section['section_content'], $matches);

                        // Ensure $limit is cast to an integer
                        $limit = (int)($matches[1] ?? 10);  // Explicitly cast to integer
                        $order = $matches[3] ?? 'asc';

                        // Fetch from sections + section_datas with language-specific data
                        $banners = DB::table('sections')
                            ->join('section_datas', function ($join) use ($lang_id) {
                                $join->on('sections.id', '=', 'section_datas.section_id')
                                    ->where('section_datas.language_id', '=', $lang_id);
                            })
                            ->select('sections.id', 'section_datas.datas')
                            ->where('sections.name', 'Banner Two')
                            ->orderBy('sections.id', $order)
                            ->limit($limit)  // Ensure $limit is an integer
                            ->get();

                        $userCount = User::count();

                        foreach ($banners as &$banner) {
                            $decodedData = json_decode($banner->datas, true);

                            $banner->label = $decodedData['label_two'] ?? null;
                            $banner->description = $decodedData['description_two'] ?? null;

                            $relativePath = 'storage/' . ($decodedData['thumbnail_image_two'] ?? '');
                            $defaultImage = asset('frontend/assets/img/banner/placeholder-banner.jpg');
                            $thumbnailKey = 'thumbnail_image_two';

                            $banner->thumbnail_image = (
                                isset($decodedData[$thumbnailKey]) &&
                                !empty($decodedData[$thumbnailKey]) &&
                                file_exists(public_path($relativePath))
                            ) ? asset($relativePath) : $defaultImage;

                            $banner->customer_count = $userCount;

                            $banner->customer_images = [
                                asset('backend/assets/img/profiles/avatar-01.jpg'),
                                asset('backend/assets/img/profiles/avatar-02.jpg'),
                                asset('backend/assets/img/profiles/avatar-03.jpg'),
                            ];

                            unset($banner->datas);
                        }

                        $section['section_type'] = 'banner_two';
                        $section['type'] = 'banner_two';
                        $section['design'] = 'banner_two';
                        $section['section_content'] = $banners;
                    }
                }

                // Banner Three
                if ($section['status'] == 1) {
                    if (isset($section['section_content']) && strpos($section['section_content'], '[banner_three') !== false) {
                        preg_match('/limit=(\d+)\s+viewall=(yes|no)\s+order=(asc|desc)/', $section['section_content'], $matches);

                        $limit = (int)($matches[1] ?? 10);  // Explicitly cast to integer
                        $order = $matches[3] ?? 'asc';

                        $banners = DB::table('sections')
                            ->join('section_datas', function ($join) use ($lang_id) {
                                $join->on('sections.id', '=', 'section_datas.section_id')
                                    ->where('section_datas.language_id', '=', $lang_id);
                            })
                            ->select('sections.id', 'section_datas.datas')
                            ->where('sections.name', 'Banner Three')
                            ->orderBy('sections.id', $order)
                            ->limit($limit)  // Ensure $limit is an integer
                            ->get();

                        $userCount = User::count();

                        foreach ($banners as &$banner) {
                            $decodedData = json_decode($banner->datas, true);

                            $banner->label = $decodedData['label_three'] ?? null;
                            $banner->description = $decodedData['description_three'] ?? null;

                            $relativePath = 'storage/' . ($decodedData['thumbnail_image_four'] ?? '');
                            $defaultImage = asset('frontend/assets/img/banner/placeholder-banner.jpg');
                            $thumbnailKey = 'thumbnail_image_four';

                            $banner->thumbnail_image = (
                                isset($decodedData[$thumbnailKey]) &&
                                !empty($decodedData[$thumbnailKey]) &&
                                file_exists(public_path($relativePath))
                            ) ? asset($relativePath) : $defaultImage;

                            $banner->customer_count = $userCount;

                            $banner->customer_images = [
                                asset('backend/assets/img/profiles/avatar-01.jpg'),
                                asset('backend/assets/img/profiles/avatar-02.jpg'),
                                asset('backend/assets/img/profiles/avatar-03.jpg'),
                            ];

                            unset($banner->datas);
                        }

                        $section['section_type'] = 'banner_three';
                        $section['type'] = 'banner_three';
                        $section['design'] = 'banner_three';
                        $section['section_content'] = $banners;
                    }
                }

                // Banner Four
                if ($section['status'] == 1) {
                    if (isset($section['section_content']) && strpos($section['section_content'], '[banner_four') !== false) {
                        preg_match('/limit=(\d+)\s+viewall=(yes|no)\s+order=(asc|desc)/', $section['section_content'], $matches);

                        $limit = (int)($matches[1] ?? 10);
                        $order = $matches[3] ?? 'asc';

                        $banners = DB::table('sections')
                            ->join('section_datas', function ($join) use ($lang_id) {
                                $join->on('sections.id', '=', 'section_datas.section_id')
                                    ->where('section_datas.language_id', '=', $lang_id);
                            })
                            ->select('sections.id', 'section_datas.datas')
                            ->where('sections.name', 'Banner Four')
                            ->orderBy('sections.id', $order)
                            ->limit($limit)
                            ->get();

                        $userCount = User::count();

                        foreach ($banners as &$banner) {
                            $decodedData = json_decode($banner->datas, true);

                            $banner->label = $decodedData['label_boat_one'] ?? null;
                            $banner->higlight_label = $decodedData['label_boat_two'] ?? null;
                            $banner->description = $decodedData['description_boat'] ?? null;

                            $defaultImage = asset('frontend/assets/img/banner/placeholder-banner.jpg');
                            $thumbnailImages = [];

                            if (!empty($decodedData['thumbnail_image_boat']) && is_array($decodedData['thumbnail_image_boat'])) {
                                foreach ($decodedData['thumbnail_image_boat'] as $imagePath) {
                                    $fullPath = public_path('storage/' . $imagePath);
                                    $thumbnailImages[] = file_exists($fullPath) ? asset('storage/' . $imagePath) : $defaultImage;
                                }
                            }

                            $banner->thumbnail_images = $thumbnailImages;
                            $banner->customer_count = $userCount;

                            $banner->customer_images = [
                                asset('backend/assets/img/profiles/avatar-01.jpg'),
                                asset('backend/assets/img/profiles/avatar-02.jpg'),
                                asset('backend/assets/img/profiles/avatar-03.jpg'),
                            ];

                            unset($banner->datas);
                        }

                        $section['section_type'] = 'banner_four';
                        $section['type'] = 'banner_four';
                        $section['design'] = 'banner_four';
                        $section['section_content'] = $banners;
                    }
                }

                // BestVehicle
                if (is_array($section) && ($section['status'] ?? 0) == 1) {
                    $content = $section['section_content'] ?? '';

                    if (is_string($content) && strpos($content, '[bestVehicle') !== false) {
                        preg_match('/limit=(\d+)\s+viewall=(yes|no)\s+order=(asc|desc)/', $content, $matches);

                        $limit = (int)($matches[1] ?? 10);
                        $order = $matches[3] ?? 'asc';

                        $best_vehicles = DB::table('sections')
                            ->join('section_datas', function ($join) use ($lang_id) {
                                $join->on('sections.id', '=', 'section_datas.section_id')
                                    ->where('section_datas.language_id', '=', $lang_id);
                            })
                            ->select('sections.id', 'section_datas.datas')
                            ->where('sections.name', 'Best Vehicle')
                            ->orderBy('sections.id', $order)
                            ->limit($limit)
                            ->get();

                        foreach ($best_vehicles as &$best_vehicle) {
                            $decodedData = json_decode($best_vehicle->datas, true);
                            $decodedData = is_array($decodedData) ? $decodedData : [];

                            $vehicleId = $decodedData['vehicle_id'] ?? null;
                            $best_vehicle->vehicle_id = $vehicleId;

                            if ($vehicleId) {
                                $vehicle = VehicleInfo::where("language_id", $lang_id)->find($vehicleId);

                                $best_vehicle->vehicle_name = $vehicle->name ?? null;

                                $imagePath = $vehicle->vehicle_image ?? null;
                                $best_vehicle->vehicle_image_url = $imagePath ? asset('storage/' . $imagePath) : null;
                            } else {
                                $best_vehicle->vehicle_name = null;
                                $best_vehicle->vehicle_image_url = null;
                            }

                            $best_vehicle->label_1 = $decodedData['label_1'] ?? null;
                            $best_vehicle->dis_1 = $decodedData['dis_1'] ?? null;

                            $best_vehicle->label_2 = $decodedData['label_2'] ?? null;
                            $best_vehicle->dis_2 = $decodedData['dis_2'] ?? null;

                            $best_vehicle->label_3 = $decodedData['label_3'] ?? null;
                            $best_vehicle->dis_3 = $decodedData['dis_3'] ?? null;

                            $best_vehicle->label_4 = $decodedData['label_4'] ?? null;
                            $best_vehicle->dis_4 = $decodedData['dis_4'] ?? null;

                            $best_vehicle->label_5 = $decodedData['label_5'] ?? null;
                            $best_vehicle->dis_5 = $decodedData['dis_5'] ?? null;

                            $best_vehicle->label_6 = $decodedData['label_6'] ?? null;
                            $best_vehicle->dis_6 = $decodedData['dis_6'] ?? null;

                            unset($best_vehicle->content);
                        }

                        $section['section_type'] = 'best_vehicle';
                        $section['type'] = 'best_vehicle';
                        $section['design'] = 'best_vehicle';
                        $section['section_content'] = $best_vehicles;
                    }
                }

                // Brands section
                if (is_array($section) && ($section['status'] ?? 0) == 1) {
                    $content = $section['section_content'] ?? '';

                    if (is_string($content) && strpos($content, '[brand') !== false) {
                        preg_match('/limit=(\d+)\s+viewall=(yes|no)\s+order=(asc|desc)/', $content, $matches);
                        $limit = (int)($matches[1] ?? 10);
                        $order = $matches[3] ?? 'asc';

                        $getCategoryId = getCategoryId();
                        $brands = DB::table('brands')
                            ->select('id', 'brand_image', 'brand_icon', 'brand_name', 'status')
                            ->where('category_id', $getCategoryId)
                            ->where('language_id', $lang_id)
                            ->where('status', 1)
                            ->whereNull('deleted_at')
                            ->orderBy('created_at', $order)
                            ->limit($limit)
                            ->get()
                            ->map(function ($brand) {
                                $brand->brand_image = asset('storage/' . $brand->brand_image);
                                $brand->brand_icon = asset('storage/' . $brand->brand_icon);
                                return $brand;
                            });

                        $section['section_type'] = 'brands';
                        $section['design'] = 'brand_one';
                        $section['section_content'] = $brands;
                    }
                }

                // Location section
                if (is_array($section) && ($section['status'] ?? 0) == 1) {
                    $content = $section['section_content'] ?? '';

                    if (is_string($content) && strpos($content, '[location') !== false) {
                        preg_match('/limit=(\d+)\s+viewall=(yes|no)\s+order=(asc|desc)/', $content, $matches);
                        $limit = (int)($matches[1] ?? 10);
                        $order = $matches[3] ?? 'asc';

                        asset('frontend/assets/img/placeholder-app-car.jpg');

                        $locations = DB::table('locations')
                            ->select('id', 'name', 'image')
                            ->where('language_id', $lang_id)
                            ->where('status', 1)
                            ->whereNull('deleted_at')
                            ->orderBy('created_at', $order)
                            ->limit($limit)
                            ->get()
                            ->map(function ($location) use ($defaultImage) {
                                if ($location->image && Storage::disk('public')->exists($location->image)) {
                                    $location->image = asset('storage/' . $location->image);
                                } else {
                                    $location->image = $defaultImage;
                                }

                                $getCategoryId = getCategoryId();
                                $location->vehicle_count = DB::table('vehicle_info')
                                    ->where('main_location_id', $location->id)
                                    ->where('category_id', $getCategoryId)
                                    ->whereNull('deleted_at')
                                    ->count();

                                return $location;
                            });

                        $section['section_type'] = 'locations';
                        $section['type'] = 'locations';
                        $section['design'] = 'brand_one';
                        $section['section_content'] = $locations;
                    }
                }

                // Category section
                if (is_array($section) && ($section['status'] ?? 0) == 1) {
                    if (
                        isset($section['section_content']) &&
                        is_string($section['section_content']) &&
                        strpos($section['section_content'], '[category ') !== false
                    ) {
                        preg_match('/limit=(\d+)\s+viewall=(yes|no)\s+order=(asc|desc)/', $section['section_content'], $matches);
                        $limit = $matches[1] ?? 6;
                        $order = $matches[3] ?? 'asc';
                        $categoryId = getCategoryId();
                        $category = Cartype::select('name', 'icon', 'id')
                            ->limit((int) $limit)
                            ->where('type', 'car')
                            ->where('language_id', $lang_id)
                            ->where('category_id', $categoryId)
                            ->where('status', 1)
                            ->whereNull('deleted_at')
                            ->get()
                            ->map(function ($cartype) use ($lang_id) {
                                $cartype->car_count = VehicleInfo::where('type_id', $cartype->id)
                                    ->where('language_id', $lang_id)
                                    ->count();

                                $cartype->image_url = $cartype->icon
                                    ? asset('storage/' . ltrim($cartype->icon, '/'))
                                    : asset('images/default.png');

                                return $cartype;
                            });

                        $section['section_type'] = 'featured_category';
                        $section['design'] = 'category_one';
                        $section['section_content'] = $category;
                    }
                }

                // Category section Bike
                if (is_array($section) && ($section['status'] ?? 0) == 1) {
                    if (
                        isset($section['section_content']) &&
                        is_string($section['section_content']) &&
                        strpos($section['section_content'], '[bike_category ') !== false
                    ) {
                        preg_match('/limit=(\d+)\s+viewall=(yes|no)\s+order=(asc|desc)/', $section['section_content'], $matches);
                        $limit = $matches[1] ?? 6;
                        $order = $matches[3] ?? 'asc';
                        $categoryId = getCategoryId();
                        $category = Cartype::select('name', 'icon', 'id', 'type')
                            ->limit((int) $limit)
                            ->where('type', 'bike')
                            ->where('language_id', $lang_id)
                            ->where('category_id', $categoryId)
                            ->where('status', 1)
                            ->whereNull('deleted_at')
                            ->get()
                            ->map(function ($cartype) use ($lang_id) {
                                $cartype->car_count = VehicleInfo::where('type_id', $cartype->id)
                                    ->where('language_id', $lang_id)
                                    ->count();

                                $cartype->image_url = $cartype->icon
                                    ? asset('storage/' . ltrim($cartype->icon, '/'))
                                    : asset('images/default.png');

                                return $cartype;
                            });

                        $section['section_type'] = 'featured_category';
                        $section['design'] = 'category_one';
                        $section['section_content'] = $category;
                    }
                }

                // Category section Boat
                if (is_array($section) && ($section['status'] ?? 0) == 1) {
                    if (
                        isset($section['section_content']) &&
                        is_string($section['section_content']) &&
                        strpos($section['section_content'], '[boat_category ') !== false
                    ) {
                        preg_match('/limit=(\d+)\s+viewall=(yes|no)\s+order=(asc|desc)/', $section['section_content'], $matches);
                        $limit = $matches[1] ?? 6;
                        $order = $matches[3] ?? 'asc';

                        $category = Cartype::select('name', 'icon', 'id', 'type')
                            ->limit((int) $limit)
                            ->where('type', 'boat')
                            ->where('language_id', $lang_id)
                            ->where('status', 1)
                            ->whereNull('deleted_at')
                            ->get()
                            ->map(function ($cartype) use ($lang_id) {
                                $cartype->boat_count = VehicleInfo::where('type_id', $cartype->id)
                                    ->where('language_id', $lang_id)->where('type', 'boat')
                                    ->count();

                                $cartype->image_url = $cartype->icon
                                    ? asset('storage/' . ltrim($cartype->icon, '/'))
                                    : asset('images/default.png');

                                return $cartype;
                            });

                        $section['section_type'] = 'featured_category';
                        $section['design'] = 'category_one';
                        $section['section_content'] = $category;
                    }
                }

                // FAQ Section (with Facts)
                if (is_array($section) && ($section['status'] ?? 0) == 1) {
                    $content = $section['section_content'] ?? '';

                    if (is_string($content) && strpos($content, '[facts_with_faq') !== false) {
                        // Parse shortcode parameters
                        preg_match('/limit=(\d+)\s+viewall=(yes|no)\s+order=(asc|desc)/', $content, $matches);
                        $limit = $matches[1] ?? 10;
                        $order = $matches[3] ?? 'asc';

                        // Fetch FAQ data
                        $faqs = DB::table('faqs')
                            ->select('id', 'question', 'answer', 'status')
                            ->where('status', 1)
                            ->whereNull('deleted_at')
                            ->where('language_id', $lang_id)
                            ->orderBy('created_at', $order)
                            ->limit((int) $limit)
                            ->get();

                        // Fetch Facts data
                        $getCategoryId = getCategoryId();
                        $userCount = User::where("user_type", 3)->count();
                        $vehicleCount = VehicleInfo::where('category_id', $getCategoryId)->count();
                        $locationCount = Location::where('language_id', $lang_id)->count();
                        $totalKm = 1976;

                        $facts = [
                            ["key" => "happy_customers", "value" => $userCount],
                            ["key" => "vehicle_count", "value" => $vehicleCount],
                            ["key" => "location_count", "value" => $locationCount],
                            ["key" => "total_km", "value" => $totalKm],
                        ];

                        // Set section attributes
                        $section['section_type'] = 'faq_with_facts';
                        $section['design'] = 'faq_two_with_facts';
                        $section['faqs'] = $faqs;
                        $section['facts'] = $facts;
                    }
                }

                // FAQ Section (with Facts)
                if (is_array($section) && ($section['status'] ?? 0) == 1) {
                    $content = $section['section_content'] ?? '';

                    if (is_string($content) && strpos($content, '[fact_blogs') !== false) {
                        // Parse shortcode parameters
                        preg_match('/limit=(\d+)\s+viewall=(yes|no)\s+order=(asc|desc)/', $content, $matches);
                        $limit = $matches[1] ?? 10;
                        $order = $matches[3] ?? 'asc';

                        // Fetch FAQ data
                        $faqs = DB::table('faqs')
                            ->select('id', 'question', 'answer', 'status')
                            ->where('status', 1)
                            ->whereNull('deleted_at')
                            ->where('language_id', $lang_id)
                            ->orderBy('created_at', $order)
                            ->limit((int) $limit)
                            ->get();

                        // Fetch Facts data
                        $userCount = User::count(); // Get total users
                        $vehicleCount = VehicleInfo::where('type', 'boat')->count(); // Get total vehicles
                        $locationCount = Location::count(); // Get total locations
                        $totalKm = 1976; // Keeping total_km static

                        $facts = [
                            ["key" => "happy_customers", "value" => $userCount],
                            ["key" => "vehicle_count", "value" => $vehicleCount],
                            ["key" => "location_count", "value" => $locationCount],
                            ["key" => "total_km", "value" => $totalKm],
                        ];

                        // Set section attributes
                        $section['section_type'] = 'faq_with_facts';
                        $section['design'] = 'faq_two_with_facts';
                        $section['faqs'] = $faqs;
                        $section['facts'] = $facts;
                    }
                }

                // FAQ Section
                if (is_array($section) && ($section['status'] ?? 0) == 1) {
                    $content = $section['section_content'] ?? '';

                    if (is_string($content) && strpos($content, '[faq') !== false) {
                        preg_match('/limit=(\d+)\s+viewall=(yes|no)\s+order=(asc|desc)/', $content, $matches);
                        $limit = $matches[1] ?? 10;
                        $order = $matches[3] ?? 'asc';

                        $faqs = DB::table('faqs')
                            ->select('id', 'question', 'answer', 'status')
                            ->where('status', 1)
                            ->whereNull('deleted_at')
                            ->where('language_id', $lang_id)
                            ->orderBy('created_at', $order)
                            ->limit((int) $limit)
                            ->get();

                        $section['section_type'] = 'faq';
                        $section['design'] = 'faq_one';
                        $section['section_content'] = $faqs;
                    }
                }

                // How It Works
                if (is_array($section) && ($section['status'] ?? 0) == 1) {
                    $content = $section['section_content'] ?? '';

                    if (is_string($content) && strpos($content, '[how_it_work') !== false) {
                        preg_match('/limit=(\d+)\s+viewall=(yes|no)\s+order=(asc|desc)/', $content, $matches);
                        $limit = $matches[1] ?? 10;
                        $order = $matches[3] ?? 'asc';

                        if ($themeId == 3) {
                            // Theme 3: Fetch by specific key
                            $how_it_works = DB::table('general_settings')
                                ->select('id', 'key', 'value', 'group_id')
                                ->where('key', 'how_it_works_theme_3')
                                ->where('language_id', $lang_id)
                                ->orderBy('created_at', $order)
                                ->limit((int) $limit)
                                ->get();

                            $sectionType = 'how_it_works';
                        } elseif ($themeId == 4) {
                            // Theme 4: Fetch by specific key
                            $how_it_works = DB::table('general_settings')
                                ->select('id', 'key', 'value', 'group_id')
                                ->where('key', 'how_it_works_theme_4')
                                ->where('language_id', $lang_id)
                                ->orderBy('created_at', $order)
                                ->limit((int) $limit)
                                ->get();

                            $sectionType = 'how_it_works';
                        } else {
                            // Other themes: Use group_id = 10
                            $how_it_works = DB::table('general_settings')
                                ->select('id', 'key', 'value', 'group_id')
                                ->where('group_id', 10)
                                ->where('language_id', $lang_id)
                                ->orderBy('created_at', $order)
                                ->limit((int) $limit)
                                ->get();

                            $sectionType = 'how_it_works';
                        }

                        // Build section
                        $section['section_type'] = $sectionType;
                        $section['design'] = 'how_it_works_one';
                        $section['section_content'] = $how_it_works;
                    }
                }

                // Vechile
                if ($section['status'] == 1) {
                    if (isset($section['section_content']) && strpos($section['section_content'], '[vehicle') !== false) {
                        preg_match('/type=([a-zA-Z]+)\s+limit=(\d+)\s+viewall=(yes|no)/', $section['section_content'], $matches);
                        $type = $matches[1] ?? 'all';
                        $limit = $matches[2] ?? 10;

                        $query = VehicleInfo::with([
                            'carType:id,name',
                            'brand:id,brand_name',
                            'category:id,name',
                            'mainLocation:id,name',
                            'color:id,name,value',
                            'fuel_type:id,fuel_type',
                            'transmission:id,name',
                        ])->where('language_id', $lang_id);

                        if ($type === 'popular') {
                            $vehicles = $query->where('popular', 1)->where('type', 'car')->get();
                            $section['section_type'] = 'popular_vehicle';
                            $section['design'] = 'vehicle_one';
                        } elseif ($type === 'featured') {
                            $vehicles = $query->where('recommended', 1)->where('type', 'car')->get();
                            $section['section_type'] = 'feature_vehicle';
                            $section['design'] = 'vehicle_two';
                        } else {
                            $vehicles = $query->where('type', 'car')->get();
                            $section['section_type'] = 'al_vehicle';
                            $section['design'] = 'vehicle_three';
                        }

                        $data = $vehicles->map(function ($vehicle) {
                            $vehicleImages = VehicleMeta::where('vehicle_id', $vehicle->id)
                                ->where('key', 'vehicle_image')
                                ->first();

                            $vehiclePrices = $vehicle->vehicle_price ? json_decode($vehicle->vehicle_price, true) : [];
                            $filteredPrices = [];

                            if (!empty($vehiclePrices)) {
                                foreach ($vehiclePrices as $price) {
                                    $filteredPrice = array_filter($price, function ($value) {
                                        return $value > 0; // Only keep values greater than 0
                                    });

                                    if (!empty($filteredPrice)) {
                                        $filteredPrices[] = $filteredPrice;
                                    }
                                }
                            }
                            $multipleImages = $vehicleImages ? json_decode($vehicleImages->value, true) : [];

                            if (!empty($vehicle->vehicle_image)) {
                                array_unshift($multipleImages, $vehicle->vehicle_image);
                            }

                            $multipleImages = array_map(function ($img) {
                                $img = '/' . ltrim($img, '/'); // Ensure single leading slash

                                $img = str_replace('vehicles/images/', 'vehicles/images/small/', $img);

                                return url('storage' . $img);
                            }, $multipleImages);

                            /** @var \App\Models\User|null $auth */
                            $auth = current_user();
                            $authId = $auth?->id;

                            $wishlistExists = false;

                            if ($authId) {
                                $wishlistExists = Wishlist::where("user_id", $authId)
                                    ->where("vehicle_id", $vehicle->id)
                                    ->exists();
                            }

                            $currencySetting = GeneralSetting::where("key", "currency_symbol")->first();
                            $currency = null;

                            if ($currencySetting && $currencySetting->value) {
                                $currency = Currency::find($currencySetting->value);
                            }

                            $currencySymbol = $currency->symbol ?? "$";

                            $rating = Review::where("vehicle_id", $vehicle->id)->value("average_ratings") ?? 0;


                            $defaultAvatar = asset('/backend/assets/img/default-profile.png');
                            $profileImagePath = optional($vehicle->owner->userDetails)->profile_image;

                            $avatarImage = $defaultAvatar;

                            if ($profileImagePath) {
                                $fullImagePath = storage_path('app/public/' . $profileImagePath);
                                if (file_exists($fullImagePath)) {
                                    $avatarImage = url('/storage/' . $profileImagePath);
                                }
                            }
                            return [
                                'id'                      => $vehicle->id,
                                'name'                    => $vehicle->name,
                                'slug'                    => $vehicle->slug,
                                'vehicle_image'           => url('/storage/' . str_replace('vehicles/images/', 'vehicles/images/small/', $vehicle->vehicle_image)),
                                'multiple_vehicle_images' => $multipleImages,
                                'has_multiple_image'      => count($multipleImages) > 1,
                                'avatar_image'            => $avatarImage,
                                'brand_id'                => $vehicle->brand_id ?? null,
                                'brand'                   => $vehicle->brand->brand_name ?? null,
                                'car_type'                => $vehicle->carType->name ?? null,
                                'category'                => $vehicle->category->name ?? null,
                                'location'                => $vehicle->mainLocation->name ?? null,
                                'color'                   => $vehicle->color->name ?? null,
                                'fuel_type'               => $vehicle->fuel_type->fuel_type ?? null,
                                'transmission'            => $vehicle->transmission->name ?? null,
                                'year'                    => $vehicle->year,
                                'mileage'                 => $vehicle->mileage,
                                'odometer'                => $vehicle->odometer,
                                'rating'                  => $rating,
                                'currency'                => $currencySymbol,
                                'wishlist'                => $wishlistExists,
                                'passenger_capacity'      => $vehicle->passenger_capacity,
                                'num_seats'               => $vehicle->num_seats,
                                'num_doors'               => $vehicle->num_doors,
                                'num_airbags'             => $vehicle->num_airbags,
                                'vehicle_video'           => $vehicle->vehicle_video,
                                'features'                => $vehicle->features,
                                'price'                   => !empty($filteredPrices) ? $filteredPrices : null,
                                'is_featured'             => $vehicle->popular,
                                'is_top_rated'            => $vehicle->recommended,
                                'seo_title'               => $vehicle->vehicle_metatitle,
                                'seo_key'                 => $vehicle->vehicle_metakeywords,
                                'seo_description'         => $vehicle->vehicle_metadesc,
                                'created_at'              => $vehicle->created_at,
                            ];
                        });

                        $section['section_content'] = $data;
                    }
                }

                // Bike
                if ($section['status'] == 1) {
                    if (isset($section['section_content']) && strpos($section['section_content'], '[bike') !== false) {
                        preg_match('/type=([a-zA-Z]+)\s+limit=(\d+)\s+viewall=(yes|no)/', $section['section_content'], $matches);
                        $type = $matches[1] ?? 'all';
                        

                        $query = VehicleInfo::with([
                            'carType:id,name',
                            'brand:id,brand_name',
                            'category:id,name',
                            'mainLocation:id,name',
                            'color:id,name,value',
                            'fuel_type:id,fuel_type',
                            'transmission:id,name',
                        ])->where('language_id', $lang_id);

                        if ($type === 'popular') {
                            $vehicles = $query->where('popular', 1)->where('type', 'bike')->get();
                            $section['section_type'] = 'popular_vehicle';
                            $section['design'] = 'vehicle_one';
                        } elseif ($type === 'featured') {
                            $vehicles = $query->where('recommended', 1)->where('type', 'bike')->get();
                            $section['section_type'] = 'feature_vehicle';
                            $section['design'] = 'vehicle_two';
                        } elseif ($type === 'top_rated') {
                            $vehicles = $query->where('recommended', 1)->where('type', 'bike')->get();
                            $section['section_type'] = 'top_recommended';
                            $section['design'] = 'vehicle_three';
                        } else {
                            $vehicles = $query->where('recommended', 1)->where('type', 'bike')->get();
                            $section['section_type'] = 'top_recommended';
                            $section['design'] = 'vehicle_four';
                        }

                        $data = $vehicles->map(function ($vehicle) {
                            $vehicleImages = VehicleMeta::where('vehicle_id', $vehicle->id)
                                ->where('key', 'vehicle_image')
                                ->first();

                            $vehiclePrices = $vehicle->vehicle_price ? json_decode($vehicle->vehicle_price, true) : [];
                            $filteredPrices = [];

                            if (!empty($vehiclePrices)) {
                                foreach ($vehiclePrices as $price) {
                                    $filteredPrice = array_filter($price, function ($value) {
                                        return $value > 0; // Only keep values greater than 0
                                    });

                                    if (!empty($filteredPrice)) {
                                        $filteredPrices[] = $filteredPrice;
                                    }
                                }
                            }
                            $multipleImages = $vehicleImages ? json_decode($vehicleImages->value, true) : [];

                            if (!empty($vehicle->vehicle_image)) {
                                array_unshift($multipleImages, $vehicle->vehicle_image);
                            }

                            $multipleImages = array_map(function ($img) {
                                $img = '/' . ltrim($img, '/'); // Ensure single leading slash

                                $img = str_replace('vehicles/images/', 'vehicles/images/small/', $img);

                                return url('storage' . $img);
                            }, $multipleImages);

                            /** @var \App\Models\User|null $auth */
                            $auth = current_user();
                            $authId = $auth?->id;

                            $wishlistExists = false;

                            if ($authId) {
                                $wishlistExists = Wishlist::where("user_id", $authId)
                                    ->where("vehicle_id", $vehicle->id)
                                    ->exists();
                            }

                            $currencySetting = GeneralSetting::where("key", "currency_symbol")->first();
                            $currency = null;

                            if ($currencySetting && $currencySetting->value) {
                                $currency = Currency::find($currencySetting->value);
                            }

                            $currencySymbol = $currency->symbol ?? "$";

                            $rating = Review::where("vehicle_id", $vehicle->id)->value("average_ratings") ?? 0;


                            $defaultAvatar = asset('/backend/assets/img/default-profile.png');
                            $profileImagePath = optional($vehicle->owner->userDetails)->profile_image;

                            $avatarImage = $defaultAvatar;

                            if ($profileImagePath) {
                                $fullImagePath = storage_path('app/public/' . $profileImagePath);
                                if (file_exists($fullImagePath)) {
                                    $avatarImage = url('/storage/' . $profileImagePath);
                                }
                            }
                            return [
                                'id'                      => $vehicle->id,
                                'name'                    => $vehicle->name,
                                'slug'                    => $vehicle->slug,
                                'vehicle_image'           => url('/storage/' . str_replace('vehicles/images/', 'vehicles/images/small/', $vehicle->vehicle_image)),
                                'multiple_vehicle_images' => $multipleImages,
                                'has_multiple_image'      => count($multipleImages) > 1,
                                'avatar_image'            => $avatarImage,
                                'brand_id'                => $vehicle->brand_id ?? null,
                                'brand'                   => $vehicle->brand->brand_name ?? null,
                                'car_type'                => $vehicle->carType->name ?? null,
                                'category'                => $vehicle->category->name ?? null,
                                'tube_type'               => Arr::random(['Tube', 'Tubeless']),
                                'break_type'              => Arr::random(['Drum', 'Disc']),
                                'location'                => $vehicle->mainLocation->name ?? null,
                                'color'                   => $vehicle->color->name ?? null,
                                'fuel_type'               => $vehicle->fuel_type->fuel_type ?? null,
                                'transmission'            => $vehicle->transmission->name ?? null,
                                'year'                    => $vehicle->year,
                                'mileage'                 => $vehicle->mileage,
                                'odometer'                => $vehicle->odometer,
                                'rating'                  => $rating,
                                'total_review'            => Review::where("vehicle_id", $vehicle->id)->count(),
                                'currency'                => $currencySymbol,
                                'wishlist'                => $wishlistExists,
                                'passenger_capacity'      => $vehicle->passenger_capacity,
                                'num_seats'               => $vehicle->num_seats,
                                'num_doors'               => $vehicle->num_doors,
                                'num_airbags'             => $vehicle->num_airbags,
                                'vehicle_video'           => $vehicle->vehicle_video,
                                'features'                => $vehicle->features,
                                'price'                   => !empty($filteredPrices) ? $filteredPrices : null,
                                'is_featured'             => $vehicle->popular,
                                'is_top_rated'            => $vehicle->recommended,
                                'seo_title'               => $vehicle->vehicle_metatitle,
                                'seo_key'                 => $vehicle->vehicle_metakeywords,
                                'seo_description'         => $vehicle->vehicle_metadesc,
                                'created_at'              => $vehicle->created_at,
                            ];
                        });

                        $section['section_content'] = $data;
                    }
                }

                // Yachts
                if ($section['status'] == 1) {
                    if (isset($section['section_content']) && strpos($section['section_content'], '[yachts') !== false) {
                        preg_match('/type=([a-zA-Z]+)\s+limit=(\d+)\s+viewall=(yes|no)/', $section['section_content'], $matches);
                        $type = $matches[1] ?? 'all';
                        $limit = $matches[2] ?? 10;
                        

                        $query = VehicleInfo::with([
                            'carType:id,name',
                            'brand:id,brand_name',
                            'category:id,name',
                            'mainLocation:id,name',
                            'color:id,name,value',
                            'fuel_type:id,fuel_type',
                            'transmission:id,name',
                        ])->where('language_id', $lang_id);

                        if ($type === 'popular') {
                            $getCategoryId = getCategoryId();
                            $brands = DB::table('brands')
                                ->select('id', 'brand_image', 'brand_icon', 'brand_name', 'status')
                                ->where('category_id', $getCategoryId)
                                ->where('language_id', $lang_id)
                                ->where('status', 1)
                                ->whereNull('deleted_at')
                                ->orderBy('created_at', $order)
                                ->limit($limit)
                                ->get()
                                ->map(function ($brand) {
                                    $brand->brand_image = asset('storage/' . $brand->brand_image);
                                    $brand->brand_icon = asset('storage/' . $brand->brand_icon);
                                    $brand->brand_title = "Select From Professional Charter Companies";
                                    return $brand;
                                });
                            $vehicles = $query->where('popular', 1)->where('type', 'boat')->get();
                            $section['section_type'] = 'popular_vehicle';
                            $section['design'] = 'vehicle_one';
                        } elseif ($type === 'featured') {
                            $vehicles = $query->where('recommended', 1)->where('type', 'boat')->get();
                            $section['section_type'] = 'feature_vehicle';
                            $section['design'] = 'vehicle_two';
                        } else {
                            $vehicles = $query->where('type', 'boat')->get();
                            $section['section_type'] = 'al_vehicle';
                            $section['design'] = 'vehicle_three';
                        }

                        $data = $vehicles->map(function ($vehicle) {
                            $vehicleImages = VehicleMeta::where('vehicle_id', $vehicle->id)
                                ->where('key', 'vehicle_image')
                                ->first();

                            $vehiclePrices = $vehicle->vehicle_price ? json_decode($vehicle->vehicle_price, true) : [];
                            $filteredPrices = [];

                            if (!empty($vehiclePrices)) {
                                foreach ($vehiclePrices as $price) {
                                    $filteredPrice = array_filter($price, function ($value) {
                                        return $value > 0; // Only keep values greater than 0
                                    });

                                    if (!empty($filteredPrice)) {
                                        $filteredPrices[] = $filteredPrice;
                                    }
                                }
                            }
                            $multipleImages = $vehicleImages ? json_decode($vehicleImages->value, true) : [];

                            if (!empty($vehicle->vehicle_image)) {
                                array_unshift($multipleImages, $vehicle->vehicle_image);
                            }

                            $multipleImages = array_map(function ($img) {
                                $img = '/' . ltrim($img, '/'); // Ensure single leading slash

                                $img = str_replace('vehicles/images/', 'vehicles/images/small/', $img);

                                return url('storage' . $img);
                            }, $multipleImages);

                            /** @var \App\Models\User|null $auth */
                            $auth = current_user();
                            $authId = $auth?->id;

                            $wishlistExists = false;

                            if ($authId) {
                                $wishlistExists = Wishlist::where("user_id", $authId)
                                    ->where("vehicle_id", $vehicle->id)
                                    ->exists();
                            }

                            $currencySetting = GeneralSetting::where("key", "currency_symbol")->first();
                            $currency = null;

                            if ($currencySetting && $currencySetting->value) {
                                $currency = Currency::find($currencySetting->value);
                            }

                            $currencySymbol = $currency->symbol ?? "$";

                            $rating = Review::where("vehicle_id", $vehicle->id)->value("average_ratings") ?? 0;


                            $defaultAvatar = asset('/backend/assets/img/default-profile.png');
                            $profileImagePath = optional($vehicle->owner->userDetails)->profile_image;

                            $avatarImage = $defaultAvatar;

                            if ($profileImagePath) {
                                $fullImagePath = storage_path('app/public/' . $profileImagePath);
                                if (file_exists($fullImagePath)) {
                                    $avatarImage = url('/storage/' . $profileImagePath);
                                }
                            }
                            return [
                                'id'                      => $vehicle->id,
                                'name'                    => $vehicle->name,
                                'slug'                    => $vehicle->slug,
                                'vehicle_image'           => url('/storage/' . str_replace('vehicles/images/', 'vehicles/images/small/', $vehicle->vehicle_image)),
                                'multiple_vehicle_images' => $multipleImages,
                                'has_multiple_image'      => count($multipleImages) > 1,
                                'avatar_image'            => $avatarImage,
                                'brand_id'                => $vehicle->brand_id ?? null,
                                'brand'                   => $vehicle->brand->brand_name ?? null,
                                'car_type'                => $vehicle->carType->name ?? null,
                                'category'                => $vehicle->category->name ?? null,
                                'tube_type'               => Arr::random(['Tube', 'Tubeless']),
                                'break_type'              => Arr::random(['Drum', 'Disc']),
                                'location'                => $vehicle->mainLocation->name ?? null,
                                'color'                   => $vehicle->color->name ?? null,
                                'fuel_type'               => $vehicle->fuel_type->fuel_type ?? null,
                                'transmission'            => $vehicle->transmission->name ?? null,
                                'year'                    => $vehicle->year,
                                'mileage'                 => $vehicle->mileage,
                                'odometer'                => $vehicle->odometer,
                                'rating'                  => $rating,
                                'total_review'            => Review::where("vehicle_id", $vehicle->id)->count(),
                                'currency'                => $currencySymbol,
                                'wishlist'                => $wishlistExists,
                                'passenger_capacity'      => $vehicle->passenger_capacity,
                                'num_seats'               => $vehicle->num_seats,
                                'num_doors'               => $vehicle->num_doors,
                                'num_airbags'             => $vehicle->num_airbags,
                                'vehicle_video'           => $vehicle->vehicle_video,
                                'features'                => $vehicle->features,
                                'price'                   => !empty($filteredPrices) ? $filteredPrices : null,
                                'is_featured'             => $vehicle->popular,
                                'is_top_rated'            => $vehicle->recommended,
                                'seo_title'               => $vehicle->vehicle_metatitle,
                                'seo_key'                 => $vehicle->vehicle_metakeywords,
                                'seo_description'         => $vehicle->vehicle_metadesc,
                                'created_at'              => $vehicle->created_at,
                            ];
                        });

                        $section['section_content'] = [
                            'brands'   => $brands,
                            'vehicles' => $data,
                        ];
                    }
                }

                // Car Type
                if (is_array($section) && ($section['status'] ?? 0) == 1) {
                    $content = $section['section_content'] ?? '';

                    if (is_string($content) && strpos($content, '[car_type ') !== false) {
                        preg_match('/limit=(\d+)\s+viewall=(yes|no)/', $content, $matches);
                        $limit = isset($matches[1]) ? (int)$matches[1] : 10;
                        $viewAll = $matches[2] ?? 'no';

                        $cartypes = Cartype::select('name', 'icon', 'id')
                            ->where('language_id', $lang_id)
                            ->where('status', 1)
                            ->whereNull('deleted_at')
                            ->limit($limit)
                            ->get()
                            ->map(function ($cartype) use ($lang_id) {
                                $cartype->car_count = VehicleInfo::where('type_id', $cartype->id)
                                    ->where('language_id', $lang_id)
                                    ->count();

                                $cartype->image_url = $cartype->icon
                                    ? asset('storage/' . ltrim($cartype->icon, '/'))
                                    : asset('images/default.png');

                                return $cartype;
                            });

                        // Set values only if it's confirmed to be an array
                        $section['section_type'] = 'car_type';
                        $section['design'] = 'car_type_one';
                        $section['section_content'] = $cartypes;
                    }
                }

                //Testimonial
                if (is_array($section) && ($section['status'] ?? 0) == 1) {
                    $content = $section['section_content'] ?? '';

                    if (is_string($content) && strpos($content, '[testimonial') !== false) {
                        preg_match('/limit=(\d+)\s+viewall=(yes|no)/', $content, $matches);
                        $limit = isset($matches[1]) ? (int)$matches[1] : 10;
                        $viewAll = $matches[2] ?? 'no';

                        $testimonials = DB::table('testimonials')->select('customer_name', 'image', 'ratings', 'review', 'location')
                            ->limit($limit)
                            ->where('language_id', $lang_id)
                            ->where('status', 1)
                            ->whereNull('deleted_at')
                            ->get();

                        foreach ($testimonials as &$testimonial) {
                            $testimonial->image = asset('storage/' . $testimonial->image);
                        }

                        $section['section_type'] = 'testimonial';
                        $section['design'] = 'testimonial_one';
                        $section['section_content'] = $testimonials;
                    }
                }

                // AD Card Section
                if (is_array($section) && ($section['status'] ?? 0) == 1) {
                    $content = $section['section_content'] ?? '';

                    if (is_string($content) && strpos($content, '[ad_card') !== false) {
                        preg_match('/limit=(\d+)\s+viewall=(yes|no)\s+order=(asc|desc)/', $content, $matches);

                        $limit = isset($matches[1]) ? (int)$matches[1] : 10;
                        $viewAll = $matches[2] ?? 'no';
                        $order = $matches[3] ?? 'asc';
                        $how_it_works = DB::table('general_settings')
                            ->select('key', 'value')
                            ->where(['group_id' => 15])
                            ->orderBy('created_at', $order)
                            ->limit($limit)
                            ->get()
                            ->map(function ($item) {
                                $value = json_decode($item->value, true);

                                if (!is_array($value)) {
                                    $value = ['text' => $item->value];
                                }

                                // Assign default image initially
                                $value['image'] = asset('frontend/assets/img/placeholder-app-car.jpg');

                                $item->value = $value;
                                return $item;
                            });

                        // Now fetch image block
                        $imageBlocks = DB::table('sections')
                            ->join('section_datas', function ($join) use ($lang_id) {
                                $join->on('sections.id', '=', 'section_datas.section_id')
                                    ->where('section_datas.language_id', '=', $lang_id);
                            })
                            ->select('sections.id', 'section_datas.datas')
                            ->where('sections.name', 'Ad Card one')
                            ->orderBy('sections.id', $order)
                            ->limit($limit)
                            ->get();

                        // Decode image blocks
                        $imageList = [];

                        foreach ($imageBlocks as $block) {
                            $data = json_decode($block->datas, true);
                            if (is_array($data)) {
                                foreach ($data as $key => $val) {
                                    if (str_starts_with($key, 'thumbnail_image_') && !empty($val)) {
                                        $imageUrl = Storage::disk('public')->exists(ltrim($val, '/'))
                                            ? asset('storage/' . ltrim($val, '/'))
                                            : asset('frontend/assets/img/placeholder-app-car.jpg');
                                        $imageList[] = $imageUrl;
                                    }
                                }
                            }
                        }

                        // Inject images into $how_it_works items
                        foreach ($how_it_works as $index => $item) {
                            if (isset($imageList[$index])) {
                                $item->value['image'] = $imageList[$index];
                            }
                        }

                        $section['section_type'] = 'ad_card_section';
                        $section['design'] = 'ad_card_section_one';
                        $section['section_content'] = $how_it_works;
                    }
                }

                // Why Choose us Section
                if (is_array($section) && ($section['status'] ?? 0) == 1) {
                    $content = $section['section_content'] ?? '';

                    if (is_string($content) && strpos($content, '[why_us') !== false) {
                        preg_match('/limit=(\d+)\s+viewall=(yes|no)\s+order=(asc|desc)/', $content, $matches);
                        $limit = isset($matches[1]) ? (int)$matches[1] : 10;
                        $viewAll = $matches[2] ?? 'no';
                        $order = $matches[3] ?? 'asc';

                        $whyus = DB::table('sections')
                            ->join('section_datas', function ($join) use ($lang_id) {
                                $join->on('sections.id', '=', 'section_datas.section_id')
                                    ->where('section_datas.language_id', '=', $lang_id);
                            })
                            ->select('sections.id', 'section_datas.datas')
                            ->where('sections.name', 'Why Choose Us')
                            ->orderBy('sections.id', $order)
                            ->limit($limit)
                            ->get();

                        if ($whyus->isNotEmpty()) {
                            $first = $whyus[0];
                            $data = json_decode($first->datas, true);

                            $items = [];

                            foreach ([1, 2, 3] as $i) {
                                $label = $data["why_label_$i"] ?? '';
                                $description = $data["why_dis_$i"] ?? '';
                                $icon = $data["why_icon_$i"] ?? null;

                                // Fallbacks for missing icons using asset image paths
                                if (empty($icon)) {
                                    if ($i === 1) {
                                        $icon = asset('/frontend/assets/img/icons/bx-selection.svg');
                                    } elseif ($i === 2) {
                                        $icon = asset('/frontend/assets/img/icons/bx-crown.svg');
                                    } elseif ($i === 3) {
                                        $icon = asset('/frontend/assets/img/icons/bx-user-check.svg');
                                    }
                                } else {
                                    $icon = asset('storage/' . $icon);
                                }

                                // Only add if any of the fields are filled
                                if (!empty($label) || !empty($description) || !empty($icon)) {
                                    $items[] = [
                                        'why_label' => $label,
                                        'why_dis'   => $description,
                                        'why_icon'  => $icon,
                                    ];
                                }
                            }

                            $section['section_type'] = 'why_us_section';
                            $section['type'] = 'why_us_section';
                            $section['design'] = 'why_us_one';
                            $section['section_content'] = [
                                "items" => $items,
                            ];
                        }
                    }
                }

                // Exclusive Bike Section
                if (is_array($section) && ($section['status'] ?? 0) == 1) {
                    $content = $section['section_content'] ?? '';

                    if (is_string($content) && strpos($content, '[exclusive_bike') !== false) {
                        preg_match('/limit=(\d+)\s+viewall=(yes|no)\s+order=(asc|desc)/', $content, $matches);
                        $limit = isset($matches[1]) ? (int)$matches[1] : 10;
                        $viewAll = $matches[2] ?? 'no';
                        $order = $matches[3] ?? 'asc';

                        $whyus = DB::table('sections')
                            ->join('section_datas', function ($join) use ($lang_id) {
                                $join->on('sections.id', '=', 'section_datas.section_id')
                                    ->where('section_datas.language_id', '=', $lang_id);
                            })
                            ->select('sections.id', 'section_datas.datas')
                            ->where('sections.name', 'Exclusive Bike')
                            ->orderBy('sections.id', $order)
                            ->limit($limit)
                            ->get();

                        if ($whyus->isNotEmpty()) {
                            $first = $whyus[0];
                            $data = json_decode($first->datas, true);

                            $items = [];
                            asset('frontend/assets/img/placeholder-app-car.jpg');

                            $previewImage = $defaultImage;
                            if (!empty($data['thumbnail_image_bike_exclusive'])) {
                                $relativePath = ltrim($data['thumbnail_image_bike_exclusive'], '/');
                                $fullPath = storage_path('app/public/' . $relativePath);
                                if (file_exists($fullPath)) {
                                    $previewImage = asset('storage/' . $relativePath);
                                }
                            }

                            foreach ([1, 2, 3, 4] as $i) {
                                $label = $data["bike_label_$i"] ?? '';
                                $description = $data["bike_dis_$i"] ?? '';

                                if ($label || $description) {
                                    $items[] = [
                                        'bike_label' => $label,
                                        'bike_dis'   => $description,
                                    ];
                                }
                            }

                            $section['section_type'] = 'exclusive_bike';
                            $section['type'] = 'exclusive_bike';
                            $section['design'] = 'exclusive_bike';
                            $section['section_content'] = [
                                "bike_icon" => $previewImage,
                                "items"     => $items,
                            ];
                        }
                    }
                }

                // AD Card Seasonal Section
                if (is_array($section) && ($section['status'] ?? 0) == 1) {
                    $content = $section['section_content'] ?? '';

                    if (is_string($content) && strpos($content, '[seasonal_card') !== false) {
                        preg_match('/limit=(\d+)\s+viewall=(yes|no)\s+order=(asc|desc)/', $content, $matches);
                        $limit = isset($matches[1]) ? (int)$matches[1] : 10;
                        $order = $matches[3] ?? 'asc';

                        $experiences = DB::table('sections')
                            ->join('section_datas', function ($join) use ($lang_id) {
                                $join->on('sections.id', '=', 'section_datas.section_id')
                                    ->where('section_datas.language_id', '=', $lang_id);
                            })
                            ->select('sections.id', 'section_datas.datas')
                            ->where('sections.name', 'Theme Four AD Card')
                            ->orderBy('sections.id', $order)
                            ->limit($limit)
                            ->get();

                        if ($experiences->isNotEmpty()) {
                            $items = [];

                            foreach ($experiences as $experience) {
                                $data = json_decode($experience->datas, true);

                                if (is_array($data)) {
                                    asset('frontend/assets/img/placeholder-app-car.jpg');

                                    foreach ($data as $key => $value) {
                                        if (str_starts_with($key, 'thumbnail_image_')) {
                                            $imagePath = ltrim($value, '/');
                                            $fullPath = storage_path('app/public/' . $imagePath);

                                            if (!empty($value) && file_exists($fullPath)) {
                                                $data[$key] = asset('storage/' . $imagePath);
                                            } else {
                                                $data[$key] = $defaultImage;
                                            }
                                        }
                                    }

                                    $items[] = [
                                        'data' => $data,
                                    ];
                                }
                            }

                            $section['section_type'] = 'yacht_seasonal';
                            $section['type'] = 'yacht_seasonal';
                            $section['design'] = 'yacht_seasonal';
                            $section['section_content'] = $items; // Directly assign items
                        }
                    }
                }

                // AD Card Offer Section
                if (is_array($section) && ($section['status'] ?? 0) == 1) {
                    $content = $section['section_content'] ?? '';

                    if (is_string($content) && strpos($content, '[offer_card') !== false) {
                        preg_match('/limit=(\d+)\s+viewall=(yes|no)\s+order=(asc|desc)/', $content, $matches);
                        $limit = isset($matches[1]) ? (int)$matches[1] : 10;
                        $order = $matches[3] ?? 'asc';

                        $experiences = DB::table('sections')
                            ->join('section_datas', function ($join) use ($lang_id) {
                                $join->on('sections.id', '=', 'section_datas.section_id')
                                    ->where('section_datas.language_id', '=', $lang_id);
                            })
                            ->select('sections.id', 'section_datas.datas')
                            ->where('sections.name', 'Offer Card')
                            ->orderBy('sections.id', $order)
                            ->limit($limit)
                            ->get();

                        if ($experiences->isNotEmpty()) {
                            $items = [];

                            foreach ($experiences as $experience) {
                                $data = json_decode($experience->datas, true);

                                if (is_array($data)) {
                                    asset('frontend/assets/img/placeholder-app-car.jpg');

                                    foreach ($data as $key => $value) {
                                        if (str_starts_with($key, 'thumbnail_image_')) {
                                            $imagePath = ltrim($value, '/');
                                            $fullPath = storage_path('app/public/' . $imagePath);

                                            if (!empty($value) && file_exists($fullPath)) {
                                                $data[$key] = asset('storage/' . $imagePath);
                                            } else {
                                                $data[$key] = $defaultImage;
                                            }
                                        }
                                    }

                                    $items[] = [
                                        'data' => $data,
                                    ];
                                }
                            }

                            $section['section_type'] = 'yacht_offer';
                            $section['type'] = 'yacht_offer';
                            $section['design'] = 'yacht_offer';
                            $section['section_content'] = $items; // Directly assign items
                        }
                    }
                }

                // Benefits Of Renting Yacht
                if (is_array($section) && ($section['status'] ?? 0) == 1) {
                    $content = $section['section_content'] ?? '';

                    if (is_string($content) && strpos($content, '[yart_benefit') !== false) {
                        preg_match('/limit=(\d+)\s+viewall=(yes|no)\s+order=(asc|desc)/', $content, $matches);
                        $limit = isset($matches[1]) ? (int)$matches[1] : 10;
                        $viewAll = $matches[2] ?? 'no';
                        $order = $matches[3] ?? 'asc';

                        $benefits = DB::table('sections')
                            ->join('section_datas', function ($join) use ($lang_id) {
                                $join->on('sections.id', '=', 'section_datas.section_id')
                                    ->where('section_datas.language_id', '=', $lang_id);
                            })
                            ->select('sections.id', 'section_datas.datas')
                            ->where('sections.name', 'Benefits Of Yacht')
                            ->orderBy('sections.id', $order)
                            ->limit($limit)
                            ->get();

                        if ($benefits->isNotEmpty()) {
                            $first = $benefits[0];
                            $data = json_decode($first->datas, true);

                            $items = [];
                            $fallbacks = [
                                1 => '/frontend/assets/img/icons/bx-selection.svg',
                                2 => '/frontend/assets/img/icons/bx-crown.svg',
                                3 => '/frontend/assets/img/icons/bx-user-check.svg',
                                4 => '/frontend/assets/img/icons/bx-selection.svg',
                                5 => '/frontend/assets/img/icons/bx-selection.svg',
                                6 => '/frontend/assets/img/icons/bx-heart.svg',
                            ];

                            foreach (range(1, 6) as $i) {
                                $label = $data["label_boat_benefits_$i"] ?? '';
                                $description = $data["description_boat_benefits_$i"] ?? '';
                                $thumbnail = $data["thumbnail_image_boat_benefits_$i"] ?? null;

                                // Default to fallback icon
                                $image = asset($fallbacks[$i]);

                                if (!empty($thumbnail)) {
                                    $relativePath = ltrim($thumbnail, '/');
                                    $fullPath = storage_path('app/public/' . $relativePath);

                                    if (file_exists($fullPath)) {
                                        $image = asset('storage/' . $relativePath);
                                    }
                                }

                                if ($label || $description || $thumbnail) {
                                    $items[] = [
                                        'label'       => $label,
                                        'description' => $description,
                                        'image'       => $image,
                                    ];
                                }
                            }

                            asset('frontend/assets/img/placeholder-app-car.jpg');

                            $mainImage = $defaultImage;

                            if (!empty($data['thumbnail_image_boat_benefits_main'])) {
                                $relativePath = ltrim($data['thumbnail_image_boat_benefits_main'], '/');
                                $fullPath = storage_path('app/public/' . $relativePath);

                                if (file_exists($fullPath)) {
                                    $mainImage = asset('storage/' . $relativePath);
                                }
                            }

                            $section['section_type'] = 'yacht_benefits';
                            $section['type'] = 'yacht_benefits';
                            $section['design'] = 'yacht_benefits_six';
                            $section['section_content'] = [
                                'main_image' => $mainImage,
                                'items'      => $items,
                                'view_all'   => $viewAll ?? null, // Optional: only if $viewAll is defined
                            ];
                        }
                    }
                }

                // Yacht Experience
                if (is_array($section) && ($section['status'] ?? 0) == 1) {
                    $content = $section['section_content'] ?? '';

                    if (is_string($content) && strpos($content, '[experience') !== false) {
                        preg_match('/limit=(\d+)\s+viewall=(yes|no)\s+order=(asc|desc)/', $content, $matches);
                        $limit = isset($matches[1]) ? (int)$matches[1] : 10;
                        $order = $matches[3] ?? 'asc';

                        $experiences = DB::table('sections')
                            ->join('section_datas', function ($join) use ($lang_id) {
                                $join->on('sections.id', '=', 'section_datas.section_id')
                                    ->where('section_datas.language_id', '=', $lang_id);
                            })
                            ->select('sections.id', 'section_datas.datas')
                            ->where('sections.name', 'Yacht Experience')
                            ->orderBy('sections.id', $order)
                            ->limit($limit)
                            ->get();

                        if ($experiences->isNotEmpty()) {
                            $items = [];

                            foreach ($experiences as $experience) {
                                $data = json_decode($experience->datas, true);

                                if (is_array($data)) {
                                    // Normalize image URLs
                                    asset('frontend/assets/img/placeholder-app-car.jpg');

                                    foreach ($data as $key => $value) {
                                        if (str_starts_with($key, 'thumbnail_image_')) {
                                            $imagePath = ltrim($value, '/');
                                            $fullPath = storage_path('app/public/' . $imagePath);

                                            if (!empty($value) && file_exists($fullPath)) {
                                                $data[$key] = asset('storage/' . $imagePath);
                                            } else {
                                                $data[$key] = $defaultImage;
                                            }
                                        }
                                    }

                                    $items[] = [
                                        'id'   => $experience->id,
                                        'data' => $data,
                                    ];
                                }
                            }

                            $section['section_type'] = 'yacht_experience';
                            $section['type'] = 'yacht_experience';
                            $section['design'] = 'yacht_experience_six';
                            $section['section_content'] = $items; // Directly assign items
                        }
                    }
                }

                //Blog Section
                if (is_array($section) && ($section['status'] ?? 0) == 1) {
                    $content = $section['section_content'] ?? '';

                    if (is_string($content) && strpos($content, '[blog') !== false) {
                        preg_match('/type=([a-zA-Z]+)\s+limit=(\d+)\s+viewall=(yes|no)/', $content, $matches);
                        $type = $matches[1] ?? 'all';
                        $limit = $matches[2] ?? 10;
                        

                        $blogss = DB::table('blog_posts')
                            ->select('id', 'title', 'image', 'slug', 'category', 'description', 'updated_at')
                            ->when($type === 'all', fn ($query) => $query)
                            ->limit((int) $limit)
                            ->where('language_id', $lang_id)
                            ->where('status', 1)
                            ->whereNull('deleted_at')
                            ->orderByDesc('id')
                            ->get();

                        $blogs = [];
                        $appAdmin = User::where('user_type', 1)->first();
                        foreach ($blogss as $blog) {
                            $category = BlogCategory::find($blog->category);
                            $blogs[] = [
                                'id'          => $blog->id,
                                'title'       => $blog->title,
                                'slug'        => $blog->slug ?? Str::slug($blog->title),
                                'image'       => uploadedAsset($blog->image),
                                'category'    => $category?->name ?? '',
                                'description' => $blog->description,
                                'updated_at'  => formatDateTime($blog->updated_at),
                                'author'      => [
                                    'name'   => getCurrentUserFullName($appAdmin->id),
                                    'avatar' => uploadedAsset($appAdmin->userDetails->profile_image, 'profile'),
                                ],
                            ];
                        }

                        $section['section_type'] = 'blog';
                        $section['design'] = 'blog_one';
                        $section['section_content'] = $blogs;
                    }
                }

                // Search Section
                if (is_array($section) && ($section['status'] ?? 0) == 1) {
                    $content = $section['section_content'] ?? '';

                    if (is_string($content) && strpos($content, '[search') !== false) {
                        preg_match('/limit=(\d+)\s+viewall=(yes|no)\s+order=(asc|desc)/', $content, $matches);
                        $limit = $matches[1] ?? 10;
                        $viewAll = $matches[2] ?? 'no';
                        $order = $matches[3] ?? 'asc';

                        // Provide static content
                        $section['section_type'] = 'search_section';
                        $section['type'] = 'search_section';
                        $section['design'] = 'search_one';
                        $section['section_content'] = [
                            "title"       => "Search Section",
                            "description" => "Find the best vehicles and services easily."
                        ];
                    }
                }

                // Marquee Section
                if (is_array($section) && ($section['status'] ?? 0) == 1) {
                    $content = $section['section_content'] ?? '';

                    if (is_string($content) && strpos($content, '[marquee') !== false) {
                        preg_match('/limit=(\d+)\s+viewall=(yes|no)\s+order=(asc|desc)/', $content, $matches);
                        $limit = $matches[1] ?? 10;
                        $viewAll = $matches[2] ?? 'no';
                        $order = $matches[3] ?? 'asc';

                        $page = Page::select("keywords")->where("slug", $slug)->where("language_id", $lang_id)->first();

                        if (!$page) {
                            $basePage = Page::select("keywords", "id")->where('slug', $slug)->whereNull('parent_id')->first();

                            if ($basePage) {
                                $page = Page::select("keywords")->where('parent_id', $basePage->id)->where('language_id', $lang_id)->first();
                            }
                        }

                        $keywordsJsonArray = [];

                        if ($page && isset($page->keywords)) {
                            $keywords = array_map('trim', explode(',', $page->keywords));
                            foreach ($keywords as $keyword) {
                                $keywordsJsonArray[] = ['text' => $keyword];
                            }
                        }

                        $section['section_type'] = 'marquee_section';
                        $section['type'] = 'marquee_section';
                        $section['design'] = 'marquee_one';
                        $section['section_content'] = $keywordsJsonArray;
                    }
                }

                // Bike Experience
                if (is_array($section) && ($section['status'] ?? 0) == 1) {
                    $content = $section['section_content'] ?? '';

                    if (is_string($content) && strpos($content, '[second_card') !== false) {
                        preg_match('/limit=(\d+)\s+viewall=(yes|no)\s+order=(asc|desc)/', $content, $matches);
                        $limit = isset($matches[1]) ? (int)$matches[1] : 10;
                        $order = $matches[3] ?? 'asc';

                        $experiences = DB::table('sections')
                            ->join('section_datas', function ($join) use ($lang_id) {
                                $join->on('sections.id', '=', 'section_datas.section_id')
                                    ->where('section_datas.language_id', '=', $lang_id);
                            })
                            ->select('sections.id', 'section_datas.datas')
                            ->where('sections.name', 'Ad Card Two')
                            ->orderBy('sections.id', $order)
                            ->limit($limit)
                            ->get();

                        if ($experiences->isNotEmpty()) {
                            $items = [];

                            foreach ($experiences as $experience) {
                                $data = json_decode($experience->datas, true);

                                if (is_array($data)) {
                                    foreach ($data as $key => $value) {
                                        if (str_starts_with($key, 'thumbnail_image_') && !empty($value)) {
                                            // Check if file exists in storage
                                            if (Storage::disk('public')->exists(ltrim($value, '/'))) {
                                                $data[$key] = asset('storage/' . ltrim($value, '/'));
                                            } else {
                                                // Fallback default image path
                                                $data[$key] = asset('frontend/assets/img/placeholder-app-car.jpg');
                                            }
                                        }
                                    }

                                    $items[] = [
                                        'data' => $data,
                                    ];
                                }
                            }

                            $section['section_type'] = 'bike_experience';
                            $section['type'] = 'bike_experience';
                            $section['design'] = 'bike_experience_six';
                            $section['section_content'] = $items; // Directly assign items
                        }
                    }
                }

                // Bike Experience
                if (is_array($section) && ($section['status'] ?? 0) == 1) {
                    $content = $section['section_content'] ?? '';

                    if (is_string($content) && strpos($content, '[exclusive_yacht ') !== false) {
                        preg_match('/limit=(\d+)\s+viewall=(yes|no)\s+order=(asc|desc)/', $content, $matches);
                        $limit = isset($matches[1]) ? (int)$matches[1] : 10;
                        $order = $matches[3] ?? 'asc';

                        $experiences = DB::table('sections')
                            ->join('section_datas', function ($join) use ($lang_id) {
                                $join->on('sections.id', '=', 'section_datas.section_id')
                                    ->where('section_datas.language_id', '=', $lang_id);
                            })
                            ->select('sections.id', 'section_datas.datas')
                            ->where('sections.name', 'Exclusive Yacht')
                            ->orderBy('sections.id', $order)
                            ->limit($limit)
                            ->get();

                        if ($experiences->isNotEmpty()) {
                            $items = [];

                            $query = VehicleInfo::with([
                                'carType:id,name',
                                'brand:id,brand_name',
                                'category:id,name',
                                'mainLocation:id,name',
                                'color:id,name,value',
                                'fuel_type:id,fuel_type',
                                'transmission:id,name',
                            ])->where('language_id', $lang_id);

                            $vehicles = $query->where('type', 'boat')->take(4)->get();

                            $data = $vehicles->map(function ($vehicle) {
                                $vehicleImages = VehicleMeta::where('vehicle_id', $vehicle->id)
                                    ->where('key', 'vehicle_image')
                                    ->first();

                                $vehiclePrices = $vehicle->vehicle_price ? json_decode($vehicle->vehicle_price, true) : [];
                                $filteredPrices = [];

                                if (!empty($vehiclePrices)) {
                                    foreach ($vehiclePrices as $price) {
                                        $filteredPrice = array_filter($price, fn ($value) => $value > 0);
                                        if (!empty($filteredPrice)) {
                                            $filteredPrices[] = $filteredPrice;
                                        }
                                    }
                                }

                                $multipleImages = $vehicleImages ? json_decode($vehicleImages->value, true) : [];

                                if (!empty($vehicle->vehicle_image)) {
                                    array_unshift($multipleImages, $vehicle->vehicle_image);
                                }

                                $multipleImages = array_map(function ($img) {
                                    $img = '/' . ltrim($img, '/');
                                    return url('storage' . $img);
                                }, $multipleImages);

                                $auth = current_user();
                                $authId = $auth?->id;

                                $wishlistExists = $authId
                                    ? Wishlist::where("user_id", $authId)->where("vehicle_id", $vehicle->id)->exists()
                                    : false;

                                $currencySetting = GeneralSetting::where("key", "currency_symbol")->first();
                                $currency = $currencySetting && $currencySetting->value
                                    ? Currency::find($currencySetting->value)
                                    : null;

                                $currencySymbol = $currency->symbol ?? "$";

                                $rating = Review::where("vehicle_id", $vehicle->id)->value("average_ratings") ?? 0;

                                $defaultAvatar = asset('/backend/assets/img/default-profile.png');
                                $profileImagePath = optional($vehicle->owner->userDetails)->profile_image;
                                $avatarImage = $defaultAvatar;

                                if ($profileImagePath) {
                                    $fullImagePath = storage_path('app/public/' . $profileImagePath);
                                    if (file_exists($fullImagePath)) {
                                        $avatarImage = url('/storage/' . $profileImagePath);
                                    }
                                }

                                return [
                                    'id'                      => $vehicle->id,
                                    'name'                    => $vehicle->name,
                                    'slug'                    => $vehicle->slug,
                                    'vehicle_image'           => url('/storage/' . $vehicle->vehicle_image),
                                    'multiple_vehicle_images' => $multipleImages,
                                    'has_multiple_image'      => count($multipleImages) > 1,
                                    'avatar_image'            => $avatarImage,
                                    'brand_id'                => $vehicle->brand_id ?? null,
                                    'brand'                   => $vehicle->brand->brand_name ?? null,
                                    'car_type'                => $vehicle->carType->name ?? null,
                                    'category'                => $vehicle->category->name ?? null,
                                    'tube_type'               => Arr::random(['Tube', 'Tubeless']),
                                    'break_type'              => Arr::random(['Drum', 'Disc']),
                                    'location'                => $vehicle->mainLocation->name ?? null,
                                    'color'                   => $vehicle->color->name ?? null,
                                    'fuel_type'               => $vehicle->fuel_type->fuel_type ?? null,
                                    'transmission'            => $vehicle->transmission->name ?? null,
                                    'year'                    => $vehicle->year,
                                    'mileage'                 => $vehicle->mileage,
                                    'odometer'                => $vehicle->odometer,
                                    'rating'                  => $rating,
                                    'total_review'            => Review::where("vehicle_id", $vehicle->id)->count(),
                                    'currency'                => $currencySymbol,
                                    'wishlist'                => $wishlistExists,
                                    'passenger_capacity'      => $vehicle->passenger_capacity,
                                    'num_seats'               => $vehicle->num_seats,
                                    'num_doors'               => $vehicle->num_doors,
                                    'num_airbags'             => $vehicle->num_airbags,
                                    'vehicle_video'           => $vehicle->vehicle_video,
                                    'features'                => $vehicle->features,
                                    'price'                   => !empty($filteredPrices) ? $filteredPrices : null,
                                    'is_featured'             => $vehicle->popular,
                                    'is_top_rated'            => $vehicle->recommended,
                                    'seo_title'               => $vehicle->vehicle_metatitle,
                                    'seo_key'                 => $vehicle->vehicle_metakeywords,
                                    'seo_description'         => $vehicle->vehicle_metadesc,
                                    'created_at'              => $vehicle->created_at,
                                ];
                            });

                            foreach ($experiences as $experience) {
                                $experienceData = json_decode($experience->datas, true);

                                if (is_array($experienceData)) {
                                    asset('frontend/assets/img/placeholder-app-car.jpg');

                                    foreach ($data as $key => $value) {
                                        if (str_starts_with($key, 'thumbnail_image_')) {
                                            $imagePath = ltrim($value, '/');
                                            $fullPath = storage_path('app/public/' . $imagePath);

                                            if (!empty($value) && file_exists($fullPath)) {
                                                $data[$key] = asset('storage/' . $imagePath);
                                            } else {
                                                $data[$key] = $defaultImage;
                                            }
                                        }
                                    }

                                    $items[] = [
                                        'data'     => $experienceData,
                                        'vehicles' => $data->toArray(),
                                    ];
                                }
                            }

                            $section['section_type'] = 'bike_exclusive';
                            $section['type'] = 'bike_exclusive';
                            $section['design'] = 'bike_exclusive_six';
                            $section['section_content'] = $items; // Directly assign items
                        }
                    }
                }

                // All Category section
                if (
                    is_array($section) &&
                    ($section['status'] ?? 0) == 1 &&
                    isset($section['section_content']) &&
                    is_string($section['section_content']) &&
                    strpos($section['section_content'], '[all_category ') !== false
                ) {
                    preg_match('/limit=(\d+)\s+viewall=(yes|no)\s+order=(asc|desc)/', $section['section_content'], $matches);

                    $limit = $matches[1] ?? 12;
                    $viewAll = $matches[2] ?? 'no';
                    $order = $matches[3] ?? 'asc';

                    $allCategory = Cartype::select('name', 'id')
                        ->limit((int) $limit)
                        ->where('status', 1)
                        ->where('language_id', $lang_id)
                        ->whereNull('deleted_at')
                        ->get()
                        ->map(fn ($cartype) => ['id' => $cartype->id, 'name' => $cartype->name]);

                    $section['section_type'] = 'all_category';
                    $section['design'] = 'category_two';
                    $section['section_content'] = $allCategory;
                }

                // Facts Section
                if (is_array($section) && ($section['status'] ?? 0) == 1) {
                    $content = $section['section_content'] ?? '';

                    if (is_string($content) && strpos($content, '[facts') !== false) {
                        preg_match('/limit=(\d+)\s+viewall=(yes|no)\s+order=(asc|desc)/', $content, $matches);
                        $limit = $matches[1] ?? 10;
                        $viewAll = $matches[2] ?? 'no';
                        $order = $matches[3] ?? 'asc';

                        $userCount = User::count(); // Get total users
                        $vehicleCount = VehicleInfo::count(); // Get total vehicles
                        $locationCount = Location::count(); // Get total locations
                        $totalKm = 1976; // Keeping total_km static

                        // Set facts content dynamically
                        $section['facts_content'] = [
                            ["key" => "happy_customers", "value" => $userCount],
                            ["key" => "vehicle_count", "value" => $vehicleCount],
                            ["key" => "location_count", "value" => $locationCount],
                            ["key" => "total_km", "value" => $totalKm],
                        ];
                        $section['section_type'] = 'facts_section';
                        $section['type'] = 'facts_section';
                        $section['design'] = 'facts_one';
                    }
                }

                if (isset($section['section_content']) && is_string($section['section_content'])) {
                    if (preg_match('/\[[^\]]+\]/', $section['section_content']) === 0) {
                        $section['section_content'] = $section['section_content'];
                        $section['section_type'] = 'multiple_section';
                    }
                }
            }
        }
        $languageCode = app()->getLocale();
        $language_id = getLanguageId($languageCode);
        $cookieSettings = GeneralSetting::where('group_id', 7)->where('language_id', $language_id)->pluck('value', 'key');
        $cookieResponse = [
            'content'           => $cookieSettings['cookiesContentText_' . $language_id] ?? '',
            'position'          => $cookieSettings['cookiesPosition_' . $language_id] ?? '',
            'agree_btn_text'    => $cookieSettings['agreeButtonText_' . $language_id] ?? '',
            'decline_btn_text'  => $cookieSettings['declineButtonText_' . $language_id] ?? '',
            'show_decline_btn'  => $cookieSettings['showDeclineButton_' . $language_id] ?? '',
            'cookies_page_link' => $cookieSettings['cookiesPageLink_' . $language_id] ?? '',
        ];
        $categoryId = getCategoryId();
        $vehicleTypes = Cartype::select('name', 'id')->where('language_id', $language_id)->where('category_id', $categoryId)->where('status', 1)->get();
        $vehicleModels = CarModel::select('model_name as name', 'id')->where('language_id', $language_id)
            ->whereHas('brand', function ($query) use ($categoryId) {
                $query->where('category_id', $categoryId);
            })
            ->where('status', 1)->get();
        $locations = Location::select('name', 'id')->where('status', 1)->where('language_id', $language_id)->get();
        $totalReviews = Review::count();
        $page = Page::where('slug', $slug)->where('theme_id', $themeId)->where('language_id', $lang_id)->first();
        if (!$page) {
            $basePage = Page::where('slug', $slug)->whereNull('parent_id')->first();

            if ($basePage) {
                $page = Page::where('parent_id', $basePage->id)->where('language_id', $lang_id)->where('theme_id', $themeId)->first();
            }
        }
        if ($page) {
            $data = [
                'page_title'       => $page->page_title,
                'slug'             => $page->slug,
                'currency'         => getDefaultCurrencySymbol(),
                'language_id'      => $page->language_id,
                'content_sections' => $pageContentSections,
                'seo_tag'          => $page->seo_tag,
                'seo_title'        => $page->seo_title,
                'seo_description'  => $page->seo_description,
                'status'           => $page->status,
                'cookie_settings'  => $cookieResponse,
                'vehicle_types'    => $vehicleTypes,
                'vehicle_models'   => $vehicleModels,
                'locations'        => $locations,
                'total_reviews'    => $totalReviews
            ];

            $seo_title = $page->seo_title;
            $seo_description = $page->seo_description;
            $og_title = $page->og_title;
            $og_description = $page->og_description;
            $meta_keywords = $page->keywords;

            $vehicleBrand = Brand::select("id", "brand_name", "brand_image", "brand_icon")
                ->where("language_id", $language_id)
                ->where("category_id", $categoryId)
                ->where("status", 1)
                ->get();

            $content_sections = collect((array) $data['content_sections']);
            if (request()->has('is_mobile') && request()->get('is_mobile') === "yes") {
                return response()->json(['code' => "200", 'message' => __('Page details retrieved successfully.'), 'data' => $data], 200);
            } else {
                $defaultTheme = GeneralSetting::where('key', 'default_theme')->first();
                $theme = $defaultTheme ? $defaultTheme->value : 1;
                $viewPath = 'frontend.home.home_' . $theme;
                if (!view()->exists($viewPath)) {
                    $viewPath = 'frontend.home.home_1';
                }

                return view($viewPath, compact('data', 'content_sections', 'vehicleBrand', 'seo_title', 'seo_description', 'og_title', 'og_description', 'meta_keywords'));
            }
        } else {
            return response()->json(['code' => '404', 'message' => __('Page not found.')], 404);
        }
    }

    public function getPage(string $slug): View
    {
        $defaultLang = 'en';
        $language = TranslationLanguage::where('code', $defaultLang)->first();

        if (!$language) {
            abort(404, 'Default language not found');
        }

        $authUser = current_user();

        $lang_id = null;

        if ($authUser && !empty($authUser->language_id)) {
            $lang_id = $authUser->language_id;
        } elseif (App::getLocale()) {
            $currentLocale = App::getLocale();
            $language = TranslationLanguage::where('code', $currentLocale)->first();
            $lang_id = $language->id ?? null;
        } else {
            $defaultLang = Language::select("language_id")->where("default", 1)->first();
            $lang_id = $defaultLang->language_id ?? 1;
        }

        $fallbackSlug = 'pages/' . ltrim($slug, '/');
        $page = Page::where('slug', $fallbackSlug)
            ->where('language_id', $lang_id)
            ->first();

        if (!$page) {
            $fallbackSlug = 'pages/' . ltrim($slug, '/');
            $basePage = Page::where('slug', $fallbackSlug)->whereNull('parent_id')->first();

            if ($basePage) {
                $page = Page::where('parent_id', $basePage->id)->where('language_id', $lang_id)->first();
            }
        }

        $pageContentSections = json_decode($page->page_content ?? '[]', true) ?? [];

        if (empty($pageContentSections) || !collect((array)$pageContentSections)->contains(fn ($section) => $section['status'] == 1)) {
            $pageContentSections = [];
        } else {
            foreach ($pageContentSections as &$section) {
                // Banner One
                if (is_array($section) && ($section['status'] ?? 0) == 1) {
                    $content = $section['section_content'] ?? '';

                    if (is_string($content) && strpos($content, '[banner_one') !== false) {
                        preg_match('/limit=(\d+)\s+viewall=(yes|no)\s+order=(asc|desc)/', $content, $matches);

                        $limit = (int)($matches[1] ?? 10);
                        $viewAll = $matches[2] ?? 'no';
                        $order = $matches[3] ?? 'asc';

                        $banners = DB::table('sections')
                            ->join('section_datas', function ($join) use ($lang_id) {
                                $join->on('sections.id', '=', 'section_datas.section_id')
                                    ->where('section_datas.language_id', '=', $lang_id);
                            })
                            ->select('sections.id', 'section_datas.datas')
                            ->where('sections.name', 'Banner One')
                            ->orderBy('sections.id', $order)
                            ->limit($limit)
                            ->get();

                        foreach ($banners as &$banner) {
                            $decodedData = json_decode($banner->datas, true);
                            $decodedData = is_array($decodedData) ? $decodedData : [];

                            $banner->label = $decodedData['label_one'] ?? null;
                            $banner->line_one = $decodedData['line_one'] ?? null;
                            $banner->line_two = $decodedData['line_two'] ?? null;
                            $banner->description = $decodedData['description_one'] ?? null;

                            $relativePath = 'storage/' . ($decodedData['thumbnail_image_one'] ?? '');
                            $defaultImage = asset('backend/assets/img/car/car-right.png');
                            $thumbnailKey = 'thumbnail_image_one';

                            $banner->thumbnail_image = (
                                isset($decodedData[$thumbnailKey]) &&
                                !empty($decodedData[$thumbnailKey]) &&
                                file_exists(public_path($relativePath))
                            ) ? asset($relativePath) : $defaultImage;

                            unset($banner->datas);
                        }

                        $section['section_type'] = 'banner';
                        $section['type'] = 'banner';
                        $section['design'] = 'banner_one';
                        $section['section_content'] = $banners;
                    }
                }

                // Banner Two
                if ($section['status'] == 1) {
                    if (isset($section['section_content']) && strpos($section['section_content'], '[banner_two') !== false) {
                        preg_match('/limit=(\d+)\s+viewall=(yes|no)\s+order=(asc|desc)/', $section['section_content'], $matches);

                        // Ensure $limit is cast to an integer
                        $limit = (int)($matches[1] ?? 10);  // Explicitly cast to integer
                        $viewAll = $matches[2] ?? 'no';
                        $order = $matches[3] ?? 'asc';

                        // Fetch from sections + section_datas with language-specific data
                        $banners = DB::table('sections')
                            ->join('section_datas', function ($join) use ($lang_id) {
                                $join->on('sections.id', '=', 'section_datas.section_id')
                                    ->where('section_datas.language_id', '=', $lang_id);
                            })
                            ->select('sections.id', 'section_datas.datas')
                            ->where('sections.name', 'Banner Two')
                            ->orderBy('sections.id', $order)
                            ->limit($limit)  // Ensure $limit is an integer
                            ->get();

                        $userCount = User::count();

                        foreach ($banners as &$banner) {
                            $decodedData = json_decode($banner->datas, true);

                            $banner->label = $decodedData['label_two'] ?? null;
                            $banner->description = $decodedData['description_two'] ?? null;

                            $relativePath = 'storage/' . ($decodedData['thumbnail_image_two'] ?? '');
                            $defaultImage = asset('backend/assets/img/car/car-right.png');
                            $thumbnailKey = 'thumbnail_image_two';

                            $banner->thumbnail_image = (
                                isset($decodedData[$thumbnailKey]) &&
                                !empty($decodedData[$thumbnailKey]) &&
                                file_exists(public_path($relativePath))
                            ) ? asset($relativePath) : $defaultImage;

                            $banner->customer_count = $userCount;

                            $banner->customer_images = [
                                asset('backend/assets/img/profiles/avatar-01.jpg'),
                                asset('backend/assets/img/profiles/avatar-02.jpg'),
                                asset('backend/assets/img/profiles/avatar-03.jpg'),
                            ];

                            unset($banner->datas);
                        }

                        $section['section_type'] = 'banner_two';
                        $section['type'] = 'banner_two';
                        $section['design'] = 'banner_two';
                        $section['section_content'] = $banners;
                    }
                }

                // BestVehicle
                if (is_array($section) && ($section['status'] ?? 0) == 1) {
                    $content = $section['section_content'] ?? '';

                    if (is_string($content) && strpos($content, '[bestVehicle') !== false) {
                        preg_match('/limit=(\d+)\s+viewall=(yes|no)\s+order=(asc|desc)/', $content, $matches);

                        $limit = (int)($matches[1] ?? 10);
                        $viewAll = $matches[2] ?? 'no';
                        $order = $matches[3] ?? 'asc';

                        $best_vehicles = DB::table('sections')
                            ->join('section_datas', function ($join) use ($lang_id) {
                                $join->on('sections.id', '=', 'section_datas.section_id')
                                    ->where('section_datas.language_id', '=', $lang_id);
                            })
                            ->select('sections.id', 'section_datas.datas')
                            ->where('sections.name', 'Best Vehicle')
                            ->orderBy('sections.id', $order)
                            ->limit($limit)
                            ->get();

                        foreach ($best_vehicles as &$best_vehicle) {
                            $decodedData = json_decode($best_vehicle->datas, true);
                            $decodedData = is_array($decodedData) ? $decodedData : [];

                            $vehicleId = $decodedData['vehicle_id'] ?? null;
                            $best_vehicle->vehicle_id = $vehicleId;

                            if ($vehicleId) {
                                $vehicle = VehicleInfo::where("language_id", $lang_id)->find($vehicleId);

                                $best_vehicle->vehicle_name = $vehicle->name ?? null;

                                $imagePath = $vehicle->vehicle_image ?? null;
                                $best_vehicle->vehicle_image_url = $imagePath ? asset('storage/' . $imagePath) : null;
                            } else {
                                $best_vehicle->vehicle_name = null;
                                $best_vehicle->vehicle_image_url = null;
                            }

                            $best_vehicle->label_1 = $decodedData['label_1'] ?? null;
                            $best_vehicle->dis_1 = $decodedData['dis_1'] ?? null;

                            $best_vehicle->label_2 = $decodedData['label_2'] ?? null;
                            $best_vehicle->dis_2 = $decodedData['dis_2'] ?? null;

                            $best_vehicle->label_3 = $decodedData['label_3'] ?? null;
                            $best_vehicle->dis_3 = $decodedData['dis_3'] ?? null;

                            $best_vehicle->label_4 = $decodedData['label_4'] ?? null;
                            $best_vehicle->dis_4 = $decodedData['dis_4'] ?? null;

                            $best_vehicle->label_5 = $decodedData['label_5'] ?? null;
                            $best_vehicle->dis_5 = $decodedData['dis_5'] ?? null;

                            $best_vehicle->label_6 = $decodedData['label_6'] ?? null;
                            $best_vehicle->dis_6 = $decodedData['dis_6'] ?? null;

                            unset($best_vehicle->content);
                        }

                        $section['section_type'] = 'best_vehicle';
                        $section['type'] = 'best_vehicle';
                        $section['design'] = 'best_vehicle';
                        $section['section_content'] = $best_vehicles;
                    }
                }

                // Brands section
                if (is_array($section) && ($section['status'] ?? 0) == 1) {
                    $content = $section['section_content'] ?? '';

                    if (is_string($content) && strpos($content, '[brand') !== false) {
                        preg_match('/limit=(\d+)\s+viewall=(yes|no)\s+order=(asc|desc)/', $content, $matches);
                        $limit = (int)($matches[1] ?? 10);
                        $viewAll = $matches[2] ?? 'no';
                        $order = $matches[3] ?? 'asc';

                        $brands = DB::table('brands')
                            ->select('id', 'brand_image', 'brand_icon', 'brand_name', 'status')
                            ->where('language_id', $lang_id)
                            ->where('status', 1)
                            ->whereNull('deleted_at')
                            ->orderBy('created_at', $order)
                            ->limit($limit)
                            ->get()
                            ->map(function ($brand) {
                                $brand->brand_image = asset('storage/' . $brand->brand_image);
                                $brand->brand_icon = asset('storage/' . $brand->brand_icon);
                                return $brand;
                            });

                        $section['section_type'] = 'brands';
                        $section['design'] = 'brand_one';
                        $section['section_content'] = $brands;
                    }
                }

                // Category section
                if (is_array($section) && ($section['status'] ?? 0) == 1) {
                    if (
                        isset($section['section_content']) &&
                        is_string($section['section_content']) &&
                        strpos($section['section_content'], '[category ') !== false
                    ) {
                        preg_match('/limit=(\d+)\s+viewall=(yes|no)\s+order=(asc|desc)/', $section['section_content'], $matches);
                        $limit = $matches[1] ?? 6;
                        $viewAll = $matches[2] ?? 'no';
                        $order = $matches[3] ?? 'asc';
                        $categoryId = getCategoryId();
                        $category = Cartype::select('name', 'icon', 'id')
                            ->limit((int) $limit)
                            ->where('language_id', $lang_id)
                            ->where('category_id', $categoryId)
                            ->where('status', 1)
                            ->whereNull('deleted_at')
                            ->get()
                            ->map(function ($cartype) use ($lang_id) {
                                $cartype->car_count = VehicleInfo::where('type_id', $cartype->id)
                                    ->where('language_id', $lang_id)
                                    ->count();

                                $cartype->image_url = $cartype->icon
                                    ? asset('storage/' . ltrim($cartype->icon, '/'))
                                    : asset('images/default.png');

                                return $cartype;
                            });

                        $section['section_type'] = 'featured_category';
                        $section['design'] = 'category_one';
                        $section['section_content'] = $category;
                    }
                }

                // FAQ Section
                if (is_array($section) && ($section['status'] ?? 0) == 1) {
                    $content = $section['section_content'] ?? '';

                    if (is_string($content) && strpos($content, '[faq') !== false) {
                        preg_match('/limit=(\d+)\s+viewall=(yes|no)\s+order=(asc|desc)/', $content, $matches);
                        $limit = $matches[1] ?? 10;
                        $viewAll = $matches[2] ?? 'no';
                        $order = $matches[3] ?? 'asc';

                        $faqs = DB::table('faqs')
                            ->select('id', 'question', 'answer', 'status')
                            ->where('status', 1)
                            ->whereNull('deleted_at')
                            ->where('language_id', $lang_id)
                            ->orderBy('created_at', $order)
                            ->limit((int) $limit)
                            ->get();

                        $section['section_type'] = 'faq';
                        $section['design'] = 'faq_one';
                        $section['section_content'] = $faqs;
                    }
                }

                // How It Works
                if (is_array($section) && ($section['status'] ?? 0) == 1) {
                    $content = $section['section_content'] ?? '';

                    if (is_string($content) && strpos($content, '[how_it_work') !== false) {
                        preg_match('/limit=(\d+)\s+viewall=(yes|no)\s+order=(asc|desc)/', $content, $matches);
                        $limit = $matches[1] ?? 10;
                        $viewAll = $matches[2] ?? 'no';
                        $order = $matches[3] ?? 'asc';

                        $how_it_works = DB::table('general_settings')->select('id', 'key', 'value', 'group_id')
                            ->where(['group_id' => 10])
                            ->where('language_id', $lang_id)
                            ->orderBy('created_at', $order)
                            ->limit((int) $limit)
                            ->get();

                        $section['section_type'] = 'how_it_works';
                        $section['design'] = 'how_it_works_one';
                        $section['section_content'] = $how_it_works;
                    }
                }

                // Vechile
                if ($section['status'] == 1) {
                    if (isset($section['section_content']) && strpos($section['section_content'], '[vehicle') !== false) {
                        preg_match('/type=([a-zA-Z]+)\s+limit=(\d+)\s+viewall=(yes|no)/', $section['section_content'], $matches);
                        $type = $matches[1] ?? 'all';
                        

                        $query = VehicleInfo::with([
                            'carType:id,name',
                            'brand:id,brand_name',
                            'category:id,name',
                            'mainLocation:id,name',
                            'color:id,name,value',
                            'fuel_type:id,fuel_type',
                            'transmission:id,name',
                        ])->where('language_id', $lang_id);

                        if ($type === 'popular') {
                            $vehicles = $query->where('popular', 1)->get();
                            $section['section_type'] = 'popular_vehicle';
                            $section['design'] = 'vehicle_one';
                        } elseif ($type === 'featured') {
                            $vehicles = $query->where('recommended', 1)->get();
                            $section['section_type'] = 'feature_vehicle';
                            $section['design'] = 'vehicle_two';
                        } else {
                            $vehicles = $query->get();
                            $section['section_type'] = 'al_vehicle';
                            $section['design'] = 'vehicle_three';
                        }

                        $data = $vehicles->map(function ($vehicle) {
                            $vehicleImages = VehicleMeta::where('vehicle_id', $vehicle->id)
                                ->where('key', 'vehicle_image')
                                ->first();

                            $vehiclePrices = $vehicle->vehicle_price ? json_decode($vehicle->vehicle_price, true) : [];
                            $filteredPrices = [];

                            if (!empty($vehiclePrices)) {
                                foreach ($vehiclePrices as $price) {
                                    $filteredPrice = array_filter($price, function ($value) {
                                        return $value > 0; // Only keep values greater than 0
                                    });

                                    if (!empty($filteredPrice)) {
                                        $filteredPrices[] = $filteredPrice;
                                    }
                                }
                            }
                            $multipleImages = $vehicleImages ? json_decode($vehicleImages->value, true) : [];
                            if (!empty($vehicle->vehicle_image)) {
                                array_unshift($multipleImages, $vehicle->vehicle_image);
                            }
                            $multipleImages = array_map(fn ($img) => url('storage/vehicles/' . basename($img)), $multipleImages);

                            /** @var \App\Models\User|null $auth */
                            $auth = current_user();
                            $authId = $auth?->id;

                            $wishlistExists = false;

                            if ($authId) {
                                $wishlistExists = Wishlist::where("user_id", $authId)
                                    ->where("vehicle_id", $vehicle->id)
                                    ->exists();
                            }

                            $currencySetting = GeneralSetting::where("key", "currency_symbol")->first();
                            $currency = null;

                            if ($currencySetting && $currencySetting->value) {
                                $currency = Currency::find($currencySetting->value);
                            }

                            $currencySymbol = $currency->symbol ?? "$";

                            $rating = Review::where("vehicle_id", $vehicle->id)->value("average_ratings") ?? 0;

                            $user = User::where('id', $vehicle->created_by)
                                ->first();

                            $user = User::where('id', $vehicle->created_by)->first();
                            $userDetail = null;
                            $userProfileImg = null;

                            if ($user) {
                                $userDetail = UserDetail::where("user_id", $user->id)->first();

                                $userProfileImg = $userDetail && $userDetail->profile_image
                                    ? url('/storage/' . $userDetail->profile_image)
                                    : null;
                            }

                            return [
                                'id'                      => $vehicle->id,
                                'name'                    => $vehicle->name,
                                'slug'                    => $vehicle->slug,
                                'vehicle_image'           => url('/storage/' . $vehicle->vehicle_image),
                                'multiple_vehicle_images' => $multipleImages,
                                'has_multiple_image'      => count($multipleImages) > 1,
                                'avatar_image'            => $userProfileImg ?? null,
                                'brand_id'                => $vehicle->brand_id ?? null,
                                'brand'                   => $vehicle->brand->brand_name ?? null,
                                'car_type'                => $vehicle->carType->name ?? null,
                                'category'                => $vehicle->category->name ?? null,
                                'location'                => $vehicle->mainLocation->name ?? null,
                                'color'                   => $vehicle->color->name ?? null,
                                'fuel_type'               => $vehicle->fuel_type->fuel_type ?? null,
                                'transmission'            => $vehicle->transmission->name ?? null,
                                'year'                    => $vehicle->year,
                                'mileage'                 => $vehicle->mileage,
                                'odometer'                => $vehicle->odometer,
                                'rating'                  => $rating,
                                'currency'                => $currencySymbol,
                                'wishlist'                => $wishlistExists,
                                'passenger_capacity'      => $vehicle->passenger_capacity,
                                'num_seats'               => $vehicle->num_seats,
                                'num_doors'               => $vehicle->num_doors,
                                'num_airbags'             => $vehicle->num_airbags,
                                'vehicle_video'           => $vehicle->vehicle_video,
                                'features'                => $vehicle->features,
                                'price'                   => !empty($filteredPrices) ? $filteredPrices : null,
                                'is_featured'             => $vehicle->popular,
                                'is_top_rated'            => $vehicle->recommended,
                                'seo_title'               => $vehicle->vehicle_metatitle,
                                'seo_key'                 => $vehicle->vehicle_metakeywords,
                                'seo_description'         => $vehicle->vehicle_metadesc,
                                'created_at'              => $vehicle->created_at,
                            ];
                        });

                        $section['section_content'] = $data;
                    }
                }

                // Car Type
                if (is_array($section) && ($section['status'] ?? 0) == 1) {
                    $content = $section['section_content'] ?? '';

                    if (is_string($content) && strpos($content, '[car_type ') !== false) {
                        preg_match('/limit=(\d+)\s+viewall=(yes|no)/', $content, $matches);
                        $limit = isset($matches[1]) ? (int)$matches[1] : 10;
                        $viewAll = $matches[2] ?? 'no';
                        $categoryId = getCategoryId();
                        $cartypes = Cartype::select('name', 'icon', 'id')
                            ->where('language_id', $lang_id)
                            ->where('category_id', $categoryId)
                            ->where('status', 1)
                            ->whereNull('deleted_at')
                            ->limit($limit)
                            ->get()
                            ->map(function ($cartype) use ($lang_id) {
                                $cartype->car_count = VehicleInfo::where('type_id', $cartype->id)
                                    ->where('language_id', $lang_id)
                                    ->count();

                                $cartype->image_url = $cartype->icon
                                    ? asset('storage/' . ltrim($cartype->icon, '/'))
                                    : asset('images/default.png');

                                return $cartype;
                            });

                        // Set values only if it's confirmed to be an array
                        $section['section_type'] = 'car_type';
                        $section['design'] = 'car_type_one';
                        $section['section_content'] = $cartypes;
                    }
                }

                //Testimonial
                if (is_array($section) && ($section['status'] ?? 0) == 1) {
                    $content = $section['section_content'] ?? '';

                    if (is_string($content) && strpos($content, '[testimonial') !== false) {
                        preg_match('/limit=(\d+)\s+viewall=(yes|no)/', $content, $matches);
                        $limit = isset($matches[1]) ? (int)$matches[1] : 10;
                        $viewAll = $matches[2] ?? 'no';

                        $testimonials = DB::table('testimonials')->select('customer_name', 'image', 'ratings', 'review', 'location')
                            ->limit($limit)
                            ->where('language_id', $lang_id)
                            ->where('status', 1)
                            ->whereNull('deleted_at')
                            ->get();

                        foreach ($testimonials as &$testimonial) {
                            $testimonial->image = asset('storage/' . $testimonial->image);
                        }

                        $section['section_type'] = 'testimonial';
                        $section['design'] = 'testimonial_one';
                        $section['section_content'] = $testimonials;
                    }
                }

                // AD Card Section
                if (is_array($section) && ($section['status'] ?? 0) == 1) {
                    $content = $section['section_content'] ?? '';

                    if (is_string($content) && strpos($content, '[ad_card') !== false) {
                        preg_match('/limit=(\d+)\s+viewall=(yes|no)\s+order=(asc|desc)/', $content, $matches);

                        $limit = isset($matches[1]) ? (int)$matches[1] : 10;
                        $viewAll = $matches[2] ?? 'no';
                        $order = $matches[3] ?? 'asc';
                        $how_it_works = DB::table('general_settings')
                            ->select('key', 'value')
                            ->where(['group_id' => 15])
                            ->orderBy('created_at', $order)
                            ->limit($limit)
                            ->get()
                            ->map(function ($item) {
                                $value = json_decode($item->value, true);

                                if (!is_array($value)) {
                                    $value = ['text' => $item->value];
                                }

                                // Assign default image initially
                                $value['image'] = asset('frontend/assets/img/placeholder-app-car.jpg');

                                $item->value = $value;
                                return $item;
                            });

                        // Now fetch image block
                        $imageBlocks = DB::table('sections')
                            ->join('section_datas', function ($join) use ($lang_id) {
                                $join->on('sections.id', '=', 'section_datas.section_id')
                                    ->where('section_datas.language_id', '=', $lang_id);
                            })
                            ->select('sections.id', 'section_datas.datas')
                            ->where('sections.name', 'Ad Card one')
                            ->orderBy('sections.id', $order)
                            ->limit($limit)
                            ->get();

                        // Decode image blocks
                        $imageList = [];

                        foreach ($imageBlocks as $block) {
                            $data = json_decode($block->datas, true);
                            if (is_array($data)) {
                                foreach ($data as $key => $val) {
                                    if (str_starts_with($key, 'thumbnail_image_') && !empty($val)) {
                                        $imageUrl = Storage::disk('public')->exists(ltrim($val, '/'))
                                            ? asset('storage/' . ltrim($val, '/'))
                                            : asset('frontend/assets/img/placeholder-app-car.jpg');
                                        $imageList[] = $imageUrl;
                                    }
                                }
                            }
                        }

                        // Inject images into $how_it_works items
                        foreach ($how_it_works as $index => $item) {
                            if (isset($imageList[$index])) {
                                $item->value['image'] = $imageList[$index];
                            }
                        }

                        $section['section_type'] = 'ad_card_section';
                        $section['design'] = 'ad_card_section_one';
                        $section['section_content'] = $how_it_works;
                    }
                }
                // Why Choose us Section
                if (is_array($section) && ($section['status'] ?? 0) == 1) {
                    $content = $section['section_content'] ?? '';

                    if (is_string($content) && strpos($content, '[why_us') !== false) {
                        preg_match('/limit=(\d+)\s+viewall=(yes|no)\s+order=(asc|desc)/', $content, $matches);
                        $limit = isset($matches[1]) ? (int)$matches[1] : 10;
                        $viewAll = $matches[2] ?? 'no';
                        $order = $matches[3] ?? 'asc';

                        $section['section_type'] = 'why_us_section';
                        $section['type'] = 'why_us_section';
                        $section['design'] = 'why_us_one';
                        $section['section_content'] = [
                            "title"       => "Why Choose Us Section",
                            "description" => "Find the best vehicles and services easily."
                        ];
                    }
                }

                //Blog Section
                if (is_array($section) && ($section['status'] ?? 0) == 1) {
                    $content = $section['section_content'] ?? '';

                    if (is_string($content) && strpos($content, '[blog') !== false) {
                        preg_match('/type=([a-zA-Z]+)\s+limit=(\d+)\s+viewall=(yes|no)/', $content, $matches);
                        $type = $matches[1] ?? 'all';
                        $limit = $matches[2] ?? 10;
                        

                        $blogss = DB::table('blog_posts')
                            ->select('id', 'title', 'image', 'slug', 'category', 'description', 'updated_at')
                            ->when($type === 'all', fn ($query) => $query)
                            ->limit((int) $limit)
                            ->where('language_id', $lang_id)
                            ->where('status', 1)
                            ->whereNull('deleted_at')
                            ->orderByDesc('id')
                            ->get();

                        $blogs = [];

                        foreach ($blogss as $blog) {
                            $category = BlogCategory::find($blog->category);
                            $blogs[] = [
                                'id'          => $blog->id,
                                'title'       => $blog->title,
                                'slug'        => $blog->slug ?? Str::slug($blog->title),
                                'image'       => uploadedAsset($blog->image),
                                'category'    => $category?->name ?? '',
                                'description' => $blog->description,
                                'updated_at'  => \Carbon\Carbon::parse($blog->updated_at)->format('F j, Y'),
                                'author'      => [
                                    'name'   => 'Admin',
                                    'avatar' => asset('/backend/assets/img/default-profile.png'),
                                ],
                            ];
                        }

                        $section['section_type'] = 'blog';
                        $section['design'] = 'blog_one';
                        $section['section_content'] = $blogs;
                    }
                }

                // Search Section
                if (is_array($section) && ($section['status'] ?? 0) == 1) {
                    $content = $section['section_content'] ?? '';

                    if (is_string($content) && strpos($content, '[search') !== false) {
                        preg_match('/limit=(\d+)\s+viewall=(yes|no)\s+order=(asc|desc)/', $content, $matches);
                        $limit = $matches[1] ?? 10;
                        $viewAll = $matches[2] ?? 'no';
                        $order = $matches[3] ?? 'asc';

                        // Provide static content
                        $section['section_type'] = 'search_section';
                        $section['type'] = 'search_section';
                        $section['design'] = 'search_one';
                        $section['section_content'] = [
                            "title"       => "Search Section",
                            "description" => "Find the best vehicles and services easily."
                        ];
                    }
                }

                // Marquee Section
                if (is_array($section) && ($section['status'] ?? 0) == 1) {
                    $content = $section['section_content'] ?? '';

                    if (is_string($content) && strpos($content, '[marquee') !== false) {
                        preg_match('/limit=(\d+)\s+viewall=(yes|no)\s+order=(asc|desc)/', $content, $matches);
                        $limit = $matches[1] ?? 10;
                        $viewAll = $matches[2] ?? 'no';
                        $order = $matches[3] ?? 'asc';

                        $page = Page::select("keywords")->where("slug", $slug)->where("language_id", $lang_id)->first();

                        if (!$page) {
                            $basePage = Page::select("keywords", "id")->where('slug', $slug)->whereNull('parent_id')->first();

                            if ($basePage) {
                                $page = Page::select("keywords")->where('parent_id', $basePage->id)->where('language_id', $lang_id)->first();
                            }
                        }

                        $keywordsJsonArray = [];

                        if ($page && isset($page->keywords)) {
                            $keywords = array_map('trim', explode(',', $page->keywords));
                            foreach ($keywords as $keyword) {
                                $keywordsJsonArray[] = ['text' => $keyword];
                            }
                        }

                        $section['section_type'] = 'marquee_section';
                        $section['type'] = 'marquee_section';
                        $section['design'] = 'marquee_one';
                        $section['section_content'] = $keywordsJsonArray;
                    }
                }

                // All Category section
                if (
                    is_array($section) &&
                    ($section['status'] ?? 0) == 1 &&
                    isset($section['section_content']) &&
                    is_string($section['section_content']) &&
                    strpos($section['section_content'], '[all_category ') !== false
                ) {
                    preg_match('/limit=(\d+)\s+viewall=(yes|no)\s+order=(asc|desc)/', $section['section_content'], $matches);

                    $limit = $matches[1] ?? 12;
                    $viewAll = $matches[2] ?? 'no';
                    $order = $matches[3] ?? 'asc';

                    $allCategory = Cartype::select('name', 'id')
                        ->limit((int) $limit)
                        ->where('status', 1)
                        ->where('language_id', $lang_id)
                        ->whereNull('deleted_at')
                        ->get()
                        ->map(fn ($cartype) => ['id' => $cartype->id, 'name' => $cartype->name]);

                    $section['section_type'] = 'all_category';
                    $section['design'] = 'category_two';
                    $section['section_content'] = $allCategory;
                }

                // Facts Section
                if (is_array($section) && ($section['status'] ?? 0) == 1) {
                    $content = $section['section_content'] ?? '';

                    if (is_string($content) && strpos($content, '[facts') !== false) {
                        preg_match('/limit=(\d+)\s+viewall=(yes|no)\s+order=(asc|desc)/', $content, $matches);
                        $limit = $matches[1] ?? 10;
                        $viewAll = $matches[2] ?? 'no';
                        $order = $matches[3] ?? 'asc';

                        $userCount = User::count(); // Get total users
                        $vehicleCount = VehicleInfo::count(); // Get total vehicles
                        $locationCount = Location::count(); // Get total locations
                        $totalKm = 1976; // Keeping total_km static

                        // Set facts content dynamically
                        $section['facts_content'] = [
                            ["key" => "happy_customers", "value" => $userCount],
                            ["key" => "vehicle_count", "value" => $vehicleCount],
                            ["key" => "location_count", "value" => $locationCount],
                            ["key" => "total_km", "value" => $totalKm],
                        ];
                        $section['section_type'] = 'facts_section';
                        $section['type'] = 'facts_section';
                        $section['design'] = 'facts_one';
                    }
                }

                if (isset($section['section_content']) && is_string($section['section_content'])) {
                    if (preg_match('/\[[^\]]+\]/', $section['section_content']) === 0) {
                        $section['section_content'] = $section['section_content'];
                        $section['section_type'] = 'multiple_section';
                    }
                }
            }
        }

        if ($page) {
            $data = [
                'page_title'       => $page->page_title,
                'slug'             => $page->slug,
                'currency'         => getDefaultCurrencySymbol(),
                'language_id'      => $page->language_id,
                'content_sections' => $pageContentSections,
                'seo_tag'          => $page->seo_tag,
                'seo_title'        => $page->seo_title,
                'seo_description'  => $page->seo_description,
                'status'           => $page->status,
            ];

            $content_sections = collect((array) $data['content_sections']);

            $pageContent = $page->page_content ? json_decode($page->page_content) : [];
            $sectionContent = $pageContent && isset($pageContent[0]->section_content) ? $pageContent[0]->section_content : [];
            $seo_title = $page->page_title;



            return view('frontend.pages.page', compact('page', 'data', 'sectionContent', 'content_sections', 'seo_title'));
        } else {
            abort(404);
        }
    }
}
