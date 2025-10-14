<?php

namespace App\Repositories\Eloquent;

use App\Models\Country;
use App\Models\User;
use App\Models\UserDetail;
use App\Repositories\Contracts\UserProfileRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\CarInfo\Models\Enquiry;
use Modules\GeneralSetting\Models\Language;
use Modules\GeneralSetting\Models\TranslationLanguage;

class UserProfileRepository implements UserProfileRepositoryInterface
{
    public function getProfileSettings(): array
    {
        $user = Auth::guard('web')->user();
        $countries = Country::where('status', 1)->get();
        $seo_title = __('web.user.profile');

        return [
            'user'      => $user,
            'countries' => $countries,
            'seo_title' => $seo_title,
        ];
    }

    public function updateProfile(Request $request): array
    {
        try {
            $user = Auth::guard('web')->user();

            if ($user instanceof User) {
                $user->update([
                    'email'        => $request->email,
                    'phone_number' => $request->user_phone,
                ]);
            }

            $profilePhoto = null;

            if ($request->hasFile('profile_photo')) {
                $folder = "profile";
                $profilePhoto = $request->file('profile_photo')
                    ? uploadFile($request->file('profile_photo'), $folder)
                    : null;
            }

            UserDetail::updateOrCreate(
                ['user_id' => $user?->id],
                [
                    'first_name'    => $request->first_name,
                    'last_name'     => $request->last_name,
                    'mobile_number' => $request->user_phone,
                    'address'       => $request->address_line,
                    'country_id'    => $request->country,
                    'state_id'      => $request->state,
                    'city_id'       => $request->city,
                    'postal_code'   => $request->postal_code,
                    'profile_image' => $profilePhoto ?? $user->userDetail->profile_image ?? null,
                ]
            );

            $profileImage = UserDetail::where('user_id', $user?->id)->value('profile_image');

            return [
                'status'  => 'success',
                'code'    => 200,
                'message' => __('web.user.profile_updated_successfully'),
                'data'    => [
                    'profile_image' => uploadedAsset($profileImage, 'profile'),
                ],
            ];
        } catch (\Exception $e) {
            return [
                'status'  => 'error',
                'code'    => 500,
                'message' => __('web.user.error_occured'),
                'error'   => $e->getMessage(),
            ];
        }
    }

    public function getPreferenceSettings(): array
    {
        $languages = Language::select('languages.language_id')
            ->with([
                'transLang' => function ($query) {
                    $query->select('id', 'code', 'name');
                },
            ])
            ->where('languages.status', 1)
            ->get();

        $id = Auth::guard('web')->user()->id ?? 0;
        $preference = User::select('language_id', 'region_id')->where('id', $id)->first();
        $countries = Country::select('id', 'name')->where('status', 1)->get();
        $seo_title = __('web.user.preferences');

        return [
            'languages'  => $languages,
            'preference' => $preference,
            'countries'  => $countries,
            'seo_title'  => $seo_title,
        ];
    }

    public function updatePreference(Request $request): array
    {
        try {
            $id = Auth::guard('web')->user()->id ?? 0;
            $data = [];

            if ($request->has('language_id')) {
                $data['language_id'] = $request->language_id;
            } elseif ($request->has('region_id')) {
                $data['region_id'] = $request->region_id;
            }

            User::where('id', $id)->update($data);

            $language = TranslationLanguage::select('code')->where('id', $request->language_id)->first();
            session(['app_locale_user' => $language->code ?? 'en']);

            return [
                'status'  => 'success',
                'code'    => 200,
                'message' => __('web.user.preference_update_success'),
            ];
        } catch (\Throwable $e) {
            return [
                'status'  => 'error',
                'code'    => 500,
                'message' => __('web.common.default_update_error'),
            ];
        }
    }

    public function getPreferences(Request $request): array
    {
        try {
            $id = Auth::guard('web')->user()->id ?? $request->user_id;
            $data = User::select('language_id', 'region_id')->where('id', $id)->first();

            return [
                'status'  => 'success',
                'code'    => 200,
                'data'    => $data,
                'message' => __('web.common.default_retrieve_success'),
            ];
        } catch (\Throwable $e) {
            return [
                'status'  => 'error',
                'code'    => 500,
                'message' => __('web.common.default_retrieve_error'),
            ];
        }
    }

    public function storeEnquiry(Request $request): array
    {
        try {
            Enquiry::create([
                'car_id'          => $request->vehicle_id,
                'customer_name'   => $request->enquiry_name,
                'email'           => $request->enquiry_email,
                'phone'           => $request->international_phone_number,
                'enquiry_date'    => date('Y-m-d'),
                'enquiry_details' => $request->enquiry_message,
            ]);

            return [
                'status'  => 'success',
                'code'    => 200,
                'message' => __('web.user.enquiry_submitted_successfully'),
            ];
        } catch (\Throwable $th) {
            return [
                'status'  => 'error',
                'code'    => 500,
                'message' => __('web.user.error_occured'),
            ];
        }
    }
}

