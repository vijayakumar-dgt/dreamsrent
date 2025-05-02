<?php

namespace Modules\MenuManagement\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Modules\MenuManagement\Models\Menu;
use Modules\GeneralSetting\Models\Language;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Modules\GeneralSetting\Models\TranslationLanguage;
use Illuminate\View\View;

class MenuManagementController extends Controller
{
    public function menu(): View
    {
        $languages = Language::with('transLang')->get();
        return view('menumanagement::menu.menulist', compact('languages'));
    }

    public function menuManagement(): View
    {
        $langCode = app()->getLocale();  // Fixed: Removed unnecessary null coalescing operator
        $defaultLanguageId = getLanguageId($langCode);
        
        // Fetch pages (page title and slug)
        $pages = DB::table('pages')->select('id', 'page_title', 'slug')->where('language_id', $defaultLanguageId)->get();

        // Fetch menus (menu name and ID)
        $menus = DB::table('menus')
        ->where('language_id', $defaultLanguageId)
        ->select('id', 'name')
        ->get();

        return view('menumanagement::menu.menumanagement', compact('pages', 'menus'));
    }

    public function menuManagementUpdate(Request $request): JsonResponse
    {
        $request->validate([
            'menu_id' => 'required|exists:menus,id',
            'menu_items' => 'required|array|min:1',
        ]);

        foreach ($request->menu_items as $item) {
            if (empty($item['link'])) {
                return response()->json([
                    'code' => 422,
                    'success' => false,
                    'message' => 'The link field is required for all menu items.',
                ], 422);
            }
        }

        $menu = Menu::find($request->menu_id);  // Fixed: Ensured single instance

        if (!$menu) {
            return response()->json([
                'code' => 404,
                'success' => false,
                'message' => 'Menu not found'
            ], 404);
        }

        $menu->update([
            'menus' => json_encode($request->menu_items),
        ]);

        return response()->json([
            'code' => 200,
            'success' => true,
            'message' => __('admin.cms.menu_update_success'),
            'menu' => $menu
        ], 200);
    }

    public function menuStore(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'menu_name' => 'required|string|max:255',
                'menu_type' => 'required',
                'menu_permalink' => 'required|url',
                'language' => 'required|integer',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'code' => 422,
                    'message' => 'Validation Error',
                    'errors' => $validator->errors(),
                ], 422);
            }

            if ($request->menu_type === 'header' && Menu::where(['menu_type' => 'header', 'language_id' => $request->language])->exists()) {
                return response()->json([
                    'code' => 422,
                    'message' => __('admin.cms.header_menu_exists'),
                    'errors' => ['menu_type' => [__('admin.cms.header_menu_exists')]],
                ], 422);
            }

            $menu = Menu::create([
                'name' => $request->menu_name,
                'permenantlink' => $request->menu_permalink,
                'language_id' => $request->language,
                'menu_type' => $request->menu_type,
            ]);

            return response()->json([
                'code' => 200,
                'message' => __('admin.cms.menu_create_success'),
                'data' => $menu,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => __('admin.common.default_create_error'),
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function menuList(Request $request): JsonResponse
    {
        try {
            $langCode = app()->getLocale();  // Fixed: Removed unnecessary null coalescing operator
            $defaultLanguageId = $request->language_id;

            if (!$defaultLanguageId) {
                $defaultLanguageId = getLanguageId($langCode);
            }

            // Check if 'id' is passed in the request
            if ($request->has('id')) {
                $menu = Menu::where('id', $request->id)
                            ->where('language_id', $defaultLanguageId)
                            ->first();

                if (!$menu) {
                    return response()->json([
                        'code' => 404,
                        'message' => 'Menu not found for the default language',
                    ], 404);
                }

                return response()->json([
                    'code' => 200,
                    'message' => __('admin.common.default_retrieve_success'),
                    'data' => $menu,
                ], 200);
            }

            // If no ID is provided, return all menus for the default language
            $menus = Menu::where('language_id', $defaultLanguageId)
                        ->orderBy('created_at', 'desc')
                        ->get();

            return response()->json([
                'code' => 200,
                'message' => __('admin.common.default_retrieve_success'),
                'data' => $menus,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => __('admin.common.default_retrieve_error'),
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function menuUpdate(Request $request): JsonResponse
    {
        // Validate request data
        $request->validate([
            'menu_id' => 'required|exists:menus,id',
            'editMenuType' => 'required',
            'editMenuName' => 'required|string|min:3|max:255',
            'editMenuPermalink' => 'required|url|max:255',
            'menu_status' => 'nullable|in:on,off',
            'language' => 'required|integer',
        ]);

        try {
            $menu = Menu::findOrFail($request->menu_id);  // Fixed: Ensured single instance

            // Check if updating to 'header' type and another 'header' menu already exists
            if (
                $request->editMenuType == 'header' &&
                Menu::where(['menu_type' => 'header', 'language_id' => $request->language])->where('id', '!=', $request->menu_id)->exists()
            ) {
                return response()->json([
                    'code' => 422,
                    'message' => __('admin.cms.header_menu_exists'),
                    'errors' => ['editMenuType' => [__('admin.cms.header_menu_exists')]],
                ], 422);
            }

            // Update menu
            $menu->update([
                'name' => $request->editMenuName,
                'permenantlink' => $request->editMenuPermalink,
                'status' => $request->has('menu_status') ? 1 : 0,
                'language_id' => $request->language,
                'menu_type' => $request->editMenuType,
            ]);

            return response()->json([
                'code' => 200,
                'message' => __('admin.cms.menu_update_success'),
                'data' => $menu
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => __('admin.common.default_update_error'),
            ], 500);
        }
    }

    public function menuDelete(Request $request): JsonResponse
    {
        $id = $request->id;

        if (!$id) {
            return response()->json(['code' => 400, 'message' => 'Menu ID is required.'], 400);
        }

        try {
            $faq = Menu::findOrFail($id);  // Fixed: Ensured single instance
            $faq->delete();

            return response()->json([
                'code' => 200,
                'message' => __('admin.cms.menu_delete_success'),
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
