<?php

namespace Modules\GeneralSetting\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Modules\GeneralSetting\Http\Requests\CompanySettingRequest;
use Modules\GeneralSetting\Http\Requests\CookiesSettingsRequest;
use Modules\GeneralSetting\Http\Requests\ListCompanyRequest;
use Modules\GeneralSetting\Http\Requests\SettingListRequest;
use Modules\GeneralSetting\Http\Requests\StorageStatusUpdateRequest;
use Modules\GeneralSetting\Http\Requests\StoreAwsSettingsRequest;
use Modules\GeneralSetting\Http\Requests\StoreCookiesSettingsRequest;
use Modules\GeneralSetting\Http\Requests\StoreInvoiceSettingsRequest;
use Modules\GeneralSetting\Http\Requests\StoreLogoSettingsRequest;
use Modules\GeneralSetting\Http\Requests\StoreMaintenanceSettingsRequest;
use Modules\GeneralSetting\Http\Requests\StoreNotificationSettingsRequest;
use Modules\GeneralSetting\Http\Requests\StoreOtpSettingsRequest;
use Modules\GeneralSetting\Http\Requests\StoreRentalSettingsRequest;
use Modules\GeneralSetting\Http\Requests\StoreSeoSetupRequest;
use Modules\GeneralSetting\Http\Requests\UpdateEmailRequest;
use Modules\GeneralSetting\Http\Requests\UpdatePasswordRequest;
use Modules\GeneralSetting\Http\Requests\UpdatePaymentSettingsRequest;
use Modules\GeneralSetting\Http\Requests\UpdatePaymentStatusRequest;
use Modules\GeneralSetting\Http\Requests\UpdatePhoneNumberRequest;
use Modules\GeneralSetting\Http\Requests\UpdateThemeSettingsRequest;
use Modules\GeneralSetting\Models\Language;
use Modules\GeneralSetting\Repositories\Contracts\AppearanceSettingRepositoryInterface;
use Modules\GeneralSetting\Repositories\Contracts\BusinessSettingRepositoryInterface;
use Modules\GeneralSetting\Repositories\Contracts\CompanySettingRepositoryInterface;
use Modules\GeneralSetting\Repositories\Contracts\ContentSettingRepositoryInterface;
use Modules\GeneralSetting\Repositories\Contracts\PaymentSettingRepositoryInterface;
use Modules\GeneralSetting\Repositories\Contracts\SecuritySettingRepositoryInterface;
use Modules\GeneralSetting\Exceptions\StorageStatusUpdateException;
use Modules\GeneralSetting\Exceptions\AwsSettingsUpdateException;
use Modules\GeneralSetting\Exceptions\PaymentSettingsUpdateException;
use Modules\GeneralSetting\Exceptions\PaymentStatusUpdateException;

class GeneralSettingController extends Controller
{
    public function __construct(
        private readonly CompanySettingRepositoryInterface $companySettings,
        private readonly AppearanceSettingRepositoryInterface $appearanceSettings,
        private readonly BusinessSettingRepositoryInterface $businessSettings,
        private readonly SecuritySettingRepositoryInterface $securitySettings,
        private readonly ContentSettingRepositoryInterface $contentSettings,
        private readonly PaymentSettingRepositoryInterface $paymentSettings
    ) {
    }

    public function index(): View
    {
        /** @var view-string $view */
        $view = 'generalsetting::index';
        return view($view);
    }

    public function logoSettings(): View
    {
        return view('generalsetting::website_settings.logo-setting');
    }

    public function company(): View
    {
        return view('generalsetting::company.index');
    }

    public function notifications(): View
    {
        return view('generalsetting::notifications-setting.index');
    }

    public function prefixes(): View
    {
        return view('generalsetting::website_settings.prefixes');
    }

    public function maintenance(): View
    {
        return view('generalsetting::maintenance.index');
    }

    public function seosetup(): View
    {
        return view('generalsetting::website_settings.seosetup');
    }

    public function gdprCookies(): View
    {
        $languages = Language::with('transLang')->get();
        return view('generalsetting::system_settings.gdpr-cookies', compact('languages'));
    }

    public function storage(): View
    {
        return view('generalsetting::other_settings.storage-setting');
    }

    public function invoiceSettings(): View
    {
        return view('generalsetting::app_settings.invoice-setting');
    }

    public function otpSettings(): View
    {
        return view('generalsetting::website_settings.otp-setting');
    }

