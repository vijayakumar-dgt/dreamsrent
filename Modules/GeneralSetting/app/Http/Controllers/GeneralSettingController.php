<?php

namespace Modules\GeneralSetting\Http\Controllers;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Modules\GeneralSetting\Http\Requests\CookiesSettingsRequest;
use Modules\GeneralSetting\Http\Requests\ListCompanyRequest;
use Modules\GeneralSetting\Http\Requests\SettingListRequest;
use Modules\GeneralSetting\Http\Requests\StoreCookiesSettingsRequest;
use Modules\GeneralSetting\Http\Requests\StoreInvoiceSettingsRequest;
use Modules\GeneralSetting\Http\Requests\StoreLogoSettingsRequest;
use Modules\GeneralSetting\Http\Requests\StoreMaintenanceSettingsRequest;
use Modules\GeneralSetting\Http\Requests\StoreOtpSettingsRequest;
use Modules\GeneralSetting\Http\Requests\StoreRentalSettingsRequest;
use Modules\GeneralSetting\Http\Requests\StoreSeoSetupRequest;
use Modules\GeneralSetting\Http\Requests\UpdateThemeSettingsRequest;
use Modules\GeneralSetting\Models\GeneralSetting;
use Modules\GeneralSetting\Models\UserDevice;
use Modules\GeneralSetting\Models\IndustryType;
use Modules\GeneralSetting\Models\TeamSize;
use App\Models\User;
use App\Models\UserDetail;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Modules\GeneralSetting\Models\Language;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Intervention\Image\Laravel\Facades\Image;
use Modules\GeneralSetting\Http\Requests\StoreNotificationSettingsRequest;
use Modules\GeneralSetting\Repositories\NotificationSettingsRepository;
use Modules\GeneralSetting\Http\Requests\CompanySettingRequest;
use Modules\GeneralSetting\Repositories\GeneralSettingRepository;

class GeneralSettingController extends Controller
{
    protected GeneralSettingRepository $repository;

    public function __construct(GeneralSettingRepository $repository)
    {
        $this->repository = $repository;
    }
    public function index(): View
    {
        /** @var view-string $view */
        $view = 'generalsetting::index';
        return view($view);
    }

    public function logoSettings(Request $request): View
    {
        return view('generalsetting::website_settings.logo-setting');
    }

    public function company(Request $request): View
    {
        return view('generalsetting::company.index');
    }

    public function notifications(Request $request): View
    {
        return view('generalsetting::notifications-setting.index');
    }

    public function prefixes(Request $request): View
    {
        return view('generalsetting::website_settings.prefixes');
    }

    public function maintenance(Request $request): View
    {
        return view('generalsetting::maintenance.index');
    }

    public function seosetup(Request $request): View
    {
        return view('generalsetting::website_settings.seosetup');
    }

    public function gdprCookies(Request $request): View
    {
        $languages = Language::with('transLang')->get();
        return view('generalsetting::system_settings.gdpr-cookies', compact('languages'));
    }

    public function storage(Request $request): View
    {
        return view('generalsetting::other_settings.storage-setting');
    }

    public function invoiceSettings(Request $request): View
    {
        return view('generalsetting::app_settings.invoice-setting');
    }

    public function otpSettings(Request $request): View
    {
        return view('generalsetting::website_settings.otp-setting');
    }

    public function rentalSettings(Request $request): View
    {
        return view('generalsetting::rental_settings.rental-settings');
    }

    private function updateOrCreateRentalSetting(?string $key, ?string $value): bool
    {
        return (bool) GeneralSetting::updateOrCreate(
            ['key' => $key],
            ['value' => $value, 'group_id' => 20]
        );
    }

    public function storeRentalSettings(StoreRentalSettingsRequest $request, GeneralSettingRepository $repository): JsonResponse
    {
        try {
            $repository->saveRentalSettings($request->validated());

            return response()->json([
                'code' => 200,
                'message' => __('admin.general_settings.rental_saved_successfully'),
                'data' => []
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'code' => 500,
                'message' => __('admin.general_settings.retrive_error'),
                'error' => $e->getMessage()
            ], 500);
        }
    }

