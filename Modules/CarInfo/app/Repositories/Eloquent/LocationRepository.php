<?php

namespace Modules\CarInfo\Repositories\Eloquent;

use App\Models\City;
use App\Models\Country;
use App\Models\State;
use App\Services\ImageResizer;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Modules\CarInfo\Models\Location;
use Modules\CarInfo\Models\LocationWorkingDay;
use Modules\CarInfo\Repositories\Contracts\LocationRepositoryInterface;

class LocationRepository implements LocationRepositoryInterface
{
    protected ImageResizer $imageResizer;

    public function __construct(ImageResizer $imageResizer)
    {
        $this->imageResizer = $imageResizer;
    }

    public function store(Request $request): array
    {
        /** @var \App\Models\User|null $authUser */
        $authUser = current_user();
        if (!$authUser) {
            return [
                'status'  => 'error',
                'code'    => 401,
                'message' => 'Unauthorized: User not authenticated.'
            ];
        }

        $successMessage = empty($request->id) ? __('admin.manage.location_create_success') : __('admin.manage.location_update_success');
        $errorMessage = empty($request->id) ? __('admin.common.default_create_error') : __('admin.common.default_update_error');
        try {
            $oldImage = '';
            if (!$request->filled('id')) {
                /** @var \App\Models\User $authUser  */
                $location = new Location();
                $location->language_id = $authUser->language_id;
            } else {
                /** @var \Modules\CarInfo\Models\Location|null $location  */
                $location = Location::find($request->id);
                $oldImage = $location->image ?? '';
                if (!$location) {
                    return [
                        'status'  => 'error',
                        'code'    => 404,
                        'message' => __('admin.common.default_update_error')
                    ];
                }
                $location->status = $request->status == 'on' ? 1 : 0;
                $location->language_id = $request->language_id ?? $authUser->language_id;
            }

            $folderName = 'vehicles/location';
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                if ($image && $image->isValid()) {
                    $location->image = $this->imageResizer->uploadFile($image, $folderName, $oldImage ?? null);
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

    public function getAll(Request $request): array
    {
        try {
            $search = $request->input('search');
            $status = $request->input('status');
            /** @var \App\Models\User|null $authUser */
            $authUser = current_user();
            if (!$authUser) {
                return [
                    'status'  => 'error',
                    'code'    => 401,
                    'message' => 'Unauthorized: User not authenticated.'
                ];
            }
            $language_id = $authUser->language_id;
            $query = Location::orderBy('name', 'asc')->where("language_id", $language_id)->with('workingDays');

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
                $location->image_url = uploadedAsset($image ?? '', 'default');

                return $location;
            });

            return [
                'status' => 'success',
                'code'   => 200,
                'data'   => $locations
            ];
        } catch (\Exception $e) {
            return [
                'code'    => 500,
                'message' => __('admin.common.default_retrieve_error'),
                'error'   => $e->getMessage(),
            ];
        }
    }

    public function getById(int $id): array
    {
        $location = Location::with('workingDays')->find($id);
        if (!$location) {
            return [
                'status'  => 'error',
                'code'    => 404,
                'message' => __('admin.common.no_data_found')
            ];
        }
        $location->working_days = $location->workingDays;
        $image = is_string($location->image) ? $location->image : null;

        $location->image = uploadedAsset($image ?? '', 'default');

        return [
            'status' => 'success',
            'code'   => 200,
            'data'   => $location
        ];
    }

    public function delete(int $id): array
    {
        try {
            /** @var \Modules\CarInfo\Models\Location $location */
            $location = Location::findOrFail($id);
            $location->delete();

            return [
                'status'  => 'success',
                'code'    => 200,
                'message' => __('admin.manage.location_delete_success')
            ];
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
            ];
        }
    }

    public function getCountries(Request $request): array
    {
        try {
            $countries = Country::where('status', 1)->get(['id', 'name']);

            return [
                'code'    => 200,
                'data'    => $countries,
                'message' => __('Countries retrieved successfully.')
            ];
        } catch (\Exception $e) {
            return [
                'code'    => 500,
                'message' => 'Error! while retrieving countries'
            ];
        }
    }

    public function getStates(int $countryId): array
    {
        try {
            $states = State::where('status', 1)->where('country_id', $countryId)->get(['id', 'country_id', 'name']);

            return [
                'code'    => 200,
                'data'    => $states,
                'message' => __('States retrieved successfully.')
            ];
        } catch (\Exception $e) {
            return [
                'code'    => 500,
                'message' => 'Error! while retrieving states'
            ];
        }
    }

    public function getCities(int $stateId): array
    {
        try {
            $cities = City::where('status', 1)->where('state_id', $stateId)->get(['id', 'state_id', 'name']);

            return [
                'code'    => 200,
                'data'    => $cities,
                'message' => __('Cities retrieved successfully.')
            ];
        } catch (\Exception $e) {
            return [
                'code'    => 500,
                'message' => 'Error! while retrieving cities'
            ];
        }
    }

    public function getAllLocations(Request $request): array
    {
        $orderBy = $request->order_by ?? 'asc';
        $search = $request->search ?? null;

        $locations = Location::when($search, function ($query) use ($search) {
            return $query->where('name', 'LIKE', "%{$search}%");
        })
            ->orderBy('id', $orderBy)
            ->where('status', 1)
            ->get(['id', 'name']);

        return [
            'status' => 'success',
            'code'   => 200,
            'data'   => $locations
        ];
    }
}