    public function rentalSettings(): View
    {
        return view('generalsetting::rental_settings.rental-settings');
    }

    public function storeRentalSettings(StoreRentalSettingsRequest $request): JsonResponse
    {
        try {
            $this->businessSettings->saveRentalSettings($request->validated());

            return response()->json([
                'code'    => 200,
                'message' => __('admin.general_settings.rental_saved_successfully'),
                'data'    => []
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'code'    => 500,
                'message' => __('admin.general_settings.retrive_error'),
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    public function storeLogoSettings(StoreLogoSettingsRequest $request): JsonResponse
    {
        try {
            $files = $request->only(['logo_image', 'favicon_image', 'small_image', 'dark_logo']);
            $paths = $this->appearanceSettings->storeLogoSettings($files);

            return response()->json([
                'code'    => 200,
                'message' => __('admin.general_settings.logo_update_success'),
                'data'    => $paths,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code'    => 500,
                'message' => __('admin.general_settings.logo_setting_error'),
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    public function storeOtpSettings(StoreOtpSettingsRequest $request): JsonResponse
    {
        try {
            $this->securitySettings->storeOtpSettings($request->validated());

            return response()->json([
                'code'    => 200,
                'message' => __('admin.general_settings.otp_success'),
                'data'    => []
            ], 200);
        } catch (\Throwable $e) {
            return response()->json([
                'code'    => 500,
                'message' => __('admin.general_settings.retrive_error'),
            ], 500);
        }
    }

    public function storageStatusUpdate(StorageStatusUpdateRequest $request): JsonResponse
    {
        try {
            $success = $this->paymentSettings->updateStorageStatus(
                $request->storage_type,
                (bool) $request->status
            );

            if (!$success) {
                throw new StorageStatusUpdateException(__('admin.general_settings.update_failed'));
            }

            $action = $request->status == 1 ? 'activated' : 'blocked';
            $message = ucfirst(str_replace('_', ' ', $request->storage_type)) . " {$action}";

            return response()->json([
                'success' => true,
                'message' => $message
            ]);
        } catch (StorageStatusUpdateException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => __('admin.general_settings.retrive_error'),
            ], 500);
        }
    }

    public function storeAwsSettings(StoreAwsSettingsRequest $request): JsonResponse
    {
        try {
            $settings = [
                'aws_access_key'  => $request->aws_access_key,
                'aws_secret_key'  => $request->aws_secret_key,
                'aws_region'      => $request->aws_region,
                'aws_bucket_name' => $request->aws_bucket_name,
                'aws_base_url'    => $request->aws_base_url
            ];

            $success = $this->paymentSettings->updateAwsSettings($settings);

            if (!$success) {
                throw new AwsSettingsUpdateException(__('admin.general_settings.update_failed'));
            }

            return response()->json([
                'code'    => 200,
                'message' => __('admin.general_settings.aws_success'),
                'data'    => []
            ]);
        } catch (AwsSettingsUpdateException $e) {
            return response()->json([
                'code'    => 500,
                'message' => $e->getMessage(),
            ], 500);
        } catch (\Throwable $th) {
            return response()->json([
                'code'    => 500,
                'message' => __('admin.general_settings.retrive_error'),
            ], 500);
        }
    }


    public function storeInvoiceSettings(StoreInvoiceSettingsRequest $request): JsonResponse
    {
        try {
            $this->businessSettings->saveInvoiceSettings($request->validated());

            return response()->json([
                'code'    => 200,
                'message' => __('admin.general_settings.invoice_setting_success'),
                'data'    => []
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'code'    => 500,
                'message' => __('admin.general_settings.invoice_setting_error'),
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function store(CompanySettingRequest $request): JsonResponse
    {
        try {
            $this->companySettings->storeCompanySettings($request->validated());

            return response()->json([
                'status'  => 'success',
                'code'    => 200,
                'message' => __('admin.general_settings.company_setting_success')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'code'    => 500,
                'message' => __('admin.general_settings.retrive_error'),
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    public function storeNotificationSettings(StoreNotificationSettingsRequest $request): JsonResponse
    {
        try {
            $this->companySettings->saveNotificationSettings($request->validated());

            return response()->json([
                'status'  => 'success',
                'code'    => 200,
                'message' => __('admin.general_settings.notification_update_success'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'code'    => 500,
                'message' => __('admin.general_settings.notification_error_update'),
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    public function storeSeoSetupSettings(StoreSeoSetupRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            $groupId = $request->group_id ?? null;
            $this->appearanceSettings->storeSeoSettings($data, $groupId);

            return response()->json([
                'status'  => 'success',
                'code'    => 200,
                'message' => __('admin.general_settings.seo_update_success')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'code'    => 500,
                'message' => __('admin.general_settings.seo_update_error'),
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    public function storeMaintenanceSettings(StoreMaintenanceSettingsRequest $request): JsonResponse
    {
        try {
            $this->appearanceSettings->storeMaintenanceSettings($request->all());

            return response()->json([
                'status'  => 'success',
                'code'    => 200,
                'message' => __('admin.general_settings.maintanance_update_success')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'code'    => 500,
                'message' => __('admin.general_settings.maintanance_update_error'),
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    public function storeCookiesSettings(StoreCookiesSettingsRequest $request): JsonResponse
    {
        try {
            $this->contentSettings->storeCookiesSettings($request->validated());

            return response()->json([
                'status'  => 'success',
                'code'    => 200,
                'message' => __('admin.general_settings.cookies_success'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'code'    => 500,
                'message' => __('admin.general_settings.sretrive_error'),
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    public function cookiesSettingsList(CookiesSettingsRequest $request): JsonResponse
    {
        try {
            $settings = $this->contentSettings->getCookiesSettings(
                $request->group_id,
                $request->language_id
            );

            return response()->json([
                'status'  => 'success',
                'code'    => 200,
                'message' => __('admin.general_settings.cookies_retrive_success'),
                'data'    => $settings
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'code'    => 500,
                'message' => __('admin.general_settings.retrive_error'),
                'error'   => $e->getMessage()
            ]);
        }
    }

    public function listCompany(ListCompanyRequest $request): JsonResponse
    {
        try {
            $data = $this->companySettings->getCompanySettings($request->group_id);

            if (!$data) {
                return response()->json([
                    'status'  => 'error',
                    'code'    => 404,
                    'message' => __('admin.common.no_data_found'),
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'code'   => 200,
                'data'   => $data
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'code'    => 500,
                'message' => __('admin.general_settings.retrive_error'),
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    public function list(SettingListRequest $request): JsonResponse
    {
        try {
            $settings = $this->companySettings->getSettingsByGroup($request->validated()['group_id']);

            return response()->json([
                'status'  => 'success',
                'code'    => 200,
                'message' => __('admin.general_settings.setting_retrive_success'),
                'data'    => $settings
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'code'    => 500,
                'message' => __('admin.general_settings.retrive_error'),
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    public function security(): View
    {
        return view('generalsetting::security.index');
    }

    public function checkCurrentPassword(Request $request): JsonResponse
    {
        $password = $request->password;
        $result = \Hash::check($password, auth('admin')->user()->password);

        return response()->json([
            'status'  => $result ? 'success' : 'error',
            'code'    => $result ? 200 : 422,
            'message' => __(
                $result ? 'admin.general_settings.current_password_correct' : 'admin.general_settings.current_password_incorrect'
            ),
        ]);
    }

    public function checkCurrentPhoneNumber(Request $request): JsonResponse
    {
        $user = auth('admin')->user();
        $currentPhone = $request->currentPhoneNumber;

        if (!$user->phone_number) {
            return response()->json([
                'status'  => 'error',
                'code'    => 422,
                'error'   => "null",
                'message' => __('admin.general_settings.phone_number_not_set'),
            ], 422);
        }

        if ($user->phone_number !== $currentPhone) {
            return response()->json([
                'status'  => 'error',
                'code'    => 422,
                'error'   => "incorrect",
                'message' => __('admin.general_settings.phone_number_incorrect'),
            ], 422);
        }

        return response()->json([
            'status'  => 'success',
            'code'    => 200,
            'message' => __('admin.general_settings.phone_number_correct'),
        ]);
    }

    public function updatePassword(UpdatePasswordRequest $request): JsonResponse
    {
        $result = $this->securitySettings->updatePassword($request->only([
            'current_password',
            'new_password'
        ]));

        return response()->json([
            'status'  => $result['success'] ? 'success' : 'error',
            'code'    => $result['success'] ? 200 : 422,
            'message' => $result['message']
        ], $result['success'] ? 200 : 422);
    }

    public function updatePhoneNumber(UpdatePhoneNumberRequest $request): JsonResponse
    {
        $result = $this->securitySettings->updatePhoneNumber($request->only([
            'phone_current_password',
            'current_phonenumber',
            'new_phonenumber'
        ]));

        return response()->json([
            'status'  => $result['success'] ? 'success' : 'error',
            'code'    => $result['success'] ? 200 : 422,
            'message' => $result['message']
        ], $result['success'] ? 200 : 422);
    }

    public function updateEmail(UpdateEmailRequest $request): JsonResponse
    {
        $result = $this->securitySettings->updateEmail($request->only([
            'email_current_password',
            'current_email',
            'new_email'
        ]));

        return response()->json([
            'status'  => $result['success'] ? 'success' : 'error',
            'code'    => $result['success'] ? 200 : 422,
            'message' => $result['message']
        ], $result['success'] ? 200 : 422);
    }

    public function getSecuritySettings(): JsonResponse
    {
        $data = $this->securitySettings->getSecuritySettings();

        return response()->json([
            'status' => 'success',
            'code'   => 200,
            'data'   => $data
        ]);
    }

    public function logoutDevice(Request $request): JsonResponse
    {
        $result = $this->securitySettings->logoutDevice($request->only(['isAll', 'id']));

        return response()->json([
            'status'  => $result['success'] ? 'success' : 'error',
            'code'    => 200,
            'message' => $result['message']
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
                'status'  => 'success',
                'code'    => 200,
                'message' => $message
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status'  => 'error',
                'code'    => 422,
                'message' => 'Something went wrong',
                'error'   => $th->getMessage()
            ]);
        }
    }

    public function updatePrefixes(Request $request): JsonResponse
    {
        try {
            $this->companySettings->updatePrefixes($request->all(), $request->group_id);

            return response()->json([
                'status'  => 'success',
                'code'    => 200,
                'message' => __('admin.general_settings.prefix_settings_update_success'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'code'    => 500,
                'message' => __('admin.common.default_update_error'),
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    public function paymentIndex(): View
    {
        return view('generalsetting::payment.index');
    }

    public function updatepaymentSettings(UpdatePaymentSettingsRequest $request): JsonResponse
    {
        try {
            $success = $this->paymentSettings->updatePaymentSettings($request->all());

            if (!$success) {
                throw new PaymentSettingsUpdateException(__('admin.general_settings.update_failed'));
            }

            return response()->json([
                'code'    => 200,
                'message' => __('admin.general_settings.payment_updated_successfull'),
            ]);
        } catch (PaymentSettingsUpdateException $e) {
            return response()->json([
                'code'    => 500,
                'message' => $e->getMessage(),
            ], 500);
        } catch (\Throwable $th) {
            return response()->json([
                'code'    => 500,
                'message' => __('admin.general_settings.global_settings_error') . $th->getMessage(),
            ], 500);
        }
    }

    public function updatepaymentStatus(UpdatePaymentStatusRequest $request): JsonResponse
    {
        try {
            $success = $this->paymentSettings->updatePaymentStatus($request->all());

            if (!$success) {
                throw new PaymentStatusUpdateException(__('admin.general_settings.update_failed'));
            }

            return response()->json([
                'success' => true,
                'message' => __('admin.general_settings.payment_updated_successfull')
            ]);
        } catch (PaymentStatusUpdateException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => __('admin.general_settings.global_settings_error') . $th->getMessage()
            ], 500);
        }
    }

    public function paymentList(Request $request): JsonResponse
    {
        $orderBy = $request->order_by ?? 'desc';
        $groupId = 13;

        try {
            $data = $this->paymentSettings->getPaymentSettings($groupId, $orderBy);

            return response()->json([
                'code'    => 200,
                'message' => __('admin.general_settings.general_settings_success'),
                'data'    => $data,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code'    => 500,
                'message' => __('admin.general_settings.global_settings_error'),
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function themeSettings(): View
    {
        return view('generalsetting::website_settings.theme_settings');
    }

    public function updateThemeSettings(UpdateThemeSettingsRequest $request): JsonResponse
    {
        try {
            $this->appearanceSettings->updateThemeSettings($request->validated());

            return response()->json([
                'status'  => 'success',
                'code'    => 200,
                'message' => __('admin.general_settings.theme_update_success')
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status'  => 'error',
                'code'    => 500,
                'message' => __('admin.common.default_update_error'),
            ], 500);
        }
    }
}
