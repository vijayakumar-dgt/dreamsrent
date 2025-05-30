<?php

namespace Modules\CarInfo\Repositories\Eloquent;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Modules\CarInfo\Models\Category;
use Modules\CarInfo\Models\Season;
use Modules\CarInfo\Repositories\Contracts\CategoryRepositoryInterface;
use Modules\CarInfo\Repositories\Contracts\SeasonRepositoryInterface;

class SeasonRepository implements SeasonRepositoryInterface
{
    public function save(Request $request): array
    {
        try {
            $season = $request->filled('id') ? Season::find($request->id) : new Season();

            if (!$season) {
                return [
                    'status' => 'error',
                    'code' => 422,
                    'message' => __('admin.rentals.season_not_found'),
                ];
            }

            $season->name = $request->name;
            $season->status = $request->status == "on" ? 1 : 0;
            $season->save();

            return [
                'status' => 'success',
                'code' => 200,
                'message' => empty($request->id)
                    ? __('admin.rentals.season_create_success')
                    : __('admin.rentals.season_update_success'),
            ];
        } catch (\Throwable $th) {

            return [
                'status' => 'error',
                'code' => 500,
                'message' => empty($request->id)
                    ? __('admin.common.default_create_error')
                    : __('admin.common.default_update_error'),
            ];
        }
    }

    public function getSeasons(Request $request): array
    {
        try {
            $seasons = Season::orderBy('id', 'desc');

            if ($request->filled('keyword')) {
                $seasons->where('name', 'like', '%' . $request->keyword . '%');
            }

            if ($request->filled('status')) {
                $seasons->where('status', $request->status);
            }

            $results = $seasons->get();

            return [
                'status' => 'success',
                'code'   => 200,
                'data'   => $results
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'code'   => 500,
                'message' => __('admin.common.default_retrieve_error'),
                'error' => $e->getMessage()
            ];
        }
    }

     public function getSeason($id): array
    {
        try {
            $season = Season::findOrFail($id);

            return [
                'status' => 'success',
                'code'   => 200,
                'data'   => $season
            ];
        } catch (ModelNotFoundException $e) {
            return [
                'status' => 'error',
                'code'   => 404,
                'message' => 'Season not found.'
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'code'   => 500,
                'message' => __('admin.common.default_retrieve_error'),
                'error' => $e->getMessage()
            ];
        }
    }

    public function delete(Request $request): array
    {
        try {
            $season = Season::where('id', $request->delete_id)->firstOrFail();
            $season->delete();

            return [
                'status' => 'success',
                'code'   => 200,
                'message' => __('admin.rentals.season_delete_success'),
            ];
        } catch (ModelNotFoundException $e) {
            return [
                'status' => 'error',
                'code'   => 422,
                'message' => 'Season not found',
            ];
        } catch (\Throwable $th) {
            return [
                'status' => 'error',
                'code'   => 422,
                'message' => $th->getMessage(),
            ];
        }
    }

 

}