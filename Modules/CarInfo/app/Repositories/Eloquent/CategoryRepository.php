<?php

namespace Modules\CarInfo\Repositories\Eloquent;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Modules\CarInfo\Models\Category;
use Modules\CarInfo\Repositories\Contracts\CategoryRepositoryInterface;

class CategoryRepository implements CategoryRepositoryInterface
{
    public function store(Request $request): array
    {
        try {
            $id = $request->id ?? null;
            $authUser = current_user();
            $languageId = $authUser->language_id ?? 1;

            $data = [
                'name' => $request->name,
                'status' => $request->status ?? 1,
                'language_id' => $languageId,
            ];

            if (empty($id)) {
                Category::create($data);
                return [
                    'status' => 'success',
                    'code' => 200,
                    'message' => __('admin.rentals.category_create_success'),
                ];
            } else {
                Category::where('id', $id)->update($data);
                return [
                    'status' => 'success',
                    'code' => 200,
                    'message' => __('admin.rentals.category_update_success'),
                ];
            }
        } catch (\Exception $th) {
            return [
                'status' => 'error',
                'code' => 500,
                'message' => empty($id) 
                    ? __('admin.common.default_create_error') 
                    : __('admin.common.default_update_error'),
            ];
        }
    }
   public function list(Request $request): array
    {
        try {
            $orderBy = $request->order_by ?? 'desc';
            $search = $request->input('search');
            $status = $request->input('status');

            $authUser = current_user();
            $languageId = $authUser->language_id ?? 1;

            $query = Category::orderBy('id', $orderBy)
                ->where('language_id', $languageId);

            if (!empty($search)) {
                $query->where('name', 'LIKE', "%{$search}%");
            }

            if ($status !== null && $status !== '') {
                $query->where('status', $status);
            }

            $data = $query->get();

            return [
                'status' => 'success',
                'code' => 200,
                'message' => __('admin.common.default_retrieve_success'),
                'data' => $data,
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'code' => 500,
                'message' => __('admin.common.default_retrieve_error'),
                'error' => $e->getMessage(),
            ];
        }
    }
    public function edit(Request $request): array
    {
        try {
            $id = $request->id;

            // Attempt to find the category by ID
            $category = Category::find($id);

            if (!$category) {
                return [
                    'status' => 'error',
                    'code' => 404,
                    'message' => __('admin.common.category_not_found'),
                ];
            }

            return [
                'status' => 'success',
                'code' => 200,
                'data' => $category,
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'code' => 500,
                'message' => __('admin.common.default_retrieve_error'),
                'error' => $e->getMessage(),
            ];
        }
    }

    public function delete(Request $request): array
    {
        try {
            $id = $request->id;

            // Attempt to find and delete the category by ID
            $category = Category::find($id);

            if (!$category) {
                return [
                    'status' => 'error',
                    'code' => 404,
                    'message' => __('admin.common.category_not_found'),
                ];
            }

            // Perform the delete operation
            $category->delete();

            return [
                'status' => 'success',
                'code' => 200,
                'message' => __('admin.rentals.category_delete_success'),
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'code' => 500,
                'message' => __('admin.common.default_delete_error'),
                'error' => $e->getMessage(),
            ];
        }
    }

    public function bulkDelete(Request $request): array
    {
        try {
            $ids = $request->ids;

            // Check if IDs are provided
            if (!$ids || count($ids) == 0) {
                return [
                    'status' => 'error',
                    'code' => 400,
                    'message' => __('admin.common.no_items_selected'),
                ];
            }

            // Perform the bulk delete operation
            Category::whereIn('id', $ids)->delete();

            return [
                'status' => 'success',
                'code' => 200,
                'message' => __('admin.rentals.selected_items_deleted_successfully'),
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'code' => 500,
                'message' => __('admin.common.default_delete_error'),
                'error' => $e->getMessage(),
            ];
        }
    }

    public function pdfExport(Request $request)
    {
        try {
            $ids = $request->input('ids');

            if (empty($ids)) {
                return response()->json(['error' => 'No items selected'], 400);
            }

            $categories = Category::whereIn('id', $ids)->get();
            $pdf = Pdf::loadView('carinfo::category.Pdf', compact('categories'));
            return response($pdf->output(), 200)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'attachment; filename="Categories.pdf"');
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error generating PDF: ' . $e->getMessage()], 500);
        }
    }

}