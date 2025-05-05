<?php

namespace Modules\CarInfo\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Modules\CarInfo\Models\Season;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;

class SeasonController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        return view('carinfo::season.index');
    }

    /**
     * Save or update a season.
     *
     * Validates the request data for a season name that is required,
     * has a maximum length of 100 characters, and is unique in the
     * seasons table. If validation passes, it either creates a new
     * season or updates an existing one based on the presence of an
     * 'id' in the request. Responds with a JSON containing status
     * and appropriate messages.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */

    public function save(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => [
                'required',
                'max:30',
                Rule::unique('seasons')->ignore($request->id)->whereNull('deleted_at')
            ]
        ], [
            'name.required' => __('admin.rentals.season_name_required'),
            'name.unique' => __('admin.rentals.season_name_unique'),
            'name.max' => __('admin.rentals.season_name_maxlength'),
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'code'   => 422,
                'errors' => $validator->errors()->toArray()
            ], 422);
        }
        $response = [];
        $successMessage = empty($request->id) ? __('admin.rentals.season_create_success') : __('admin.rentals.season_update_success');
        $errorMessage = empty($request->id) ? __('admin.common.default_create_error') : __('admin.common.default_update_error');

        try {
            if (!$request->filled('id')) {
                $season = new Season();
            } else {
                $season = Season::find($request->id);
                if (!($season instanceof Season)) {
                    return response()->json([
                        'status' => 'error',
                        'code'   => 422,
                        'message' => 'Season not found'
                    ]);
                }
                $season->status = $request->status == 'on' ? 1 : 0;
            }
            $season->name = $request->name;
            $season->save();
            $response = [
                'status' => 'success',
                'code'   => 200,
                'message' => $successMessage
            ];
        } catch (\Throwable $th) {
            $response = [
                'status' => 'error',
                'code'   => 422,
                'message' => $errorMessage
            ];
        }
        return response()->json($response);
    }

    /**
     * Get all seasons.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSeasons(Request $request): JsonResponse
    {
        $seasons = Season::orderBy('id', 'desc');
        if ($request->has('keyword') && $request->keyword != "") {
            $seasons = $seasons->where('name', 'like', '%' . $request->keyword . '%');
        }
        if ($request->has('status') && $request->status != "") {
            $seasons = $seasons->where('status', $request->status);
        }
        $seasons = $seasons->get();
        return response()->json([
            'status' => 'success',
            'code'   => 200,
            'data' => $seasons
        ]);
    }

    /**
     * Get a season by its ID.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSeason($id): JsonResponse
    {
        $season = Season::find($id);
        return response()->json([
            'status' => 'success',
            'code'   => 200,
            'data' => $season
        ]);
    }

    /**
     * Delete a season
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(Request $request): JsonResponse
    {
        try {
            $season = Season::where('id', $request->delete_id)->firstOrFail();
            $season->delete();
            return response()->json([
                'status' => 'success',
                'code'   => 200,
                'message' => __('admin.rentals.season_delete_success')
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'code'   => 422,
                'message' => 'Season not found'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'code'   => 422,
                'message' => $th->getMessage()
            ]);
        }
    }
}
