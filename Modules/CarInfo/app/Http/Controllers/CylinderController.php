<?php

namespace Modules\CarInfo\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Modules\CarInfo\Models\Cylinder;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;

class CylinderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        return view('carinfo::cylinder.index');
    }

    /**
     * Store or update a cylinder type.
     *
     * Validates the request data for creating or updating a cylinder type. If the `id` is provided,
     * it updates the existing cylinder type; otherwise, it creates a new one. It ensures the
     * cylinder type is unique. Returns a JSON response indicating success or failure.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */

    public function storeCylinderType(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'cylinder_type' => 'required|unique:cylinders,cylinder_type,' . $request->id . ',id,deleted_at,NULL',
        ], [
            'cylinder_type.required' => __('admin.rentals.cylinder_type_required'),
            'cylinder_type.unique' => __('admin.rentals.cylinder_type_unique'),
        ]);

        if ($validator->fails()) {
            return response()->json([
                 'status' => 'error',
                 'code'   => 422,
                 'errors' => $validator->errors()->toArray(),
            ], 422);
        }

        try {
            $successMessage = "";
            if ($request->has('id') && $request->id == '') {
                $cylinder = new Cylinder();
                $successMessage = __('admin.rentals.cylinder_type_added');
            } else {
                 /** @var \Modules\CarInfo\Models\Cylinder  */
                $cylinder = Cylinder::find($request->id);
                $cylinder->status = $request->status == 'on' ? 1 : 0;
                $successMessage = __('admin.rentals.cylinder_type_updated');
            }
            $cylinder->cylinder_type = $request->cylinder_type;
            $cylinder->save();

            return response()->json([
                'status' => 'success',
                'code'   => 200,
                'message' => $successMessage
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'code'   => 422,
                'message' => $th->getMessage()
            ], 422);
        }
    }

    /**
     * Retrieves all cylinder types
     *
     * Returns a JSON response with a collection of cylinder types ordered by ID in descending order.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCylinders(): JsonResponse
    {
        $cylinders = Cylinder::orderBy('id', 'desc')->get();
        return response()->json([
            'status' => 'success',
            'code'   => 200,
            'data' => $cylinders
        ], 200);
    }

    /**
     * Retrieves a cylinder type
     *
     * Returns a JSON response with the cylinder type found by the given ID.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCylinder($id): JsonResponse
    {
        try {
            $cylinder = Cylinder::find($id);
            return response()->json([
                'status' => 'success',
                'code'   => 200,
                'data' => $cylinder
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'code'   => 422,
                'message' => $th->getMessage()
            ], 422);
        }
    }

    /**
     * Delete a cylinder type by ID
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */

    public function deleteCylinder(Request $request): JsonResponse
    {
        try {
            $cylinder = Cylinder::where('id',$request->delete_id)->firstOrFail();
            $cylinder->delete();
            return response()->json([
                'status' => 'success',
                'code'   => 200,
                'message' => __('admin.rentals.cylinder_type_deleted')
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'code'   => 422,
                'message' => __('admin.rentals.cylinder_type_not_found')
            ], 422);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'code'   => 422,
                'message' => $th->getMessage()
            ], 422);
        }
    }

    /**
     * Retrieve cylinder types in a serverside paginated manner.
     *
     * The request must contain the following parameters:
     *
     * - `draw`: The draw counter that ensures the DataTables request is processed correctly.
     * - `length`: The number of records to display on each page.
     * - `start`: The starting point for the query.
     * - `search[value]`: The search string to filter the records.
     *
     * The response will contain the following keys:
     *
     * - `draw`: The same draw counter as the request.
     * - `recordsTotal`: The total number of records in the database.
     * - `recordsFiltered`: The total number of records that are filtered.
     * - `data`: An array of records.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCylinderServerside(Request $request): JsonResponse
    {
        $pageLength = $request->length;
        $offset     = $request->start;
        $cylinders  = Cylinder::query();
        if ($request->has('search') && $request->search != null) {
            $cylinders = $cylinders->where(function ($query) use ($request) {
                          $query->where('cylinder_type', 'like', '%' . $request->search . '%')
                                ->orWhere('status', 'like', '%' . $request->search . '%');
            });
        }
        if ($request->has('status') && $request->status != null) {
            $cylinders = $cylinders->where('status', $request->status);
        }
        $cylinders = $cylinders->skip($offset)->take($pageLength)->orderBy('cylinder_type', 'asc')->get();
        $totalRecords = $filteredRecords =  Cylinder::count();

        return response()->json([
            'draw' => $request->draw,
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $cylinders
        ]);
    }
}
