<?php

namespace Modules\MenuManagement\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\GeneralSetting\Models\Language;
use Modules\MenuManagement\Http\Requests\MenuManagementUpdateRequest;
use Modules\MenuManagement\Http\Requests\StoreMenuRequest;
use Modules\MenuManagement\Http\Requests\UpdateMenuRequest;
use Modules\MenuManagement\Models\Menu;
use Modules\MenuManagement\Repositories\Contracts\MenuManagementInterface;

class MenuManagementController extends Controller
{
    protected $menuRepository;

    public function __construct(MenuManagementInterface $menuRepository)
    {
        $this->menuRepository = $menuRepository;
    }

    public function menu(): View
    {
        $languages = Language::with('transLang')->get();
        return view('menumanagement::menu.menulist', compact('languages'));
    }

    public function menuManagement(): View
    {
        $langCode = app()->getLocale();
        $defaultLanguageId = getLanguageId($langCode);

        $pages = $this->menuRepository->getPagesByLanguage($defaultLanguageId);
        $menus = $this->menuRepository->getMenusByLanguage($defaultLanguageId);

        return view('menumanagement::menu.menumanagement', compact('pages', 'menus'));
    }

    public function menuManagementUpdate(MenuManagementUpdateRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();
            $menu = $this->menuRepository->updateMenuItems(
                $validated['menu_id'],
                $request->menu_items
            );

            return response()->json([
                'code'    => 200,
                'success' => true,
                'message' => __('admin.cms.menu_update_success'),
                'menu'    => $menu
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'code'    => 500,
                'success' => false,
                'message' => __('admin.common.default_update_error'),
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function menuStore(StoreMenuRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();

            // Check for duplicate header menu
            if (
                $data['menu_type'] === 'header' &&
                $this->menuRepository->exists([
                    'menu_type'   => 'header',
                    'language_id' => $data['language']
                ])
            ) {
                return response()->json([
                    'code'    => 422,
                    'message' => __('admin.cms.header_menu_exists'),
                    'errors'  => ['menu_type' => [__('admin.cms.header_menu_exists')]],
                ], 422);
            }

            $menu = $this->menuRepository->create([
                'name'          => $data['menu_name'],
                'permenantlink' => $data['menu_permalink'],
                'language_id'   => $data['language'],
                'menu_type'     => $data['menu_type'],
                'status'        => 1
            ]);

            return response()->json([
                'code'    => 200,
                'message' => __('admin.cms.menu_create_success'),
                'data'    => $menu,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'code'    => 500,
                'message' => __('admin.common.default_create_error'),
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function menuList(Request $request): JsonResponse
    {
        try {
            $langCode = app()->getLocale();
            $defaultLanguageId = $request->language_id ?? getLanguageId($langCode);

            // This variable will hold either the single menu object or the collection of menus.
            $data = null;

            // Single menu retrieval logic
            if ($request->has('id')) {
                $menu = $this->menuRepository->find($request->id);

                // Guard clause: Return early if menu not found or language mismatch
                if (!$menu || $menu->language_id != $defaultLanguageId) {
                    // Return 1: Not Found
                    return response()->json([
                        'code'    => 404,
                        'message' => 'Menu not found for the specified language',
                    ], 404);
                }

                // Assign the single menu to our data variable instead of returning
                $data = $menu;
            } else {
                // Get all menus logic
                $filters = [
                    'language_id' => $defaultLanguageId,
                    'search'      => $request->search,
                    'sort'        => $request->sort
                ];

                $menus = $this->menuRepository->all($filters)->map(function ($menu) {
                    $menu->created_date = formatDateTime($menu->created_at, false);
                    unset($menu->created_at);
                    return $menu;
                });

                // Assign the collection of menus to our data variable
                $data = $menus;
            }

            // Return 2: Consolidated Success Response
            return response()->json([
                'code'    => 200,
                'message' => __('admin.common.default_retrieve_success'),
                'data'    => $data,
            ], 200);
        } catch (\Exception $e) {
            // Return 3: Error Response
            return response()->json([
                'code'    => 500,
                'message' => __('admin.common.default_retrieve_error'),
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function menuUpdate(UpdateMenuRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();

            // Prevent duplicate header menus for the same language
            if (
                $data['editMenuType'] == 'header' &&
                $this->menuRepository->exists([
                    'menu_type'   => 'header',
                    'language_id' => $data['language'],
                    ['id', '!=', $data['menu_id']]
                ])
            ) {
                return response()->json([
                    'code'    => 422,
                    'message' => __('admin.cms.header_menu_exists'),
                    'errors'  => ['editMenuType' => [__('admin.cms.header_menu_exists')]],
                ], 422);
            }

            $menu = $this->menuRepository->update($data['menu_id'], [
                'name'          => $data['editMenuName'],
                'permenantlink' => $data['editMenuPermalink'],
                'status'        => $request->has('menu_status') ? 1 : 0,
                'language_id'   => $data['language'],
                'menu_type'     => $data['editMenuType'],
            ]);

            return response()->json([
                'code'    => 200,
                'message' => __('admin.cms.menu_update_success'),
                'data'    => $menu
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code'    => 500,
                'message' => __('admin.common.default_update_error'),
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function menuDelete(Request $request): JsonResponse
    {
        $data = $request->validate([
            'id' => 'required|integer|exists:menus,id',
        ]);

        try {
            $this->menuRepository->delete($data['id']);

            return response()->json([
                'code'    => 200,
                'message' => __('admin.cms.menu_delete_success'),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'code'    => 500,
                'message' => __('admin.common.default_delete_error'),
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}
