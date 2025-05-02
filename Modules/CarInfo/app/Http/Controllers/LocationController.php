<?php

namespace Modules\CarInfo\Http\Controllers;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Modules\CarInfo\Models\Location;
use Modules\CarInfo\Models\LocationWorkingDay;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;

class LocationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        return view('carinfo::location.index');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function storeLocation(Request $request): JsonResponse
    {
        /** @var \App\Models\User|null $authUser */
        $authUser = current_user();
        if (!$authUser) {
            return response()->json([
                'status' => 'error',
                'code'   => 401,
                'message' => 'Unauthorized: User not authenticated.'
            ], 401);
        }
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:locations,name,' . $request->id . ',id,deleted_at,NULL',
            'email' => 'required|email|unique:locations,email,' . $request->id . ',id,deleted_at,NULL',
            'international_phone_number' => 'required|numeric|unique:locations,phone,' . $request->id . ',id,deleted_at,NULL',
            'address' => 'required',
            'country' => 'required',
            'state' => 'required',
            'city' => 'required',
            'pincode' => 'required|max:6',
        ], [
            'name.required' => __('admin.manage.name_required'),
            'name.unique' => __('admin.manage.name_unique'),
            'email.required' => __('admin.common.email_required'),
            'email.unique' => __('admin.common.email_unique'),
            'international_phone_number.required' => __('admin.common.phone_number_required'),
            'address.required' => __('admin.manage.address_required'),
            'country.required' => __('admin.manage.country_required'),
            'state.required' => __('admin.manage.state_required'),
            'city.required' => __('admin.manage.city_required'),
            'pincode.required' => __('admin.manage.pincode_required'),
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'code'   => 422,
                'errors' => $validator->errors()->toArray()
            ], 422);
        }
        $successMessage = empty($request->id) ? __('admin.manage.location_create_success') : __('admin.manage.location_update_success');
        $errorMessage = empty($request->id) ? __('admin.common.default_create_error') : __('admin.common.default_update_error');
        try {
            if (!$request->filled('id')) {
                /** @var \App\Models\User $authUser  */
                $location = new Location();
                $location->language_id = $authUser->language_id;
            } else {
                /** @var \Modules\CarInfo\Models\Location|null $location  */
                $location = Location::find($request->id);
                if (!$location) {
                    return response()->json([
                        'status' => 'error',
                        'code'   => 404,
                        'message' => __('admin.common.default_update_error')
                    ], 404);
                }
                $location->status = $request->status == 'on' ? 1 : 0;
                $location->language_id = $request->language_id ?? $authUser->language_id;
            }
            $oldImage = is_array($location->image) ? null : $location->image;
            $folderName = 'location';
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                if($image && $image->isValid()){
                    $location->image = uploadFile($image, $folderName, $oldImage);
                }
            }
            $location->name = $request->name;
            $location->email = $request->email;
            $location->phone = $request->international_phone_number;
            $location->address = $request->address;
            $location->country = $request->country;
            $location->state = $request->state;
            $location->city = $request->city;
            $location->pincode = $request->pincode;
            $location->save();

            if ($request->has('working_days') && count($request->working_days) > 0) {
                $oldWorkingDays = LocationWorkingDay::where('location_id', '=', $location->id)->pluck('day')->toArray();
                $newWorkingDays = $request->working_days;
                $deleteWorkingDays = array_diff($oldWorkingDays, $newWorkingDays);
                if (count($deleteWorkingDays) > 0) {
                    LocationWorkingDay::whereIn('day', $deleteWorkingDays)->where('location_id', '=', $location->id)->delete();
                }
                foreach ($request->working_days as $k => $day) {
                    if ($request->has('id') && $request->id == "") {
                        $workingDay = new LocationWorkingDay();
                    } else {
                        $workingDay = LocationWorkingDay::where('location_id', '=', $request->id)->where('day', '=', $day)->first();
                    }
                    if (!$workingDay) {
                        $workingDay = new LocationWorkingDay();
                    }
                    $workingDay->location_id = $location->id;
                    $workingDay->day = $day;
                    $workingDay->start_time = $request->days[$day]['start'] ? Carbon::parse($request->days[$day]['start'])->format('H:i:s') : null;
                    $workingDay->end_time = $request->days[$day]['end'] ? Carbon::parse($request->days[$day]['end'])->format('H:i:s') : null;
                    $workingDay->save();
                }
            }

            return response()->json([
                'status' => 'success',
                'code'   => 200,
                'message' => $successMessage
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'code'   => 500,
                'message' => $errorMessage,
            ], 500);
        }
    }

    /**
     * Retrieve all locations ordered by ID in descending order.
     *
     * @return \Illuminate\Http\JsonResponse
     */

    public function getLocations(Request $request): JsonResponse
    {
        $search = $request->input('search');
        $status = $request->input('status');
        /** @var \App\Models\User|null $authUser */
        $authUser = current_user();
        if (!$authUser) {
            return response()->json([
                'status' => 'error',
                'code'   => 401,
                'message' => 'Unauthorized: User not authenticated.'
            ], 401);
        }
        $language_id = $authUser->language_id;
        $query = Location::orderBy('id', 'desc')->where("language_id", $language_id)->with('workingDays');

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('address', 'LIKE', "%{$search}%");
            });
        }

        if ($status !== null && $status !== '') {
            $query->where('status', $status);
        }

        $locations = $query->get();

        $locations->map(function ($location) {
            $image = is_string($location->image) ? $location->image : null;
        
            $location->image_url = $image && file_exists(public_path('storage/' . $image))
                ? uploadedAsset($image)
                : null;
        
            return $location;
        });
        

        return response()->json([
            'status' => 'success',
            'code'   => 200,
            'data'   => $locations
        ]);
    }



    /**
     * Get a location by ID.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getLocation($id): JsonResponse
    {
        $location = Location::with('workingDays')->find($id);
        if(!$location) {
            return response()->json([
                'status' => 'error',
                'code'   => 404,
                'message' => __('admin.common.default_update_error')
            ], 404);
        }
        $location->working_days = $location->workingDays;
        $image = is_string($location->image) ? $location->image : null;

        $location->image = $image && file_exists(public_path('storage/' . $image))
            ? uploadedAsset($image)
            : null;

        return response()->json([
            'status' => 'success',
            'code'   => 200,
            'data' => $location
        ]);
    }


    /**
     * Delete a location by ID.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteLocation(Request $request): JsonResponse
    {
        try {
            $location = Location::where('id',$request->delete_id)->firstOrFail();
            $location->delete();
            return response()->json([
                'status' => 'success',
                'code'   => 200,
                'message' => __('admin.manage.location_delete_success')
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'code'   => 422,
                'message' => __('admin.common.default_delete_error')
            ], 422);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'success',
                'code'   => 422,
                'message' => __('admin.common.default_delete_error')
            ], 422);
        }
    }

    public function getCountries(Request $request): JsonResponse
    {
        try {
            $countries = DB::table('countries')->get(['id', 'name']);

            return response()->json([
                'code' => 200,
                'data' => $countries,
                'message' => __('Countries retrieved successfully.')
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => 'Error! while retrieving countries'
            ], 500);
        }
    }

    public function getStates(Request $request): JsonResponse
    {
        $countryId = $request->country_id;

        $validator = Validator::make($request->all(), [
            'country_id' => [
                'required',
            ],
        ], [
            'country_id.required' => __('Country id is required.'),
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'code' => 422,
                'message' => $validator->messages()->toArray()
            ], 422);
        }

        try {
            $states = DB::table('states')->where('country_id', $countryId)->get(['id', 'country_id', 'name']);

            return response()->json([
                'code' => 200,
                'data' => $states,
                'message' => __('States retrieved successfully.')
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => 'Error! while retrieving states'
            ], 500);
        }
    }

    public function getCities(Request $request): JsonResponse
    {
        $stateId = $request->state_id;

        $validator = Validator::make($request->all(), [
            'state_id' => [
                'required',
            ],
        ], [
            'state_id.required' => __('State id is required.'),
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'code' => 422,
                'message' => $validator->messages()->toArray()
            ], 422);
        }

        try {
            $cities = DB::table('cities')->where('state_id', $stateId)->get(['id', 'state_id', 'name']);

            return response()->json([
                'code' => 200,
                'data' => $cities,
                'message' => __('Cities retrieved successfully.')
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => 'Error! while retrieving cities'
            ], 500);
        }
    }

    public function getAllLocations(Request $request): JsonResponse
    {
        $orderBy = $request->order_by ?? 'asc';
        $search = $request->search ?? null;

        $locations = Location::when($search, function ($query) use ($search) {
            return $query->where('name', 'LIKE', "%{$search}%");
        })
            ->orderBy('id', $orderBy)
            ->where('status', 1)
            ->get(['id', 'name']);

        return response()->json([
            'status' => 'success',
            'code'   => 200,
            'data' => $locations
        ]);
    }
}
