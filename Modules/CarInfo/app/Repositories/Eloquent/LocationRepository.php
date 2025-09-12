<?php

namespace Modules\CarInfo\Repositories\Eloquent;

use App\Models\City;
use App\Models\Country;
use App\Models\State;
use App\Models\User;
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
        /** @var User|null $authUser */
        $authUser = currentUser();

        if (!$authUser) {
            return [
                'status'  => 'error',
                'code'    => 401,
                'message' => 'Unauthorized: User not authenticated.'
            ];
        }

        $id = $request->id;
        $successMessage = empty($id) ? __('admin.manage.location_create_success') : __('admin.manage.location_update_success');
        $errorMessage = empty($id) ? __('admin.common.default_create_error') : __('admin.common.default_update_error');

        try {
            [$location, $oldImage] = $this->prepareLocation($request, $authUser);

            $this->handleImageUpload($request, $location, $oldImage);
            $this->fillLocationData($request, $location);

            $location->save();

            $this->syncWorkingDays($request, $location);

            $response = [
                'status'  => 'success',
                'code'    => 200,
                'message' => $successMessage,
            ];
        } catch (ModelNotFoundException $e) {
            $response = [
                'status'  => 'error',
                'code'    => 404,
                'message' => __('admin.common.default_update_error'),
            ];
        } catch (\Throwable $th) {
            $response = [
                'status'  => 'error',
                'code'    => 500,
                'message' => $errorMessage,
            ];
        }

        return $response;
    }

    private function prepareLocation(Request $request, User $authUser): array
    {
        if (!$request->filled('id')) {
            $location = new Location();
            $location->language_id = $authUser->language_id;

            return [$location, ''];
        }

        $location = Location::find($request->id);

        if (!$location) {
            throw new ModelNotFoundException();
        }

        $location->status = $request->status === 'on' ? 1 : 0;
        $location->language_id = $request->language_id ?? $authUser->language_id;

        return [$location, $location->image ?? ''];
    }

    private function handleImageUpload(Request $request, Location $location, string $oldImage): void
    {
        $image = $request->file('image');

        if ($request->hasFile('image') && $image && $image->isValid()) {
            $location->image = $this->imageResizer->uploadFile($image, 'vehicles/location', $oldImage ?: null);
        }
    }

    private function fillLocationData(Request $request, Location $location): void
    {
        $location->name = $request->name;
        $location->email = $request->email;
        $location->phone = $request->international_phone_number;
        $location->address = $request->address;
        $location->country = $request->country;
        $location->state = $request->state;
        $location->city = $request->city;
        $location->pincode = $request->pincode;
    }

    private function syncWorkingDays(Request $request, Location $location): void
    {
        $days = $request->working_days ?? [];
        if (empty($days)) {
            return;
        }

        $existingDays = LocationWorkingDay::where('location_id', $location->id)->pluck('day')->toArray();
        $deleteDays = array_diff($existingDays, $days);

        if (!empty($deleteDays)) {
            LocationWorkingDay::where('location_id', $location->id)->whereIn('day', $deleteDays)->delete();
        }

        foreach ($days as $day) {
            $workingDay = LocationWorkingDay::firstOrNew([
                'location_id' => $location->id,
                'day'         => $day,
            ]);

            $workingDay->start_time = $this->parseTime($request->days[$day]['start'] ?? null);
            $workingDay->end_time = $this->parseTime($request->days[$day]['end'] ?? null);
            $workingDay->save();
        }
    }

    private function parseTime(?string $time): ?string
    {
        return $time ? Carbon::parse($time)->format('H:i:s') : null;
    }

    public function getAll(Request $request): array
    {
        try {
            $search = $request->input('search');
            $status = $request->input('status');
            /** @var \App\Models\User|null $authUser */
            $authUser = currentUser();
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
