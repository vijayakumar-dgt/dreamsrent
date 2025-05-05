<?php

namespace Modules\CarInfo\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Modules\CarInfo\Models\DamageType;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class DamageTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        return view('carinfo::damage_type.index');
    }

    public function storeDamageType(Request $request): JsonResponse
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
            'damage_type' => 'required|unique:damage_types,damage_type,' . $request->id . ',id,deleted_at,NULL',
        ], [
            'damage_type.required' => __('admin.rentals.damage_type_required'),
            'damage_type.unique' => __('admin.rentals.damage_type_unique'),
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
                $carType = new DamageType();
                $carType->language_id = $language_id;
                $successMessage = __('admin.rentals.damage_type_added');
            } else {
                /** @var \Modules\CarInfo\Models\DamageType  */
                $carType = DamageType::find($request->id);

                if ($carType == null) {
                    return response()->json([
                        'status' => 'error',
                        'code' => 404,
                        'message' => 'Damage type not found.'
                    ], 404);
                }

                $carType->language_id = $request->language_id ?? $carType->language_id;
                $carType->status = $request->status === 'on' ? 1 : 0;
                $successMessage = __('admin.rentals.damage_type_updated');
            }

            $carType->damage_type = $request->damage_type;
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
     * Get all damage types
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDamageTypes(Request $request): JsonResponse
    {
        /** @var \App\Models\User $authUser  */
        $authUser = current_user();
        $language_id = $authUser->language_id;
        $damageTypes = DamageType::when($request->has('keyword') && $request->keyword != "", function ($query) use ($request) {
            $query->where('damage_type', 'like', '%' . $request->keyword . '%');
        })
        ->when($request->has('status') && $request->status != "", function ($query) use ($request) {
            $query->where('status', $request->status);
        })
        ->where("language_id", $language_id)
        ->orderBy('damage_type', 'asc')->get();
        return response()->json([
            'status' => 'success',
            'code'   => 200,
            'data' => $damageTypes
        ]);
    }

    /**
     * Get damage type by id
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDamageType($id): JsonResponse
    {
        $damageType = DamageType::find($id);
        return response()->json([
            'status' => 'success',
            'code'   => 200,
            'data' => $damageType
        ]);
    }

    /**
     * Delete a damage type by id
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteDamageType(Request $request): JsonResponse
    {
        try {
            $damageType = DamageType::where('id',$request->delete_id)->firstOrFail();
            $damageType->delete();
            return response()->json([
                'status' => 'success',
                'code'   => 200,
                'message' => __('admin.rentals.damage_type_deleted')
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) { 
            return response()->json([
                'status' => 'error',
                'code'   => 422,
                'message' => __('admin.rentals.damage_type_not_found')
            ], 422);
        } catch (\Throwable $th) {
            return response()->json([
               'status' => 'error',
               'code'   => 422,
               'message' => $th->getMessage()
            ]);
        }
    }
}
