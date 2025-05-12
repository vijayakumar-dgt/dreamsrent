<?php

namespace Modules\CarInfo\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Modules\CarInfo\Models\VehicleInfo;
use Modules\CarInfo\Models\CarModel;
use Modules\CarInfo\Models\Driver;
use Modules\CarInfo\Models\DriverDocument;

use function PHPUnit\Framework\isArray;

class DriverController extends Controller
{
    public function index(): View
    {
        $cars = DB::table('vehicle_info')->get(['id', 'name']);
        return view('carinfo::driver.index', compact('cars'));
    }

    public function store(Request $request): JsonResponse
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

        $validator = Validator::make($request->all(), [
            'driver_name' => [
                'required',
                'max:20',
            ],
            'gender' => ['required'],
            'phone_number' => ['required'],
            'email' => ['required', 'email'],
            'address' => ['required','max:150'],
            'card_number' => [
                'required',
                Rule::unique('drivers', 'card_number')->ignore($id)->whereNull('deleted_at'),
            ],
            'image' => 'mimes:jpeg,jpg,png|max:2048',
            'date_of_issue' => ['required'],
            'valid_date' => ['required'],
            'documents.*' => 'file|mimes:jpeg,jpg,png,pdf,doc,docx|max:5120',
        ], [
            'driver_name.required' => __('admin.manage.driver_name_required'),
            'driver_name.max' => __('admin.manage.driver_name_maxlength'),
            'gender.required' => __('admin.manage.gender_required'),
            'phone_number.required' => __('admin.common.phone_number_required'),
            'address.required' => __('admin.manage.address_required'),
            'address.max' => __('admin.manage.address_maxlength'),
            'image.mimes' => __('admin.common.image_format'),
            'image.max' => __('admin.common.image_size', ['size' => 2]),
            'documents.*.mimes' => __('admin.manage.documents_format'),
            'documents.*.max' => __('admin.manage.documents_size', ['size' => 5]),
            'card_number.required' => __('admin.manage.card_number_required'),
            'card_number.unique' => __('admin.manage.card_number_unique'),
            'date_of_issue.required' => __('admin.manage.date_of_issue_required'),
            'valid_date.required' => __('admin.manage.valid_date_required'),
            'email.required' => __('admin.common.email_required'),
            'email.email' => __('admin.common.email_valid'),
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'code'   => 422,
                'errors' => $validator->errors()->toArray()
            ], 422);
        }

        $successMsg = empty($id) ? __('admin.manage.driver_create_success') : __('admin.manage.driver_update_success');
        $errorMsg = empty($id) ?  __('admin.common.default_create_error') : __('admin.common.default_update_error');

        try {
            if (empty($id)) {
                if ($request->hasFile('image')) {
                    $file = $request->file('image');
                    if (!$file || !$file->isValid()) {
                        return response()->json([
                            'status' => 'error',
                            'code'   => 422,
                            'message' => __('admin.common.file_upload_error')
                        ], 422);
                    }
                    $data['image'] = uploadFile($file, 'drivers');
                }
                $assignedCars = $request->assigned_cars ;
                if (is_array($assignedCars)) {
                    $data['assigned_cars'] = implode(',', $assignedCars);
                }
                $driver = Driver::create($data);

                $documents = $request->file('documents');

                if ($documents instanceof \Illuminate\Http\UploadedFile) {
                    $documents = [$documents]; // Wrap in array if only one file is uploaded
                }

                foreach ($documents ?? [] as $file) {
                    // No need to check instanceof, we assume it's a valid UploadedFile
                    $document = uploadFile($file, 'drivers');
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
                        $data['image'] = uploadFile($file, 'drivers', $oldImage);
                    }
                }

                $assignedCars = $request->assigned_cars ;
                if (is_array($assignedCars)) {
                    $data['assigned_cars'] = implode(',', $assignedCars);
                }
                $documents = $request->file('documents');

                if ($documents instanceof \Illuminate\Http\UploadedFile) {
                    $documents = [$documents]; // Wrap in array if only one file is uploaded
                }
                foreach ($documents ?? [] as $file) {
                    // No need to check instanceof, we assume it's a valid UploadedFile
                    $document = uploadFile($file, 'drivers');
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

                        if (is_string($doc) && Storage::disk('public')->exists('drivers/' . $doc)) {
                            Storage::disk('public')->delete('drivers/' . $doc);
                        }
                    }
                    if (DriverDocument::where('id', $docId)->exists()) {
                        DriverDocument::where('id', $docId)->delete();
                    }
                }

                $data['status'] = $request->status;
                Driver::where('id', $id)->update($data);
            }

            return response()->json([
                'status' => 'success',
                'code'   => 200,
                'message' => $successMsg
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'code'   => 500,
                'message' => $errorMsg,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function list(Request $request): JsonResponse
    {
        try {
            $start = $request->start ?? 0;
            $length = $request->length ?? 10;
            $searchValue = $request->search ?? null;
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
            return response()->json([
                'draw' => intval($request->draw),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $filteredRecords,
                'data' => $drivers,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => __('admin.common.default_retrieve_error'),
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function edit(Request $request): JsonResponse
    {
        $id = $request->id;
        /** @var \Modules\CarInfo\Models\Driver|null $data */
        $data = Driver::with('documents')->find($id);

        if ($data) {
            $imagePath = is_array($data->image) ? null : $data->image;
            $data->image = uploadedAsset($imagePath, "profile");
        }

        return response()->json([
            'status' => 'success',
            'code'   => 200,
            'data' => $data
        ], 200);
    }

    public function delete(Request $request): JsonResponse
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

            return response()->json([
                'status' => 'success',
                'code'   => 200,
                'message' => __('admin.manage.driver_delete_success')
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'code'   => 500,
                'message' => __('admin.common.default_delete_error')
            ], 500);
        }
    }

    public function changeStatus(Request $request): JsonResponse
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

            return response()->json([
                'status' => 'success',
                'code'   => 200,
                'message' => __('admin.manage.driver_status_success')
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'code'   => 500,
                'message' => __('admin.common.default_status_error')
            ], 500);
        }
    }

    public function getDrivers(Request $request): JsonResponse
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

            return response()->json([
                'code'   => 200,
                'message' => __('admin.common.default_retrieve_success'),
                'data' => $drivers,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'code'   => 500,
                'message' => __('admin.common.default_retrieve_error'),
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getDriverDetails(Request $request): JsonResponse
    {
        try {
            $driverId = $request->driver_id;

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

            return response()->json([
                'code'   => 200,
                'message' => __('admin.common.default_retrieve_success'),
                'data' => $driver,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'code'   => 500,
                'message' => __('admin.common.default_retrieve_error'),
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