    private function updateOrCreateLogoSetting(?string $key, ?string $path, ?int $groupId): GeneralSetting
    {
        return GeneralSetting::updateOrCreate(
            ['key' => $key],
            ['value' => $path, 'group_id' => $groupId]
        );
    }

    public function storeLogoSettings(StoreLogoSettingsRequest $request): JsonResponse
    {
        try {
            $files = $request->only(['logo_image', 'favicon_image', 'small_image', 'dark_logo']);
            $paths = $this->repository->storeLogoSettings($files);

            return response()->json([
                'code' => 200,
                'message' => __('admin.general_settings.logo_update_success'),
                'data' => $paths,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => __('admin.general_settings.logo_setting_error'),
                'error' => $e->getMessage()
            ], 500);
        }
    }

    private function updateOrCreateOtpSetting(?string $key, ?string $value): bool
    {
        return (bool) GeneralSetting::updateOrCreate(
            ['key' => $key],
            ['value' => $value, 'group_id' => 15]
        );
    }

    public function storeOtpSettings(StoreOtpSettingsRequest $request, GeneralSettingRepository $repository): JsonResponse
    {
        try {
            $repository->storeOtpSettings($request->validated());

            return response()->json([
                'code' => 200,
                'message' => __('admin.general_settings.otp_success'),
                'data' => []
            ], 200);
        } catch (\Throwable $e) {
            return response()->json([
                'code' => 500,
                'message' => __('admin.general_settings.retrive_error'),
            ], 500);
        }
    }

