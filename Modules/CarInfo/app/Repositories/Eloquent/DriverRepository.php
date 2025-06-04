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
        $data = ['cars' => $cars];
        return $data;
    }

    public function store(Request $request): array
    {
        $id = $request->id ?? '';
        $data = [
            'driver_name' => $request->driver_name,
            'gender' => $request->gender,
            'phone_number' => $request->phone_number,
            'address' => $request->address,
            'card_number' => $request->card_number,
            'date_of_issue' => $request->date_of_issue,
            'valid_date' => $request->valid_date,
            'email' => $request->email,
        ];

        $successMsg = empty($id) ? __('admin.manage.driver_create_success') : __('admin.manage.driver_update_success');
        $errorMsg = empty($id) ?  __('admin.common.default_create_error') : __('admin.common.default_update_error');

        try {
            if (empty($id)) {
                if ($request->hasFile('image')) {
                    $file = $request->file('image');
                    if (!$file || !$file->isValid()) {
                        return [
                            'status' => 'error',
                            'code'   => 422,
                            'message' => __('admin.common.file_upload_error')
                        ];
                    }
                    $data['image'] = $this->imageResizer->uploadFile($file, 'profile');
                }
                $assignedCars = $request->assigned_cars ;
                if (is_array($assignedCars)) {
                    $data['assigned_cars'] = implode(',', $assignedCars);
                }
                $driver = Driver::create($data);

                $documents = $request->file('documents');

                if ($documents instanceof \Illuminate\Http\UploadedFile) {
                    $documents = [$documents];
                }

                foreach ($documents ?? [] as $file) {
                    $document = uploadFile($file, 'documents');
                    DriverDocument::create([
                        'driver_id' => $driver->id,
                        'document' => $document,
                    ]);
                }
            } else {
                /** @var \Modules\CarInfo\Models\Driver */
                $driver = Driver::find($id);
                $oldImage = $driver->image;

                if ($request->hasFile('image')) {
                    $file = $request->file('image');
                    if ($file && $file->isValid()) {
                        $oldImage = is_string($oldImage) ? $oldImage : '';
                        $data['image'] = $this->imageResizer->uploadFile($file, 'profile', $oldImage);
                    }
                }

                $assignedCars = $request->assigned_cars ;
                if (is_array($assignedCars)) {
                    $data['assigned_cars'] = implode(',', $assignedCars);
                }
                $documents = $request->file('documents');

                if ($documents instanceof \Illuminate\Http\UploadedFile) {
                    $documents = [$documents];
                }
                foreach ($documents ?? [] as $file) {
                    $document = uploadFile($file, 'documents');
                    DriverDocument::create([
                        'driver_id' => $driver->id,
                        'document' => $document,
                    ]);
                }

                $removedDocuments = explode(',', $request->removed_documents);

                foreach ($removedDocuments as $docId) {
                    $removedDocument = DriverDocument::where('id', $docId)->first();
                    if ($removedDocument) {
                        $doc = $removedDocument->document;

                        if (is_string($doc) && Storage::disk('public')->exists('documents' . $doc)) {
                            Storage::disk('public')->delete('documents' . $doc);
                        }
                    }
                    if (DriverDocument::where('id', $docId)->exists()) {
                        DriverDocument::where('id', $docId)->delete();
                    }
                }

                $data['status'] = $request->status;
                Driver::where('id', $id)->update($data);
            }

            return [
                'status' => 'success',
                'code'   => 200,
                'message' => $successMsg
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'code'   => 500,
                'message' => $errorMsg,
            ];
        }
    }

    public function list(Request $request): array
    {
        try {
            $start = $request->start ?? 0;
            $length = $request->length ?? 10;
            $columnIndex = $request->order[0]['column'] ?? 0;
            $columnName = $request->columns[$columnIndex]['data'] ?? 'driver_name';
            $orderDir = $request->order[0]['dir'] ?? 'asc';

            $query = Driver::with(['documents']);

            // Search Filter
            if ($request->has('search') && !empty($request->search)) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('drivers.driver_name', 'LIKE', "%{$search}%")
                        ->orWhere('drivers.card_number', 'LIKE', "%{$search}%")
                        ->orWhere('drivers.phone_number', 'LIKE', "%{$search}%")
                        ->orWhere('drivers.email', 'LIKE', "%{$search}%");
                });
            }

            // Status Filter
            if ($request->has('sort_by_status') && !empty($request->sort_by_status) || $request->sort_by_status == '0') {
                $status = $request->sort_by_status;
                $query->where('drivers.status', $status);
            }

            // Date Filter
            if ($request->has('sort_by_date') && !empty($request->sort_by_date)) {
                $dates = explode(' - ', $request->sort_by_date);
                if (count($dates) === 2) {
                    $startDate = \Carbon\Carbon::createFromFormat('m/d/Y', trim($dates[0]));
                    $endDate = \Carbon\Carbon::createFromFormat('m/d/Y', trim($dates[1]));

                    // Apply date filter only if valid date format
                    if ($startDate && $endDate) {
                        $query->whereBetween('drivers.created_at', [$startDate->startOfDay(), $endDate->endOfDay()]);
                    }
                }
            }

            // Apply Sort Filter
            if ($request->has('sort_by') && !empty($request->sort_by)) {
                switch (strtolower($request->sort_by)) {
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
                }
            }

            $query->orderBy($columnName, $orderDir);

            // Total and Filtered Records
            $totalRecords = Driver::count();
            $filteredRecords = $query->count();

            // Pagination
            $query->offset($start)->limit($length);
            $drivers = $query->get();

            // Format Response Data
            $drivers->map(function ($driver) {
                $assignedCarIds = explode(',', $driver->assigned_cars);
                $firstCarId = !empty($assignedCarIds[0]) ? trim($assignedCarIds[0]) : null;

                if ($firstCarId) {
                    $vehicle = VehicleInfo::where('vehicle_info.id', $firstCarId)
                        ->join('cartypes', 'cartypes.id', '=', 'vehicle_info.type_id')
                        ->first(['cartypes.name as cartype_name', 'vehicle_info.id', 'vehicle_info.name as vehicle_name']);

                    if ($vehicle) {
                        $driver->vehicle = [
                            'vehicle_id' => $vehicle->id,
                            'vehicle_name' => $vehicle->vehicle_name,
                            'cartype_name' => $vehicle->cartype_name,
                        ];
                    }
                }

                // Format date and image
                $driver->valid_date = formatDateTime($driver->valid_date, false);
                $imagePath = is_array($driver->image) ? null : $driver->image;
                $driver->image = uploadedAsset($imagePath, 'profile');

                return $driver;
            });

            // Prepare DataTable response
            return [
                'draw' => intval($request->draw),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $filteredRecords,
                'data' => $drivers,
                'code' => 200
            ];
        } catch (\Exception $e) {
            return [
                'code' => 500,
                'message' => __('admin.common.default_retrieve_error'),
            ];
        }
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
            'data' => $data
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
                'status' => 'success',
                'code'   => 200,
                'message' => __('admin.manage.driver_delete_success')
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'code'   => 500,
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
                'status' => 'success',
                'code'   => 200,
                'message' => __('admin.manage.driver_status_success')
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'code'   => 500,
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
                'code'   => 200,
                'message' => __('admin.common.default_retrieve_success'),
                'data' => $drivers,
            ];
        } catch (\Exception $e) {
            return [
                'code'   => 500,
                'message' => __('admin.common.default_retrieve_error'),
                'error' => $e->getMessage(),
            ];
        }
    }

    public function getDriverDetails(int $driverId): array
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
                'code'   => 200,
                'message' => __('admin.common.default_retrieve_success'),
                'data' => $driver,
            ];
        } catch (\Exception $e) {
            return [
                'code'   => 500,
                'message' => __('admin.common.default_retrieve_error'),
            ];
        }
    }
}
