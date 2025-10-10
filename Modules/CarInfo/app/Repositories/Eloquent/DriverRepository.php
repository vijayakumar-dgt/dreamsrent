<?php

namespace Modules\CarInfo\Repositories\Eloquent;

use App\Services\ImageResizer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Modules\CarInfo\Models\Driver;
use Modules\CarInfo\Models\DriverDocument;
use Modules\CarInfo\Models\VehicleInfo;
use Modules\CarInfo\Repositories\Contracts\DriverRepositoryInterface;

class DriverRepository implements DriverRepositoryInterface
{
    protected ImageResizer $imageResizer;

    public function __construct(ImageResizer $imageResizer)
    {
        $this->imageResizer = $imageResizer;
    }

    public function index(): array
    {
        $cars = VehicleInfo::where('status', 1)->get(['id', 'name']);

        return ['cars' => $cars];
    }

    public function store(Request $request): array
    {
        $id = $request->id ?? '';
        $successMsg = empty($id)
            ? __('admin.manage.driver_create_success')
            : __('admin.manage.driver_update_success');
        $errorMsg = empty($id)
            ? __('admin.common.default_create_error')
            : __('admin.common.default_update_error');

        try {
            $data = $this->prepareDriverData($request);

            if (empty($id)) {
                $driver = Driver::create($data);
                $this->handleDocuments($request->file('documents'), $driver->id);
            } else {
                /** @var \Modules\CarInfo\Models\Driver */
                $driver = Driver::find($id);

                $data['image'] = $this->handleImageUpdate($request, $driver->image);
                $this->handleDocuments($request->file('documents'), $driver->id);
                $this->removeDocuments($request->removed_documents);

                $data['status'] = $request->status;
                $driver->update($data);
            }

            return [
                'status'  => 'success',
                'code'    => 200,
                'message' => $successMsg
            ];
        } catch (\Exception $e) {
            return [
                'status'  => 'error',
                'code'    => 500,
                'message' => $errorMsg,
            ];
        }
    }