    public function storageStatusUpdate(Request $request): JsonResponse
    {
        $request->validate([
            'storage_type' => 'required|in:local_storage,aws_storage',
            'status' => 'required|boolean',
        ]);

        try {
            $oppositeStorageType = $request->storage_type === 'local_storage' ? 'aws_storage' : 'local_storage';
            $oppositeStatus = $request->status == 1 ? 0 : 1;

            GeneralSetting::updateOrCreate(
                ['key' => $request->storage_type],
                ['value' => $request->status, 'group_id' => 8]
            );

            GeneralSetting::updateOrCreate(
                ['key' => $oppositeStorageType],
                ['value' => $oppositeStatus, 'group_id' => 8]
            );

            $message = $request->status == 1
                ? ucfirst(str_replace('_', ' ', $request->storage_type)) . ' activated'
                : ucfirst(str_replace('_', ' ', $request->storage_type)) . ' blocked';

            return response()->json(['success' => true, 'message' => $message]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('admin.general_settings.retrive_error'),
            ], 500);
        }
    }

    private function updateOrCreateAwsSetting(?string $key, ?string $value): bool
    {
        return (bool) GeneralSetting::updateOrCreate(
            ['key' => $key],
            ['value' => $value, 'group_id' => 8]
        );
    }

    public function storeAwsSettings(Request $request): JsonResponse
    {
        $rules = [
            'aws_access_key' => 'required|string',
            'aws_secret_key' => 'required|string',
            'aws_region' => 'required|string',
            'aws_bucket_name' => 'required|string',
            'aws_base_url' => 'required|url',
        ];

        $messages = [
            'aws_access_key.required' => __('The AWS access key field is required.'),
            'aws_secret_key.required' => __('The AWS secret access key field is required.'),
            'aws_region.required' => __('The AWS region field is required.'),
            'aws_bucket_name.required' => __('The AWS bucket field is required.'),
            'aws_base_url.required' => __('The AWS URL field is required.'),
            'aws_base_url.url' => __('The AWS URL must be a valid URL.'),
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return response()->json(['code' => 422, 'errors' => $validator->errors()], 422);
        }

        try {
            $settings = [
                'aws_access_key' => $request->aws_access_key,
                'aws_secret_key' => $request->aws_secret_key,
                'aws_region' => $request->aws_region,
                'aws_bucket_name' => $request->aws_bucket_name,
                'aws_base_url' => $request->aws_base_url
            ];

            foreach ($settings as $key => $value) {
                $saveSetting = $this->updateOrCreateAwsSetting($key, $value);
                if (!$saveSetting) {
                    throw new \Exception("Failed to save $key");
                }
            }

            return response()->json([
                'code' => 200,
                'message' => __('admin.general_settings.aws_success'),
                'data' => []
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => __('admin.general_settings.retrive_error'),
            ], 500);
        }
    }

    private function updateOrCreateInvoiceSetting(?string $key, ?string $value): bool
    {
        return (bool) GeneralSetting::updateOrCreate(
            ['key' => $key],
            ['value' => $value, 'group_id' => 9]
        );
    }

    public function storeInvoiceSettings(StoreInvoiceSettingsRequest $request, GeneralSettingRepository $repository): JsonResponse
    {
        try {
            $repository->saveInvoiceSettings($request->validated());

            return response()->json([
                'code' => 200,
                'message' => __('admin.general_settings.invoice_setting_success'),
                'data' => []
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'code' => 500,
                'message' => __('admin.general_settings.invoice_setting_error'),
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function store(CompanySettingRequest $request): JsonResponse
    {
        try {
            $this->repository->storeCompanySettings($request->validated());

            return response()->json([
                'status' => 'success',
                'code' => 200,
                'message' => __('admin.general_settings.company_setting_success')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'code' => 500,
                'message' => __('admin.general_settings.retrive_error'),
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function transferOwnership(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'owner_id' => 'required|exists:users,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'code' => 422,
                'message' => __('admin.general_settings.validation_error'),
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $newOwnerId = $request->owner_id;

            DB::transaction(function () use ($newOwnerId) {
                \App\Models\User::where('user_type', 1)->update(['user_type' => 4]);

                \App\Models\User::where('id', $newOwnerId)->update(['user_type' => 1]);
            });

            return response()->json([
                'status' => 'success',
                'code' => 200,
                'message' => __('admin.general_settings.ownership_transfer_success')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'code' => 500,
                'message' => __('admin.general_settings.retrive_error'),
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function storeNotificationSettings(StoreNotificationSettingsRequest $request): JsonResponse
    {
        try {
            $this->repository->saveNotificationSettings($request->validated());

            return response()->json([
                'status' => 'success',
                'code' => 200,
                'message' => __('admin.general_settings.notification_update_success'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'code' => 500,
                'message' => __('admin.general_settings.notification_error_update'),
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function storeSeoSetupSettings(StoreSeoSetupRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            $groupId = $request->group_id ?? null;
            $this->repository->storeSeoSettings($data, $groupId);

            return response()->json([
                'status' => 'success',
                'code' => 200,
                'message' => __('admin.general_settings.seo_update_success')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'code' => 500,
                'message' => __('admin.general_settings.seo_update_error'),
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function storeMaintenanceSettings(StoreMaintenanceSettingsRequest $request, GeneralSettingRepository $repository): JsonResponse
    {
        try {
            $repository->storeMaintenanceSettings($request->all());

            return response()->json([
                'status' => 'success',
                'code' => 200,
                'message' => __('admin.general_settings.maintanance_update_success')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'code' => 500,
                'message' => __('admin.general_settings.maintanance_update_error'),
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function storeCookiesSettings(StoreCookiesSettingsRequest $request, GeneralSettingRepository $repository ): JsonResponse
    {
        try {
            $repository->storeCookiesSettings($request->validated());

            return response()->json([
                'status' => 'success',
                'code' => 200,
                'message' => __('admin.general_settings.cookies_success'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'code' => 500,
                'message' => __('admin.general_settings.sretrive_error'),
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function cookiesSettingsList(CookiesSettingsRequest $request, GeneralSettingRepository $repository): JsonResponse
    {
        try {
            $settings = $repository->getCookiesSettings(
                $request->group_id,
                $request->language_id
            );

            return response()->json([
                'status' => 'success',
                'code' => 200,
                'message' => __('admin.general_settings.cookies_retrive_success'),
                'data' => $settings
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'code' => 500,
                'message' => __('admin.general_settings.retrive_error'),
                'error' => $e->getMessage()
            ]);
        }
    }

    public function listCompany(ListCompanyRequest $request): JsonResponse
    {
        try {
            $data = $this->repository->getCompanySettings($request->group_id);

            if (!$data) {
                return response()->json([
                    'status' => 'error',
                    'code' => 404,
                    'message' => __('admin.common.no_data_found'),
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'code' => 200,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'code' => 500,
                'message' => __('admin.general_settings.retrive_error'),
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function list(SettingListRequest $request): JsonResponse
    {
        try {
            $settings = $this->repository->getSettingsByGroup($request->validated()['group_id']);

            return response()->json([
                'status' => 'success',
                'code' => 200,
                'message' => __('admin.general_settings.setting_retrive_success'),
                'data' => $settings
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'code' => 500,
                'message' => __('admin.general_settings.retrive_error'),
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function security(Request $request): View
    {
        return view('generalsetting::security.index');
    }

    /**
     * Check if the provided current password matches the user's actual password.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkCurrentPassword(Request $request): JsonResponse
    {
        /** @var \App\Models\User $authUser */
        $authUser = Auth::guard('admin')->user();
        $password = $request->password;
        if ($authUser->password !== null && Hash::check($password, $authUser->password)) {
            return response()->json([
                'status' => 'success',
                'code' => 200,
                'message' => __('admin.general_settings.current_password_correct'),
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'code' => 422,
                'message' => __('admin.general_settings.current_password_incorrect'),
            ]);
        }
    }

    public function checkCurrentPhoneNumber(Request $request): JsonResponse
    {
        $currentPhoneNumber = $request->currentPhoneNumber;
        /** @var \App\Models\User $user */
        $user = current_user();
        if ($user->phone_number == "") {
            return response()->json([
                'status' => 'error',
                'code' => 422,
                'error' => "null",
                'message' => __('admin.general_settings.phone_number_not_set'),
            ], 422);
        } elseif ($user->phone_number != $currentPhoneNumber) {
            return response()->json([
                'status' => 'error',
                'code' => 422,
                'error' => "incorrect",
                'message' => __('admin.general_settings.phone_number_incorrect'),
            ], 422);
        } elseif ($user->phone_number == $currentPhoneNumber) {
            return response()->json([
                'status' => 'success',
                'code' => 200,
                'message' => __('admin.general_settings.phone_number_correct'),
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'code' => 422,
                'message' => __('admin.general_settings.phone_number_incorrect'),
            ], 422);
        }
    }

    public function updatePassword(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'new_password' => 'required|min:6',
            'confirm_password' => 'required|same:new_password',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'code' => 422,
                'message' => __('admin.general_settings.validation_error'),
                'errors' => $validator->errors()->toArray()
            ], 422);
        }

        /** @var \App\Models\User $user */
        $user = current_user();

        if ($user->password !== null && !Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'status' => 'error',
                'code' => 500,
                'message' => __('admin.general_settings.current_password_incorrect'),
                'errors' => $validator->errors()->toArray()
            ], 500);
        }

        $user->password = Hash::make($request->new_password);
        $user->last_password_changed_at = now();
        $user->save();

        return response()->json([
            'status' => 'success',
            'code' => 200,
            'message' => __('admin.general_settings.password_updated_successfully'),
        ]);
    }

    /**
     * Update the authenticated user's phone number.
     *
     * Validates the new phone number and updates it in the database
     * if the validation passes. Returns a JSON response indicating
     * success or failure of the update operation.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updatePhoneNumber(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'new_phonenumber' => 'required|unique:users,phone_number',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'code' => 200,
                'message' => $validator->errors()->first()
            ], 200);
        }
        $currentPassword = $request->phone_current_password;
        /** @var \App\Models\User $authUser */
        $authUser = Auth::guard('admin')->user();

        if ($authUser->password !== null && !Hash::check($currentPassword, $authUser->password)) {
            return response()->json([
                'status' => 'error',
                'code' => 200,
                'message' => __('admin.general_settings.current_password_incorrect')
            ]);
        }
        $currentPhoneNumber = $request->current_phonenumber;
        if ($authUser->phone_number != $currentPhoneNumber && $authUser->phone_number != "") {
            return response()->json([
                'status' => 'error',
                'code' => 200,
                'message' => __('admin.general_settings.phone_number_incorrect')
            ]);
        }
        $authUser->phone_number = $request->new_phonenumber;
        $authUser->save();

        return response()->json([
            'status' => 'success',
            'code' => 200,
            'message' => __('admin.general_settings.phone_number_updated_successfully'),
        ]);
    }

    public function updateEmail(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'new_email' => 'required|email|unique:users,email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'code' => 422,
                'message' => $validator->errors()->first()
            ], 422);
        }
        $current_email = $request->current_email;
        /** @var \App\Models\User $authUser */
        $authUser = Auth::guard('admin')->user();
        if ($authUser->email != $current_email) {
            return response()->json([
                'status' => 'error',
                'code' => 200,
                'message' => __('admin.general_settings.current_email_incorrect')
            ]);
        }
        $email_current_password = $request->email_current_password;
        if ($authUser->password !== null && !Hash::check($email_current_password, $authUser->password)) {
            return response()->json([
                'status' => 'error',
                'code' => 200,
                'message' => __('admin.general_settings.current_password_incorrect')
            ]);
        }
        $authUser->email = $request->new_email;
        $authUser->save();

        return response()->json([
            'status' => 'success',
            'code' => 200,
            'message' => __('admin.general_settings.email_updated_successfully'),
        ]);
    }

    public function getSecuritySettings(): JsonResponse
    {
        /** @var \App\Models\User $authUser */
        $authUser = Auth::guard('admin')->user();
        $userDevices = UserDevice::where('user_id', $authUser->id)->orderBy('created_at', 'desc')->take(5)->get()->map(function ($device) {
            return [
                'id' => $device->id,
                'device_type' => $device->device_type,
                'browser' => $device->browser,
                'os' => $device->os,
                'ip_address' => $device->ip_address,
                'location' => $device->location,
                'date' => formatDateTime($device->created_at)
            ];
        });
        $response = [
            'user' => Auth::user(),
            'last_password_changed_at' => $authUser->last_password_changed_at ? formatDateTime($authUser->last_password_changed_at) : "null",
            'devices' => $userDevices
        ];
        return response()->json([
            'status' => 'success',
            'code' => 200,
            'data' => $response
        ]);
    }

    public function logoutDevice(Request $request): JsonResponse
    {
        /** @var \App\Models\User $authUser */
        $authUser = Auth::guard('admin')->user();
        if ($request->isAll === "true") {
            UserDevice::where('user_id', $authUser->id)->delete();
            Auth::guard('admin')->logout();
            return response()->json([
                'status' => 'success',
                'code' => 200,
                'message' => __('admin.general_settings.all_device_removed_successfully'),
            ]);
        }
        /** @var \Modules\GeneralSetting\Models\UserDevice $device */
        $device = UserDevice::find($request->id);
        $device->delete();
        return response()->json([
            'status' => 'success',
            'code' => 200,
            'message' => __('admin.general_settings.device_removed_successfully'),
        ]);
    }

    public function updateGoogleAuth(Request $request): JsonResponse
    {
        try {
            /** @var \App\Models\User $user */
            $user = Auth::guard('admin')->user();
            $user->google_auth_enabled = $request->googleAuthEnabled === "true" ? 1 : 0;
            $user->save();
            $message = $user->google_auth_enabled === 1 ? "Google Authentication Enabled Successfully" : "Google Authentication Disabled Successfully";
            return response()->json([
                'status' => 'success',
                'code' => 200,
                'message' => $message
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'code' => 422,
                'message' => 'Something went wrong',
                'error' => $th->getMessage()
            ]);
        }
    }

    public function updatePrefixes(Request $request): JsonResponse
    {
        try {
            $this->repository->updatePrefixes($request->all(), $request->group_id);

            return response()->json([
                'status' => 'success',
                'code' => 200,
                'message' => __('admin.general_settings.prefix_settings_update_success'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'code' => 500,
                'message' => __('admin.common.default_update_error'),
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function aiConfiguration(Request $request): View
    {
        return view('generalsetting::website_settings.ai_configuration');
    }

    public function updateAiConfiguration(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'group_id' => 'required|integer',
            'ai_api_key' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'code' => 422,
                'message' => __('admin.general_settings.validation_error'),
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $settings = $request->all();

            foreach ($settings as $key => $value) {
                if ($key != 'group_id') {
                    GeneralSetting::updateOrCreate(
                        ['key' => $key],
                        [
                            'value' => $value,
                            'group_id' => $request->group_id
                        ]
                    );
                }
            }

            return response()->json([
                'status' => 'success',
                'code' => 200,
                'message' => __('admin.general_settings.ai_configuration_update_success')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'code' => 500,
                'message' => __('admin.common.default_update_error'),
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function paymentIndex(Request $request): View
    {
        return view('generalsetting::payment.index');
    }

    public function updatepaymentSettings(Request $request): JsonResponse
    {
        $settings = $request->except(['_token']);

        try {
            foreach ($settings as $key => $value) {
                if ($key != 'group_id') {
                    GeneralSetting::updateOrCreate(
                        ['key' => $key],
                        [
                            'value' => $value,
                            'group_id' => $request->group_id
                        ],
                    );
                }

                if ($key == 'paypal_key') {
                    $this->updateEnvFile('PAYPAL_SANDBOX_CLIENT_ID', $value);
                }

                if ($key == 'paypal_secret') {
                    $this->updateEnvFile('PAYPAL_SANDBOX_CLIENT_SECRET', $value);
                }

                if ($key == 'stripe_key') {
                    $this->updateEnvFile('STRIPE_KEY', $value);
                }
                if ($key == 'stripe_secret') {
                    $this->updateEnvFile('STRIPE_SECRET', $value);
                }
            }

            return response()->json([
                'code' => 200,
                'message' => __('admin.general_settings.payment_updated_successfull'),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => __('admin.general_settings.global_settings_error') . $e->getMessage()
            ], 500);
        }
    }

    private function updateEnvFile(string $key, string $value): void
    {
        $path = base_path('.env');

        if (file_exists($path)) {
            $envContent = file_get_contents($path);

            if ($envContent === false) {
                return;
            }

            $pattern = "/^{$key}=.*/m";

            if (preg_match($pattern, $envContent)) {
                $envContent = preg_replace($pattern, "{$key}={$value}", $envContent);
            } else {
                $envContent .= "\n{$key}={$value}";
            }

            file_put_contents($path, $envContent);
        }
    }


    public function updatepaymentStatus(Request $request): JsonResponse
    {
        $request->validate([
            'key' => 'required|string',
            'value' => 'required|in:0,1',
            'group_id' => 'required|integer',
        ]);

        GeneralSetting::updateOrCreate(
            ['key' => $request->key, 'group_id' => $request->group_id],
            ['value' => $request->value]
        );

        return response()->json(['success' => true, 'message' => __('admin.general_settings.payment_updated_successfull')]);
    }

    public function paymentList(Request $request): JsonResponse
    {
        $orderBy = $request->order_by ?? 'desc';

        try {
            $data = GeneralSetting::orderBy('id', $orderBy)->where('group_id', 13)->get();

            return response()->json([
                'code' => 200,
                'message' => __('admin.general_settings.general_settings_success'),
                'data' => $data,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => __('admin.general_settings.global_settings_error'),
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function themeSettings(Request $request): View
    {
        return view('generalsetting::website_settings.theme_settings');
    }

    public function updateThemeSettings(UpdateThemeSettingsRequest $request, GeneralSettingRepository $repository): JsonResponse
    {
        try {
            $repository->updateThemeSettings($request->validated());

            return response()->json([
                'status' => 'success',
                'code' => 200,
                'message' => __('admin.general_settings.theme_update_success')
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'code' => 500,
                'message' => __('admin.common.default_update_error'),
            ], 500);
        }
    }
}
