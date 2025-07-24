<?php

namespace Modules\CarInfo\Repositories\Eloquent;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Modules\CarInfo\Models\Cylinder;
use Modules\CarInfo\Repositories\Contracts\CylinderRepositoryInterface;

class CylinderRepository implements CylinderRepositoryInterface
{
    public function storeCylinderType(Request $request): array
    {
        try {
            $successMessage = '';

            if (!$request->filled('id')) {
                $cylinder = new Cylinder();
                $successMessage = __('admin.rentals.cylinder_type_added');
            } else {
                $cylinder = Cylinder::find($request->id);
                if (!$cylinder) {
                    return [
                        'status'  => 'error',
                        'code'    => 404,
                        'message' => __('admin.rentals.cylinder_type_not_found')
                    ];
                }

                $cylinder->status = $request->status == 'on' ? 1 : 0;
                $successMessage = __('admin.rentals.cylinder_type_updated');
            }

            $cylinder->cylinder_type = $request->cylinder_type;
            $cylinder->save();

            return [
                'status'  => 'success',
                'code'    => 200,
                'message' => $successMessage
            ];
        } catch (\Throwable $th) {
            return [
              'status'  => 'error',
              'code'    => 422,
              'message' => $th->getMessage()
            ];
        }
    }

    public function getCylinders(): array
    {
        try {
            $cylinders = \Modules\CarInfo\Models\Cylinder::orderBy('id', 'desc')->get();

            return [
                'status' => 'success',
                'code'   => 200,
                'data'   => $cylinders
            ];
        } catch (\Throwable $th) {
            return [
                'status'  => 'error',
                'code'    => 500,
                'message' => $th->getMessage()
            ];
        }
    }

    public function getCylinder($id): array
    {
        try {
            $cylinder = \Modules\CarInfo\Models\Cylinder::findOrFail($id);

            return [
                'status' => 'success',
                'code'   => 200,
                'data'   => $cylinder
            ];
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return [
                'status'  => 'error',
                'code'    => 404,
                'message' => __('admin.rentals.cylinder_type_not_found')
            ];
        } catch (\Throwable $th) {
            return [
                'status'  => 'error',
                'code'    => 422,
                'message' => $th->getMessage()
            ];
        }
    }

    public function deleteCylinder(Request $request): array
    {
        try {
            $cylinder = Cylinder::where('id', $request->delete_id)->firstOrFail();
            $cylinder->delete();

            return [
                'status'  => 'success',
                'code'    => 200,
                'message' => __('admin.rentals.cylinder_type_deleted')
            ];
        } catch (ModelNotFoundException $e) {
            return [
              'status'  => 'error',
              'code'    => 422,
              'message' => __('admin.rentals.cylinder_type_not_found')
            ];
        } catch (\Throwable $th) {
            return [
             'status'  => 'error',
             'code'    => 422,
             'message' => $th->getMessage()
            ];
        }
    }

    public function getCylinderServerside(Request $request): array
    {
        $pageLength = $request->length;
        $offset = $request->start;

        $query = Cylinder::query();

        if ($request->has('search') && $request->search != null) {
            $query->where(function ($q) use ($request) {
                $q->where('cylinder_type', 'like', '%' . $request->search . '%')
                  ->orWhere('status', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->has('status') && $request->status != null) {
            $query->where('status', $request->status);
        }

        $totalRecords = Cylinder::count();
        $filteredRecords = $query->count();

        $cylinders = $query->skip($offset)
                           ->take($pageLength)
                           ->orderBy('cylinder_type', 'asc')
                           ->get();

        return [
            'draw'            => $request->draw,
            'recordsTotal'    => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data'            => $cylinders
        ];
    }
}
