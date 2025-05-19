<?php

namespace Modules\CarInfo\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Modules\CarInfo\Models\Cartype;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class CarTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function carTypes(): View
    {
        return view('carinfo::cartype.index');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */

    public function storeType(Request $request): JsonResponse
    {
        /** @var \App\Models\User|null $authUser */
        $authUser = current_user();
        if (!$authUser) {
            return response()->json([
                'status' => 'error',
                'code'   => 401,
                'message' => 'Unauthorized: User not authenticated.'
            ], 401);
        }
        $language_id = $authUser->language_id;

        $validator = Validator::make($request->all(), [
            'name' => 'required|max:30|unique:cartypes,name,' . $request->id . ',id,deleted_at,NULL',
        ], [
            'name.required' => __('admin.rentals.vehicle_type_required'),
            'name.unique' => __('admin.rentals.vehicle_type_unique'),
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'code'   => 422,
                'errors' => $validator->errors()->toArray()
            ], 422);
        }

        try {
            $successMessage = "";

            if (empty($request->id)) {
                // CREATE
                $carType = new Cartype();
                $carType->language_id = $language_id;
                $successMessage = __('admin.rentals.vehicle_type_added');
            } else {
                /** @var \Modules\CarInfo\Models\Cartype */
                $carType = Cartype::find($request->id);
                if ($carType == null) {
                    return response()->json([
                        'status' => 'error',
                        'code' => 404,
                        'message' => 'Vehicle type not found.'
                    ], 404);
                }

                $carType->language_id = $request->language_id ?? $carType->language_id;
                $carType->status = $request->status == 'on' ? 1 : 0;
                $successMessage = __('admin.rentals.vehicle_type_updated');
            }

            $folderName = 'vehicle_types';
            $cartypeIcon = $carType->icon ?? '';
            $oldIcon = str_replace($folderName . '/', '', $cartypeIcon);

            if ($request->hasFile('icon')) {
                $carIcon = $request->file('icon');
                if ($carIcon && $carIcon->isValid()) {
                    $carType->icon = uploadFile($carIcon, $folderName, $oldIcon);
                }
            }

            $carType->name = $request->name;
            $carType->save();

            return response()->json([
                'status' => 'success',
                'code'   => 200,
                'message' => $successMessage
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'code'   => 500,
                'message' => $th->getMessage()
            ], 500);
        }
    }


    /**
     * Get all car types.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCarTypes(Request $request): JsonResponse
    {
        $carTypes = Cartype::orderBy('name', 'asc')->get();
        return response()->json([
            'status' => 'success',
            'code'   => 200,
            'data' => $carTypes
        ]);
    }

    /**
     * Get car type by id.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCarType($id): JsonResponse
    {
        try {
            /** @var \Modules\CarInfo\Models\Cartype */
            $carType = Cartype::find($id);
            $carType->icon = $carType->icon != "" && file_exists(public_path('storage/' . $carType->icon)) ? uploadedAsset($carType->icon) : '';
            $response = [
                'status' => 'success',
                'code'   => 200,
                'data' => $carType
            ];
        } catch (\Throwable $th) {
            $response = [
                'status' => 'error',
                'code'   => 422,
                'message' => $th->getMessage()
            ];
        }

        return response()->json($response);
    }

    /**
     * Remove the specified car type from table.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */

    public function deleteType(Request $request): JsonResponse
    {
        try {
            $carType = Cartype::where('id', $request->delete_id)->firstOrFail();
            $carType->delete();

            return response()->json([
                'status' => 'success',
                'code'   => 200,
                'message' => __('admin.rentals.vehicle_type_deleted')
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'code'   => 422,
                'message' => __('admin.rentals.vehicle_type_not_found')
            ], 422);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'code'   => 422,
                'message' => $th->getMessage()
            ], 422);
        }
    }

    public function getCartypeServerside(Request $request): JsonResponse
    {
        $pageLength = $request->length;
        $offset     = $request->start;
       /** @var \App\Models\User|null $authUser */
        $authUser = current_user();
        if (!$authUser) {
            return response()->json([
                'status' => 'error',
                'code'   => 401,
                'message' => 'Unauthorized: User not authenticated.'
            ], 401);
        }
        $language_id = $authUser->language_id;
        $cartypes   = Cartype::query()->where("language_id", $language_id);
        if ($request->has('search') && $request->search != null) {
            $cartypes->where('name', 'like', '%' . $request->search . '%');
        }
        if ($request->has('status') && $request->status != "") {
            $cartypes->where('status', $request->status);
        }
        $filteredRecords = $cartypes->count();
        $totalRecords    = Cartype::where("language_id", $language_id)->count();
        $cartypes = $cartypes->orderBy('name', 'asc')
            ->skip($offset)
            ->take($pageLength)
            ->get();
        $cartypes = $cartypes->map(function ($cartype) {
            return [
                'id' => $cartype->id,
                'name' => $cartype->name,
                'icon' => uploadedAsset($cartype->icon ?? '', 'default'),
                'status' => $cartype->status,
            ];
        });
        return response()->json([
            'draw' => $request->draw,
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $cartypes
        ]);
    }

    public function getVehicleTypes(Request $request): JsonResponse
    {
        $orderBy = $request->order_by ?? 'asc';
        $search = $request->search ?? null;

        $carTypes = Cartype::when($search, function ($query) use ($search) {
                return $query->where('name', 'LIKE', "%{$search}%");
        })
            ->orderBy('id', $orderBy)
            ->where('status', 1)
            ->get();

        return response()->json([
            'status' => 'success',
            'code'   => 200,
            'data' => $carTypes
        ]);
    }
}
