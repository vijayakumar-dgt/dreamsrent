<?php

namespace Modules\CarInfo\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Modules\CarInfo\Models\Enquiry;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;

class EnquireController extends Controller
{
    public function index(): View
    {
        $cars = DB::table('vehicle_info')->get(['id', 'name']);
        return view('carinfo::car_enquires.index', compact('cars'));
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'assigned_cars' => 'required|array',
            'assigned_cars.*' => 'exists:vehicle_info,id',
            'customer_name' => 'required|string|max:100|regex:/^[a-zA-Z\s]+$/',
            'email' => 'required|email|max:100',
            'phone_number' => 'required|digits_between:10,15',
            'enquiry_details' => 'required|string|max:500'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

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

            return response()->json([
                'code'   => 200,
                'success' => true,
                'message' => 'Enquiry submitted successfully!',
                'data' => $enquiries
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'code'   => 500,
                'success' => false,
                'message' => 'Failed to submit enquiry.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function list(Request $request): JsonResponse
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
                    return $enquiry;
                });

            return response()->json([
                'code' => 200,
                'success' => true,
                'message' => __('admin.common.default_retrieve_success'),
                'data' => $enquiries
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'success' => false,
                'message' => __('admin.common.default_retrieve_error'),
                'error' => $e->getMessage()
            ]);
        }
    }
   public function update(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'comment' => 'required|string|max:500',
            'status' => 'required|in:1,2,3',
        ]);

        $id = $request->id;

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            /** @var \Modules\CarInfo\Models\Enquiry */
            $enquiry = Enquiry::findOrFail($id);

            if ($enquiry->status == 3) {
                return response()->json([
                    'success' => false,
                    'message' => 'This enquiry has already been closed and cannot be updated.',
                ], 400);
            }

            if ($enquiry->status == 1 && $request->status == 3) {
                return response()->json([
                    'success' => false,
                    'message' => 'Enquiry must be opened before it can be closed.',
                ], 400);
            }

            if ($enquiry->status == 2 && $request->status == 1) {
            return response()->json([
                    'success' => false,
                    'message' => 'The enquiry has already been opened and cannot be reverted to not opened.',
                ], 400);
            }

            $enquiry->comment = $request->comment;
            $enquiry->status = $request->status;
            $enquiry->save();

            return response()->json([
                'code' => 200,
                'success' => true,
                'message' => 'Enquiry updated successfully!',
                'data' => $enquiry
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'success' => false,
                'message' => 'Failed to update enquiry.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function delete(Request $request): JsonResponse
    {
        try {
            $id = $request->id;
            $enquiry = Enquiry::find($id);

            if (!$enquiry instanceof Enquiry) {
                return response()->json([
                    'code'    => 404,
                    'success' => false,
                    'message' => 'Enquiry not found.'
                ], 404);
            }

            $enquiry->delete();

            return response()->json([
                'code'    => 200,
                'success' => true,
                'message' => __('admin.bookings.enquiry_delete_success')
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'code'    => 500,
                'success' => false,
                'message' => __('admin.common.default_delete_error'),
                'error'   => $e->getMessage()
            ], 500);
        }
    }
}