    /**
    * Prepare driver data array
    */
    private function prepareDriverData(Request $request): array
    {
        $data = $request->only([
            'driver_name', 'gender', 'phone_number', 'address',
            'card_number', 'date_of_issue', 'valid_date', 'email'
        ]);

       // Assigned cars as comma-separated string
        if (is_array($request->assigned_cars)) {
            $data['assigned_cars'] = implode(',', $request->assigned_cars);
        }

        // Handle driver image
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            if ($file && $file->isValid()) {
                $data['image'] = $this->imageResizer->uploadFile($file, 'profile');
            }
        }
        return $data;
    }

    /**
    * Handle uploading multiple documents
    */
    private function handleDocuments(?array $documents, int $driverId): void
    {
        if ($documents instanceof \Illuminate\Http\UploadedFile) {
            $documents = [$documents];
        }

        foreach ($documents ?? [] as $file) {
            $documentPath = uploadFile($file, 'documents');
            DriverDocument::create([
                'driver_id' => $driverId,
                'document'  => $documentPath,
            ]);
        }
    }

    /**
    * Handle image update for existing driver
    */
    private function handleImageUpdate(Request $request, ?string $oldImage): ?string
    {
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            if ($file && $file->isValid()) {
                $oldImage = is_string($oldImage) ? $oldImage : '';
                return $this->imageResizer->uploadFile($file, 'profile', $oldImage);
            }
        }
        return $oldImage;
    }

    /**
    * Remove documents by IDs
    */
    private function removeDocuments(?string $removedDocuments): void
    {
        $docIds = explode(',', $removedDocuments);

        foreach ($docIds as $docId) {
            $document = DriverDocument::find($docId);
            if (!$document) {
                continue;
            }

            $docPath = $document->document;
            if (is_string($docPath) && Storage::disk('public')->exists('documents/' . $docPath)) {
                Storage::disk('public')->delete('documents/' . $docPath);
            }

            $document->delete();
        }
    }


    public function list(Request $request): array
    {
        try {
            $query = Driver::with(['documents']);
            $this->applySearchFilter($query, $request->search ?? null);
            $this->applyStatusFilter($query, $request->sort_by_status ?? null);
            $this->applyDateFilter($query, $request->sort_by_date ?? null);
            $this->applySortFilter($query, $request->sort_by ?? null);

            // Column ordering for DataTables
            $columnIndex = $request->order[0]['column'] ?? 0;
            $columnName = $request->columns[$columnIndex]['data'] ?? 'driver_name';
            $orderDir = $request->order[0]['dir'] ?? 'asc';
            $query->orderBy($columnName, $orderDir);

            // Total and filtered records
            $totalRecords = Driver::count();
            $filteredRecords = $query->count();

            // Pagination
            $start = $request->start ?? 0;
            $length = $request->length ?? 10;
            $drivers = $query->offset($start)->limit($length)->get();

            $drivers->map(fn($driver) => $this->formatDriverData($driver));

            return [
                'draw'            => intval($request->draw),
                'recordsTotal'    => $totalRecords,
                'recordsFiltered' => $filteredRecords,
                'data'            => $drivers,
                'code'            => 200
            ];
        } catch (\Exception $e) {
            return [
                'code'    => 500,
                'message' => __('admin.common.default_retrieve_error'),
            ];
        }
    }

    /** Apply search filter */
    private function applySearchFilter($query, ?string $search): void
    {
        if (!$search) {
            return;
        }

        $query->where(function ($q) use ($search) {
            $q->where('drivers.driver_name', 'LIKE', "%{$search}%")
            ->orWhere('drivers.card_number', 'LIKE', "%{$search}%")
            ->orWhere('drivers.phone_number', 'LIKE', "%{$search}%")
            ->orWhere('drivers.email', 'LIKE', "%{$search}%");
        });
    }

    /** Apply status filter */
    private function applyStatusFilter($query, $status): void
    {
         if ($status === null || $status === '') {
            return;
        }
        $query->where('drivers.status', $status);
    }

    /** Apply date filter */
    private function applyDateFilter($query, ?string $sortByDate): void
    {
        if (!$sortByDate) {
            return;
        }

        $dates = explode(' - ', $sortByDate);
        if (count($dates) === 2) {
            $startDate = \Carbon\Carbon::createFromFormat('m/d/Y', trim($dates[0]))->startOfDay();
            $endDate = \Carbon\Carbon::createFromFormat('m/d/Y', trim($dates[1]))->endOfDay();
            $query->whereBetween('drivers.created_at', [$startDate, $endDate]);
        }
    }

    /** Apply sorting filter */
    private function applySortFilter($query, ?string $sortBy): void
    {
        if (!$sortBy) {
            return;
        }

        switch (strtolower($sortBy)) {
            case 'latest':
                $query->orderBy('drivers.created_at', 'desc');
                break;
            case 'ascending':
                $query->orderBy('drivers.driver_name', 'asc');
                break;
            case 'descending':
                $query->orderBy('drivers.driver_name', 'desc');
                break;
            case 'last month':
                $startDate = \Carbon\Carbon::now()->subMonth()->startOfMonth();
                $endDate = \Carbon\Carbon::now()->subMonth()->endOfMonth();
                $query->whereBetween('drivers.created_at', [$startDate, $endDate]);
                break;
            case 'last 7 days':
                $startDate = \Carbon\Carbon::now()->subDays(7)->startOfDay();
                $endDate = \Carbon\Carbon::now()->endOfDay();
                $query->whereBetween('drivers.created_at', [$startDate, $endDate]);
                break;
            default:
                $query->orderBy('drivers.created_at', 'desc');
                break;
        }
    }

    /** Format driver data for response */
    private function formatDriverData($driver)
    {
        // Assigned vehicle
        $assignedCarIds = explode(',', $driver->assigned_cars);
        $firstCarId = trim($assignedCarIds[0] ?? '');
        if ($firstCarId) {
            $vehicle = VehicleInfo::where('vehicle_info.id', $firstCarId)
                ->join('cartypes', 'cartypes.id', '=', 'vehicle_info.type_id')
                ->first(['cartypes.name as cartype_name', 'vehicle_info.id', 'vehicle_info.name as vehicle_name']);

            if ($vehicle) {
                $driver->vehicle = [
                    'vehicle_id'   => $vehicle->id,
                    'vehicle_name' => $vehicle->vehicle_name,
                    'cartype_name' => $vehicle->cartype_name,
                ];
            }
        }

        // Format date and image
        $driver->valid_date = formatDateTime($driver->valid_date, false);
        $driver->image = uploadedAsset(is_array($driver->image) ? null : $driver->image, 'profile');

        return $driver;
    }


    public function getById(int $id): array
    {
        /** @var \Modules\CarInfo\Models\Driver|null $data */
        $data = Driver::with('documents')->find($id);

        if ($data) {
            $imagePath = is_array($data->image) ? null : $data->image;
            $data->image = uploadedAsset($imagePath, "profile");
        }

        return [
            'status' => 'success',
            'code'   => 200,
            'data'   => $data
        ];
    }

    public function delete(Request $request): array
    {
        try {
            $id = $request->id;
            $ids = $request->ids ?? [];

            if ($request->has('ids') && !empty($ids)) {
                Driver::whereIn('id', $ids)->delete();
                DriverDocument::whereIn('driver_id', $ids)->delete();
            } else {
                Driver::where('id', $id)->delete();
                DriverDocument::where('driver_id', $id)->delete();
            }

            return [
                'status'  => 'success',
                'code'    => 200,
                'message' => __('admin.manage.driver_delete_success')
            ];
        } catch (\Exception $e) {
            return [
                'status'  => 'error',
                'code'    => 500,
                'message' => __('admin.common.default_delete_error')
            ];
        }
    }

    public function changeStatus(Request $request): array
    {
        try {
            $id = $request->id;
            $ids = $request->ids ?? [];
            $status = $request->status ?? 1;

            if ($request->has('ids') && !empty($ids)) {
                Driver::whereIn('id', $ids)->update(['status' => $status]);
            } else {
                Driver::where('id', $id)->update(['status' => $status]);
            }

            return [
                'status'  => 'success',
                'code'    => 200,
                'message' => __('admin.manage.driver_status_success')
            ];
        } catch (\Exception $e) {
            return [
                'status'  => 'error',
                'code'    => 500,
                'message' => __('admin.common.default_status_error')
            ];
        }
    }

    public function getDrivers(Request $request): array
    {
        try {
            $vehicleIds = $request->vehicle_ids;

            $drivers = Driver::select('id', 'driver_name')
                ->when(!empty($vehicleIds), function ($query) use ($vehicleIds) {
                    foreach ($vehicleIds as $vehicleId) {
                        $query->whereRaw("FIND_IN_SET(?, assigned_cars)", [$vehicleId]);
                    }
                })
                ->where('status', 1)
                ->get();

            return [
                'code'    => 200,
                'message' => __('admin.common.default_retrieve_success'),
                'data'    => $drivers,
            ];
        } catch (\Exception $e) {
            return [
                'code'    => 500,
                'message' => __('admin.common.default_retrieve_error'),
                'error'   => $e->getMessage(),
            ];
        }
    }

    public function getDriverDetails(?int $driverId): array
    {
        try {
            $driver = Driver::select(
                'id',
                'driver_name',
                'email',
                'phone_number',
                'image',
            )
                ->where(['status' => 1, 'id' => $driverId])
                ->first();

            if ($driver) {
                $imagePath = is_array($driver->image) ? null : $driver->image;
                $driver->image = uploadedAsset($imagePath, 'profile');
            }

            return [
                'code'    => 200,
                'message' => __('admin.common.default_retrieve_success'),
                'data'    => $driver,
            ];
        } catch (\Exception $e) {
            return [
                'code'    => 500,
                'message' => __('admin.common.default_retrieve_error'),
            ];
        }
    }
}
