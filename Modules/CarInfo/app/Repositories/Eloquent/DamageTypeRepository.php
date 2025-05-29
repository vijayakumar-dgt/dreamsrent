<?php

namespace Modules\CarInfo\Repositories\Eloquent;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Modules\CarInfo\Models\DamageType;
use Modules\CarInfo\Repositories\Contracts\DamageTypeRepositoryInterface;

class DamageTypeRepository implements DamageTypeRepositoryInterface
{
    public function store(Request $request): array
    {
        /** @var \App\Models\User|null $authUser */
        $authUser = current_user();

        if (!$authUser) {
            return [
                'status' => 'error',
                'code'   => 401,
                'message' => __('auth.unauthorized'),
            ];
        }
        $id = $request->input('id');

        $successMessage = $id
            ? __('admin.rentals.damage_type_updated')
            : __('admin.rentals.damage_type_added');
        
        $errorMessage = $id
            ? __('admin.common.default_update_error')
            : __('admin.common.default_create_error');
        
        try {

            $data = [
                'language_id'  => $id ? $request->input('language_id') : $authUser->language_id,
                'damage_type'  => $request->input('damage_type'),
                'status'       => $id ? ($request->input('status') === 'on' ? 1 : 0) : 1,
            ];

            DamageType::updateOrCreate(['id' => $id], $data);

            return [
                'status'  => 'success',
                'code'    => 200,
                'message' => $successMessage
            ];
        } catch (\Throwable $th) {
            return [
                'status'  => 'error',
                'code'    => 500,
                'message' => $errorMessage,
            ];
        }
    }

    public function getALl(Request $request): array
    {
        try {
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
                
            return [
                'status' => 'success',
                'code'   => 200,
                'data' => $damageTypes
            ];
        } catch (\Exception $e) {
            return [
                'code' => 500,
                'message' => __('admin.common.default_retrieve_error'),
                'error' => $e->getMessage(),
            ];
        }
    }

    public function getById(int $id): array
    {
        $data = DamageType::find($id);

        if (!$data) {
            return [
                'status' => 'error',
                'code'   => 404,
                'message' => __('admin.rentals.damage_type_not_found')
            ];
        }

        return [
            'status' => 'success',
            'code'   => 200,
            'data' => $data
        ];
    }

    public function delete(int $id): array
    {
        try {
            /** @var \Modules\CarInfo\Models\DamageType $damageType */
            $damageType = DamageType::findOrFail($id);
            $damageType->delete();

            return [
                'status' => 'success',
                'code'   => 200,
                'message' => __('admin.rentals.damage_type_deleted')
            ];
        } catch (ModelNotFoundException $e) {
            return [
                'status' => 'error',
                'code'   => 404,
                'message' => __('admin.rentals.damage_type_not_found'),
            ];
        } catch (\Throwable $e) {
            return [
                'status' => 'error',
                'code'   => 500,
                'message' => __('admin.common.default_delete_error'),
            ];
        }
    }
}