<?php

namespace Modules\CarInfo\Repositories\Eloquent;

use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Modules\CarInfo\Models\Enquiry;
use Modules\CarInfo\Models\VehicleInfo;
use Modules\CarInfo\Repositories\Contracts\EnquiryRepositoryInterface;

class EnquiryRepository implements EnquiryRepositoryInterface
{
    public function store(Request $request): array
    {
        try {
            $enquiries = [];

            foreach ($request->assigned_cars as $carId) {
                $enquiries[] = Enquiry::create([
                    'car_id' => $carId,
                    'customer_name' => $request->customer_name,
                    'email' => $request->email,
                    'phone' => $request->phone_number,
                    'enquiry_date' => now(),
                    'enquiry_details' => $request->enquiry_details,
                    'status' => '1'
                ]);
            }

            return [
                'code'   => 200,
                'success' => true,
                'message' => __('admin.bookings.enquiry_create_success'),
                'data' => $enquiries
            ];
        } catch (\Exception $e) {
            return [
                'code'   => 500,
                'success' => false,
                'message' => __('admin.common.default_create_error'),
            ];
        }
    }

    public function getAll(Request $request): array
    {
        try {
            $enquiries = Enquiry::select('enquiries.*', 'vehicle_info.name as car_name', 'vehicle_info.vehicle_image', 'vehicle_info.type_id', 'cartypes.name as type_name')
                ->join('vehicle_info', 'enquiries.car_id', '=', 'vehicle_info.id')
                ->join('cartypes', 'vehicle_info.type_id', '=', 'cartypes.id')
                ->when($request->filled('status'), function ($query) use ($request) {
                    $status = $request->input('status');
                    if ($status === '1') {
                        $query->where('enquiries.status', 1); // Not Opened
                    } elseif ($status === '2') {
                        $query->where('enquiries.status', 2); // Opened
                    } elseif ($status === '3') {
                        $query->where('enquiries.status', 3); // Closed
                    }
                })
                ->when($request->filled('search'), function ($query) use ($request) {
                    $search = $request->input('search');
                    $query->where(function ($q) use ($search) {
                        $q->where('enquiries.customer_name', 'like', "%{$search}%")
                            ->orWhere('enquiries.email', 'like', "%{$search}%")
                            ->orWhere('enquiries.phone', 'like', "%{$search}%")
                            ->orWhere('vehicle_info.name', 'like', "%{$search}%");
                    });
                })
                ->when($request->filled('date_range'), function ($query) use ($request) {
                    $range = $request->input('date_range');
                    $dates = explode(' - ', $range);
                    if (count($dates) === 2) {
                        try {
                            $startDate = \Carbon\Carbon::createFromFormat('m/d/Y', trim($dates[0]));
                            $endDate = \Carbon\Carbon::createFromFormat('m/d/Y', trim($dates[1]));

                            if ($startDate && $endDate) {
                                $start = $startDate->startOfDay();
                                $end = $endDate->endOfDay();
                                $query->whereBetween('enquiries.enquiry_date', [$start, $end]);
                            }
                        } catch (\Exception $e) {
                        }
                    }
                })
                ->when($request->filled('sort_by'), function ($query) use ($request) {
                    $sortBy = $request->input('sort_by');
                    switch ($sortBy) {
                        case 'ascending':
                            $query->orderBy('vehicle_info.name', 'asc');
                            break;
                        case 'descending':
                            $query->orderBy('vehicle_info.name', 'desc');
                            break;
                        case 'last_7_days':
                            $query->where('enquiries.enquiry_date', '>=', now()->subDays(7));
                            break;
                        case 'last_month':
                            $query->where('enquiries.enquiry_date', '>=', now()->subMonth());
                            break;
                        default:
                            $query->orderBy('enquiries.enquiry_date', 'desc');
                            break;
                    }
                })
                ->get()->map(function ($enquiry) {
                    $enquiry->vehicle_image = uploadedAsset($enquiry->vehicle_image ?? null, 'default');
                    $enquiry->customer_name = ucwords($enquiry->customer_name);
                    $enquiry->formatted_created_at = formatDateTime($enquiry->created_at, false);
                    return $enquiry;
                });

            return [
                'code' => 200,
                'success' => true,
                'message' => __('admin.common.default_retrieve_success'),
                'data' => $enquiries
            ];
        } catch (\Exception $e) {
            return [
                'code' => 500,
                'success' => false,
                'message' => __('admin.common.default_retrieve_error'),
            ];
        }
    }

    public function update(Request $request): array
    {
        try {
            $id = $request->id;
            /** @var \Modules\CarInfo\Models\Enquiry */
            $enquiry = Enquiry::findOrFail($id);

            if ($enquiry->status == 3) {
                return [
                    'success' => false,
                    'code' => 400,
                    'message' => __('admin.bookings.enquiry_already_closed'),
                ];
            }

            if ($enquiry->status == 1 && $request->status == 3) {
                return [
                    'success' => false,
                    'code' => 400,
                    'message' => __('admin.bookings.enquiry_opened_before_cannot_be_closed'),
                ];
            }

            if ($enquiry->status == 2 && $request->status == 1) {
                return [
                    'success' => false,
                    'code' => 400,
                    'message' => __('admin.bookings.enquiry_closed_before_cannot_be_opened'),
                ];
            }

            $enquiry->comment = $request->comment;
            $enquiry->status = $request->status;
            $enquiry->save();

            return [
                'code' => 200,
                'success' => true,
                'message' => __('admin.bookings.enquiry_update_success'),
                'data' => $enquiry
            ];
        } catch (\Exception $e) {
            return [
              'code' => 500,
              'success' => false,
              'message' => __('admin.common.default_update_error'),
              'error' => $e->getMessage()
            ];
        }
    }

    public function delete(int $id): array
    {
        try {
            $enquiry = Enquiry::find($id);

            if (!$enquiry instanceof Enquiry) {
                return [
                    'code'    => 404,
                    'success' => false,
                    'message' => __('admin.common.no_data_found')
                ];
            }
            $enquiry->delete();

            return [
                'code'    => 200,
                'success' => true,
                'message' => __('admin.bookings.enquiry_delete_success')
            ];
        } catch (ModelNotFoundException $e) {
            return [
                'status' => 'error',
                'code'   => 404,
                'message' => __('admin.common.no_data_found'),
            ];
        } catch (\Throwable $e) {
            return [
                'status' => 'error',
                'code'   => 500,
                'message' => __('admin.common.default_delete_error'),
                'error'   => $e->getMessage(),
            ];
        }
    }
}