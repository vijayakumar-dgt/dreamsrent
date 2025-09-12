<?php

namespace Modules\CarInfo\Repositories\Eloquent;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Modules\CarInfo\Models\Enquiry;
use Modules\CarInfo\Repositories\Contracts\EnquiryRepositoryInterface;

class EnquiryRepository implements EnquiryRepositoryInterface
{
    public function store(Request $request): array
    {
        try {
            $enquiries = [];

            foreach ($request->assigned_cars as $carId) {
                $enquiries[] = Enquiry::create([
                    'car_id'          => $carId,
                    'customer_name'   => $request->customer_name,
                    'email'           => $request->email,
                    'phone'           => $request->phone_number,
                    'enquiry_date'    => now(),
                    'enquiry_details' => $request->enquiry_details,
                    'status'          => '1'
                ]);
            }

            return [
                'code'    => 200,
                'success' => true,
                'message' => __('admin.bookings.enquiry_create_success'),
                'data'    => $enquiries
            ];
        } catch (\Exception $e) {
            return [
                'code'    => 500,
                'success' => false,
                'message' => __('admin.common.default_create_error'),
            ];
        }
    }

    public function getAll(Request $request): array
    {
        try {
            $query = Enquiry::select('enquiries.*', 'vehicle_info.name as car_name', 'vehicle_info.vehicle_image', 'vehicle_info.type_id', 'cartypes.name as type_name')
                ->join('vehicle_info', 'enquiries.car_id', '=', 'vehicle_info.id')
                ->join('cartypes', 'vehicle_info.type_id', '=', 'cartypes.id');

            $this->applyStatusFilter($query, $request);
            $this->applySearchFilter($query, $request);
            $this->applyDateRangeFilter($query, $request);
            $this->applySortBy($query, $request);

            $enquiries = $query->get()->map(function ($enquiry) {
                $vehicleImagePath = $enquiry->vehicle_image ?? '';
                $filename = basename($vehicleImagePath);
                $newpath = 'vehicles/images/small/' . $filename;
                $file = public_path('storage/' . $newpath);
                if (file_exists($file)) {
                    $vehicleImagePath = $newpath;
                }
                $enquiry->vehicle_image = uploadedAsset($vehicleImagePath);
                $enquiry->customer_name = ucwords($enquiry->customer_name);
                $enquiry->formatted_created_at = formatDateTime($enquiry->created_at, false);
                return $enquiry;
            });

            return [
                'code'    => 200,
                'success' => true,
                'message' => __('admin.common.default_retrieve_success'),
                'data'    => $enquiries
            ];
        } catch (\Exception $e) {
            return [
                'code'    => 500,
                'success' => false,
                'message' => __('admin.common.default_retrieve_error'),
            ];
        }
    }
    private function applyStatusFilter($query, Request $request): void
    {
        if (!$request->filled('status')) {
            return;
        }
        $status = $request->input('status');
        if (in_array($status, ['1', '2', '3'], true)) {
            $query->where('enquiries.status', $status);
        }
    }

    private function applySearchFilter($query, Request $request): void
    {
        if (!$request->filled('search')) {
            return;
        }
        $search = $request->input('search');
        $query->where(function ($q) use ($search) {
            $q->where('enquiries.customer_name', 'like', "%{$search}%")
                ->orWhere('enquiries.email', 'like', "%{$search}%")
                ->orWhere('enquiries.phone', 'like', "%{$search}%")
                ->orWhere('vehicle_info.name', 'like', "%{$search}%");
        });
    }

    private function applyDateRangeFilter($query, Request $request): void
    {
        if (!$request->filled('date_range')) {
            return;
        }
        $dates = explode(' - ', $request->input('date_range'));
        if (count($dates) !== 2) {
            return;
        }

        try {
            $startDate = \Carbon\Carbon::createFromFormat('m/d/Y', trim($dates[0]));
            $endDate = \Carbon\Carbon::createFromFormat('m/d/Y', trim($dates[1]));
            $query->whereBetween('enquiries.enquiry_date', [
                $startDate->startOfDay(),
                $endDate->endOfDay(),
            ]);
        } catch (\Exception $e) {
            Log::error('Date Range Filter Error: ' . $e->getMessage());
        }
    }

    private function applySortBy($query, Request $request): void
    {
        if (!$request->filled('sort_by')) {
            $query->orderBy('enquiries.enquiry_date', 'desc');
            return;
        }

        $sortBy = $request->input('sort_by');
        $options = [
            'ascending'   => fn($q) => $q->orderBy('vehicle_info.name', 'asc'),
            'descending'  => fn($q) => $q->orderBy('vehicle_info.name', 'desc'),
            'last_7_days' => fn($q) => $q->where('enquiries.enquiry_date', '>=', now()->subDays(7)),
            'last_month'  => fn($q) => $q->where('enquiries.enquiry_date', '>=', now()->subMonth()),
        ];

        ($options[$sortBy] ?? fn($q) => $q->orderBy('enquiries.enquiry_date', 'desc'))($query);
    }

    public function update(Request $request): array
    {
        try {
            $id = $request->id;
            /** @var \Modules\CarInfo\Models\Enquiry */
            $enquiry = Enquiry::findOrFail($id);

            $response = [
                'success' => true,
                'code'    => 200,
                'message' => __('admin.bookings.enquiry_update_success'),
                'data'    => null
            ];

            if ($enquiry->status == 3) {
                $response = [
                    'success' => false,
                    'code'    => 400,
                    'message' => __('admin.bookings.enquiry_already_closed'),
                ];
            } elseif ($enquiry->status == 1 && $request->status == 3) {
                $response = [
                    'success' => false,
                    'code'    => 400,
                    'message' => __('admin.bookings.enquiry_opened_before_cannot_be_closed'),
                ];
            } elseif ($enquiry->status == 2 && $request->status == 1) {
                $response = [
                    'success' => false,
                    'code'    => 400,
                    'message' => __('admin.bookings.enquiry_closed_before_cannot_be_opened'),
                ];
            } else {
                $enquiry->comment = $request->comment;
                $enquiry->status = $request->status;
                $enquiry->save();

                $response['data'] = $enquiry;
            }

        return $response;
        } catch (\Exception $e) {
            return [
              'code'    => 500,
              'success' => false,
              'message' => __('admin.common.default_update_error'),
              'error'   => $e->getMessage()
            ];
        }
    }

    public function delete(int $id): array
    {
        try {
            $enquiry = Enquiry::find($id);

            if (!$enquiry instanceof Enquiry) {
                $response = [
                    'code'    => 404,
                    'success' => false,
                    'message' => __('admin.common.no_data_found')
                ];
            } else {
                $enquiry->delete();
                $response = [
                    'code'    => 200,
                    'success' => true,
                    'message' => __('admin.bookings.enquiry_delete_success')
                ];
            }

            return $response;
        } catch (ModelNotFoundException $e) {
            return [
                'status'  => 'error',
                'code'    => 404,
                'message' => __('admin.common.no_data_found'),
            ];
        } catch (\Throwable $e) {
            return [
                'status'  => 'error',
                'code'    => 500,
                'message' => __('admin.common.default_delete_error'),
                'error'   => $e->getMessage(),
            ];
        }
    }
}
