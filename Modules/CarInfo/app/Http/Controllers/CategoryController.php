<?php

namespace Modules\CarInfo\Http\Controllers;

use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Modules\CarInfo\Models\Category;

class CategoryController extends Controller
{
    public function index()
    {
        return view('carinfo::category.index');
    }

    public function store(Request $request)
    {
        $authUser = current_user();
        $id = $request->id ?? null;
    
        $rules = [
            'name' => ['required', Rule::unique('categories', 'name')->ignore($id)->whereNull('deleted_at')],
        ];
    
        $messages = [
            'name.required' => __('admin.rentals.category_required'),
            'name.unique' => __('admin.rentals.category_unique'),
        ];
    
        $validator = Validator::make($request->all(), $rules, $messages);
    
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'code'   => 422,
                'errors' => $validator->errors()->toArray()
            ], 422);
        }
    
        $successMsg = empty($id) ? __('admin.rentals.category_create_success') : __('admin.rentals.category_update_success');
        $errorMsg = empty($id) ? __('admin.common.default_create_error') : __('admin.common.default_update_error');
    
        try {
            if (empty($id)) {
                $data = [
                    'name' => $request->name,
                    'status' => $request->status ?? 1,
                    'language_id' => $authUser->language_id,
                ];
                Category::create($data);
            } else {
                $data = [
                    'name' => $request->name,
                    'status' => $request->status ?? 1,
                    'language_id' => $request->language_id ?? $authUser->language_id,
                ];
                Category::where('id', $id)->update($data);
            }
    
            return response()->json([
                'status' => 'success',
                'code'   => 200,
                'message' => $successMsg
            ]);
        } catch (\Exception $th) {
            return response()->json([
                'status' => 'error',
                'code'   => 500,
                'message' => $errorMsg
            ], 500);
        }
    }
    

    public function list(Request $request)
    {
        $orderBy = $request->order_by ?? 'desc';
        $search = $request->input('search');
        $status = $request->input('status');
    
        try {
            $authUser = current_user();
            $language_id = $authUser->language_id ?? 1;
    
            $query = Category::orderBy('id', $orderBy)
                ->where('language_id', $language_id);
    
            if (!empty($search)) {
                $query->where('name', 'LIKE', "%{$search}%"); // Adjust column name if needed
            }
    
            if ($status !== null && $status !== '') {
                $query->where('status', $status); // Assumes 'status' column exists in categories table
            }
    
            $data = $query->get();
    
            return response()->json([
                'code' => 200,
                'message' => __('admin.common.default_retrieve_success'),
                'data' => $data,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => __('admin.common.default_retrieve_error'),
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function edit(Request $request)
    {
        $id = $request->id;
        $category = Category::find($id);

        return response()->json([
            'status' => 'success',
            'code'   => 200,
            'data' => $category
        ], 200);
    }

    public function delete(Request $request)
    {
        try {

            $id = $request->id;

            Category::where('id', $id)->delete();

            return response()->json([
                'status' => 'success',
                'code'   => 200,
                'message' => __('admin.rentals.category_delete_success')
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'code'   => 500,
                'message' => __('admin.common.default_delete_error')
            ], 500);
        }
    }

    public function bulkDelete(Request $request)
    {
        $ids = $request->ids;

        if (!$ids || count($ids) == 0) {
            return response()->json(['success' => false, 'message' => 'No items selected.']);
        }

        Category::whereIn('id', $ids)->delete();

        return response()->json(['success' => true, 'message' => 'Selected items deleted successfully.']);
    }

    public function pdfExport(Request $request)
    {
        $ids = $request->input('ids');

        if (empty($ids)) {
            return response()->json(['error' => 'No items selected'], 400);
        }

        $categories = Category::whereIn('id', $ids)->get();

        $pdf = Pdf::loadView('carinfo::category.Pdf', compact('categories'));

        return response($pdf->output(), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="Categories.pdf"');
    }
}
