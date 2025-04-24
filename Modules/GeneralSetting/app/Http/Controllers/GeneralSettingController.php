<?php

namespace Modules\GeneralSetting\Http\Controllers;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Modules\GeneralSetting\Models\GeneralSetting;
use Modules\GeneralSetting\Models\UserDevice;
use Modules\GeneralSetting\Models\IndustryType;
use Modules\GeneralSetting\Models\TeamSize;
use App\Models\User;
use App\Models\UserDetail;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Modules\GeneralSetting\Models\Language;

class GeneralSettingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('generalsetting::index');
    }


    public function logoSettings(Request $request)
    {
        return view('generalsetting::website_settings.logo-setting');
    }

    public function company(Request $request)
    {
        $industries = IndustryType::all();
        $teamSizes = TeamSize::all();
        $users = User::where('user_type', 4)->get();

        return view('generalsetting::company.index', compact('industries', 'teamSizes', 'users'));
    }

    public function notifications(Request $request)
    {
        return view('generalsetting::notifications-setting.index');
    }

    public function prefixes(Request $request)
    {
        return view('generalsetting::website_settings.prefixes');
    }

    public function maintenance(Request $request)
    {
        return view('generalsetting::maintenance.index');
    }

    public function seosetup(Request $request)
    {
        return view('generalsetting::website_settings.seosetup');
    }

    public function gdprCookies(Request $request)
    {
        $languages = Language::with('transLang')->get();
        return view('generalsetting::system_settings.gdpr-cookies', compact('languages'));
    }

    public function storage(Request $request)
    {
        return view('generalsetting::other_settings.storage-setting');
    }

    public function invoiceSettings(Request $request)
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

    public function storeRentalSettings(Request $request)
    {
        $rules = [
            'minAdvanceReservation' => 'required|string|in:1 Day,2 Days,3 Days,1 Week',
            'maxAdvanceReservation' => 'required|string|in:1 Day,2 Days,3 Days,1 Week,2 Weeks,1 Month',
            'cancellationBuffer' => 'required|string',
            'rescheduleBuffer' => 'required|string',
            'faq' => 'nullable|boolean',
            'damages' => 'nullable|boolean',
            'extraService' => 'nullable|boolean',
            'booking' => 'nullable|boolean',
            'enquiries' => 'nullable|boolean',
            'reservation' => 'nullable|boolean',
            'seasonalPricing' => 'nullable|boolean',
            'pricing' => 'nullable|boolean',
        ];

        $messages = [
            'minAdvanceReservation.required' => __('The minimum advance reservation field is required.'),
            'minAdvanceReservation.in' => __('Invalid minimum advance reservation value.'),
            'maxAdvanceReservation.required' => __('The maximum advance reservation field is required.'),
            'maxAdvanceReservation.in' => __('Invalid maximum advance reservation value.'),
            'cancellationBuffer.required' => __('The cancellation buffer field is required.'),
            'cancellationBuffer.in' => __('Invalid cancellation buffer value.'),
            'rescheduleBuffer.required' => __('The reschedule buffer field is required.'),
            'rescheduleBuffer.in' => __('Invalid reschedule buffer value.'),
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return response()->json(['code' => 422, 'errors' => $validator->errors()], 422);
        }

        try {
            function updateOrCreateRentalSetting($key, $value)
            {
                return GeneralSetting::updateOrCreate(
                    ['key' => $key],
                    ['value' => $value, 'group_id' => 20] // Assuming rental settings belong to group_id 20
                );
            }

            $settings = [
                'minAdvanceReservation' => $request->minAdvanceReservation,
                'maxAdvanceReservation' => $request->maxAdvanceReservation,
                'cancellationBuffer' => $request->cancellationBuffer,
                'rescheduleBuffer' => $request->rescheduleBuffer,
                'faq' => $request->faq,
                'damages' => $request->damages,
                'extraService' => $request->extraService,
                'booking' => $request->booking,
                'enquiries' => $request->enquiries,
                'reservation' => $request->reservation,
                'seasonalPricing' => $request->seasonalPricing,
                'pricing' => $request->pricing,
            ];

            foreach ($settings as $key => $value) {
                $saveSetting = updateOrCreateRentalSetting($key, $value);
                if (!$saveSetting) {
                    throw new \Exception("Failed to save $key");
                }
            }

            return response()->json([
                'code' => 200,
                'message' =>  __('admin.general_settings.rental_saved_successfully'),
                'data' => []
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => __('admin.general_settings.retrive_error'),
            ], 500);
        }
    }
    public function storeLogoSettings(Request $request)
    {
        $rules = [
            'logo_image' => 'nullable|mimes:jpg,jpeg,png,svg|max:5120',
            'favicon_image' => 'nullable|image|mimes:jpg,jpeg,png,svg,ico|max:5120',
            'small_image' => 'nullable|image|mimes:jpg,jpeg,png,svg|max:5120',
            'dark_logo' => 'nullable|mimes:jpg,jpeg,png,svg|max:5120',
        ];

        $messages = [
            'logo_image.image' => __('admin.general_settings.logo_image_type'),
            'favicon_image.image' => __('admin.general_settings.favicon_image_type'),
            'small_image.image' => __('admin.general_settings.small_image_type'),
            'dark_logo.image' =>__('admin.general_settings.dark_logo_image_type'),
            'logo_image.max' =>__('admin.general_settings.logo_image_size'),
            'favicon_image.max' => __('admin.general_settings.favicon_image_size'),
            'small_image.max' =>__('admin.general_settings.small_image_size'),
            'dark_logo.max' =>__('admin.general_settings.dark_logo_image_size'),
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return response()->json(['code' => 422, 'errors' => $validator->errors()], 422);
        }

        try {
            $groupId = 16;

            function updateOrCreateLogoSetting($key, $path, $groupId) {
                return GeneralSetting::updateOrCreate(
                    ['key' => $key],
                    ['value' => $path, 'group_id' => $groupId]
                );
            }

            $paths = [];

            $logoFields = [
                'logo_image' => 'logo',
                'favicon_image' => 'favicon',
                'small_image' => 'small',
                'dark_logo' => 'dark',
            ];

            foreach ($logoFields as $field => $prefix) {
                if ($request->hasFile($field)) {
                    $file = $request->file($field);
                    $filename = $prefix . '_' . time() . '.' . $file->getClientOriginalExtension();
                    $path = $file->storeAs('logos', $filename, 'public');

                    $fullPath = 'storage/' . $path;

                    updateOrCreateLogoSetting($field, $fullPath, $groupId);

                    $paths[$field] = $fullPath;
                }
            }

            return response()->json([
                'code' => 200,
                'message' => __('admin.general_settings.logo_update_success'),
                'data' => $paths,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' =>  __('admin.general_settings.logo_setting_error'),
            ]);
        }
    }



    public function storeOtpSettings(Request $request)
    {
        $rules = [
            'otp_type' => 'required',
            'otp_type.*' => 'in:sms,email',
            'otp_digit_limit' => 'required|integer|in:4,5,6',
            'otp_expire_time' => 'required|string|in:2 mins,5 mins,10 mins',
            'login' => 'nullable|boolean',
            'register' => 'nullable|boolean',
        ];

        $messages = [
            'otp_type.required' => __('The OTP type field is required.'),
            'otp_type.*.in' => __('Invalid OTP type selected.'),
            'otp_digit_limit.required' => __('The OTP digit limit field is required.'),
            'otp_digit_limit.integer' => __('The OTP digit limit must be a number.'),
            'otp_digit_limit.in' => __('Invalid OTP digit limit selected.'),
            'otp_expire_time.required' => __('The OTP expiry time field is required.'),
            'otp_expire_time.in' => __('Invalid OTP expiry time selected.'),
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return response()->json(['code' => 422, 'errors' => $validator->errors()], 422);
        }

        try {
            function updateOrCreateOtpSetting($key, $value)
            {
                return GeneralSetting::updateOrCreate(
                    ['key' => $key],
                    ['value' => $value, 'group_id' => 15] // Assuming OTP settings belong to group_id 9
                );
            }

            $settings = [
                'otp_type' => $request->otp_type,
                'otp_digit_limit' => $request->otp_digit_limit,
                'otp_expire_time' => $request->otp_expire_time,
                'login' => $request->login,
                'register' => $request->register,
            ];

            foreach ($settings as $key => $value) {
                $saveSetting = updateOrCreateOtpSetting($key, $value);
                if (!$saveSetting) {
                    throw new \Exception("Failed to save $key");
                }
            }

            return response()->json([
                'code' => 200,
                'message' => __('admin.general_settings.otp_success'),
                'data' => []
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => __('admin.general_settings.retrive_error'),
            ], 500);
        }
    }

    public function storageStatusUpdate(Request $request)
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

    public function storeAwsSettings(Request $request)
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
            function updateOrCreateAwsSetting($key, $value)
            {
                return GeneralSetting::updateOrCreate(
                    ['key' => $key],
                    ['value' => $value, 'group_id' => 8]
                );
            }

            $settings = [
                'aws_access_key' => $request->aws_access_key,
                'aws_secret_key' => $request->aws_secret_key,
                'aws_region' => $request->aws_region,
                'aws_bucket_name' => $request->aws_bucket_name,
                'aws_base_url' => $request->aws_base_url
            ];

            foreach ($settings as $key => $value) {
                $saveSetting = updateOrCreateAwsSetting($key, $value);
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
                'message' =>__('admin.general_settings.retrive_error'),
            ], 500);
        }
    }

    public function storeInvoiceSettings(Request $request)
    {
        $rules = [
            'invoice_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'invoice_prefix' => 'required|string|max:10',
            'invoice_due' => 'required|integer|min:1',
            'invoice_round_off' => 'nullable|numeric',
            'round_off_enabled' => 'nullable|in:on,off',
            'show_company_details' => 'nullable|in:on,off',
            'invoice_terms' => 'nullable|string',
        ];

        $messages = [
            'invoice_logo.image' => __('The invoice logo must be an image.'),
            'invoice_logo.mimes' => __('The invoice logo must be a file of type: jpeg, png, jpg, gif.'),
            'invoice_logo.max' => __('The invoice logo may not be greater than 2MB.'),
            'invoice_prefix.required' => __('The invoice prefix field is required.'),
            'invoice_due.required' => __('The invoice due field is required.'),
            'invoice_due.integer' => __('The invoice due must be an integer.'),
            'invoice_due.min' => __('The invoice due must be at least 1 day.'),
            'invoice_round_off.numeric' => __('The invoice round-off must be a number.'),
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return response()->json(['code' => 422, 'errors' => $validator->errors()], 422);
        }

        try {
            function updateOrCreateInvoiceSetting($key, $value)
            {
                return GeneralSetting::updateOrCreate(
                    ['key' => $key],
                    ['value' => $value, 'group_id' => 9] // Assuming group_id for invoice settings
                );
            }

            // Handle logo upload if present
            if ($request->hasFile('invoice_logo')) {
                $logoPath = $request->file('invoice_logo')->store('invoices', 'public');
                updateOrCreateInvoiceSetting('invoice_logo', $logoPath);
            }

            $settings = [
                'invoice_prefix' => $request->invoice_prefix,
                'invoice_due' => $request->invoice_due,
                'invoice_round_off' => $request->invoice_round_off,
                'round_off_enabled' => $request->round_off_enabled === 'on' ? 1 : 0,
                'show_company_details' => $request->show_company_details === 'on' ? 1 : 0,
                'invoice_terms' => $request->invoice_terms,
            ];

            foreach ($settings as $key => $value) {
                $saveSetting = updateOrCreateInvoiceSetting($key, $value);
                if (!$saveSetting) {
                    throw new \Exception("Failed to save $key");
                }
            }

            return response()->json([
                'code' => 200,
                'message' =>__('admin.general_settings.invoice_setting_success'),
                'data' => []
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => __('admin.general_settings.invoice_setting_error'),
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'organization_name'    => 'required|string|max:100',
            'owner_name'           => 'required|string|max:100',
            'company_email'        => 'required|email|max:100',
            'company_phone'        => 'required|digits_between:10,15',
            'international_phone_number' => 'required',
            'industry'             => 'required|integer',
            'team_size'            => 'required|integer',
            'company_address_line' => 'nullable|string|max:150',
            'country'              => 'required|integer',
            'state'                => 'required|integer',
            'city'                 => 'required|integer',
            'company_postal_code'  => 'nullable|string|max:10',
            'company_profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'code'    => 422,
                'message' => 'Validation failed!',
                'errors'  => $validator->errors()
            ], 422);
        }

        try {
            $settings = $request->except('_token', 'company_profile_photo');
            if ($request->hasFile('company_profile_photo')) {
                $image = $request->file('company_profile_photo');
                $imagePath = $image->store('company_profiles', 'public');

                GeneralSetting::updateOrCreate(
                    ['key' => 'company_profile_photo'],
                    [
                        'value' => "$imagePath",
                        'group_id' => $request->group_id ?? null
                    ]
                );
            }

            foreach ($settings as $key => $value) {
                GeneralSetting::updateOrCreate(
                    ['key' => $key],
                    [
                        'value' => $value,
                        'group_id' => $request->group_id ?? null
                    ]
                );
            }

            return response()->json([
                'status'  => 'success',
                'code'    => 200,
                'message' =>  __('admin.general_settings.company_setting_success')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'code'    => 500,
                'message' =>__('admin.general_settings.retrive_error'),
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    public function transferOwnership(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'owner_id' => 'required|exists:users,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'code'    => 422,
                'message' => __('admin.general_settings.validation_error'),
                'errors'  => $validator->errors()
            ], 422);
        }

        try {
            $newOwnerId = $request->owner_id;

            \DB::transaction(function () use ($newOwnerId) {
                \App\Models\User::where('user_type', 1)->update(['user_type' => 4]);

                \App\Models\User::where('id', $newOwnerId)->update(['user_type' => 1]);
            });

            return response()->json([
                'status'  => 'success',
                'code'    => 200,
                'message' => __('admin.general_settings.ownership_transfer_success')
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

    public function storeNotificationSettings(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'group_id'                   => 'required|integer',
            'notificationPreference'     => 'required',
            'desktopNotifications'       => 'required|boolean',
            'bookingUpdates'             => 'required|boolean',
            'paymentNotifications'       => 'required|boolean',
            'vehicleManagement'          => 'required|boolean',
            'unreadBadge'                => 'required|boolean',
            'userTenantNotifications'    => 'required|boolean',
            'discountOffers'             => 'required|boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'code'    => 422,
                'message' => __('admin.general_settings.validation_error'),
                'errors'  => $validator->errors()
            ], 422);
        }

        try {
            $settings = $request->all();

            foreach ($settings as $key => $value) {
                GeneralSetting::updateOrCreate(
                    ['key' => $key], // Find by key
                    [
                        'value'    => $value,
                        'group_id' => $request->group_id ?? null // Optional if you want to include group_id
                    ]
                );
            }

            return response()->json([
                'status'  => 'success',
                'code'    => 200,
                'message' => __('admin.general_settings.notification_update_success')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'code'    => 500,
                'message' =>  __('admin.general_settings.notification_error_update'),
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    public function storeSeoSetupSettings(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'metaImage'       => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120', // Image validation (5MB max)
            'metaTitle'       => 'required|string|min:5|max:255',
            'siteDescription' => 'required|string|min:10|max:5000',
            'keywords'        => 'required|string|max:1000'
        ]);

        // Handle validation failure
        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'code'    => 422,
                'message' =>  __('admin.general_settings.validation_error'),
                'errors'  => $validator->errors()
            ], 422);
        }

        try {
            $settings = $request->except('_token', 'metaImage');

            if ($request->hasFile('metaImage')) {
                $image = $request->file('metaImage');
                $imagePath = $image->store('seo', 'public');

                GeneralSetting::updateOrCreate(
                    ['key' => 'metaImage'],
                    [
                        'value' => "$imagePath",
                        'group_id' => $request->group_id ?? null
                    ]
                );
            }

            foreach ($settings as $key => $value) {
                GeneralSetting::updateOrCreate(
                    ['key' => $key],
                    [
                        'value' => $value,
                        'group_id' => $request->group_id ?? null
                    ]
                );
            }

            return response()->json([
                'status'  => 'success',
                'code'    => 200,
                'message' => __('admin.general_settings.seo_update_success')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'code'    => 500,
                'message' =>  __('admin.general_settings.seo_update_error'),
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    public function storeMaintenanceSettings(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'group_id'                => 'required',
            'maintenance_image'       => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Image validation
            'maintenance_description' => 'nullable|string|max:5000', // Allow rich text
            'maintenance_status'      => 'nullable' // Status validation
        ]);

        // Handle validation failure
        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'code'    => 422,
                'message' =>  __('admin.general_settings.validation_failed'),
                'errors'  => $validator->errors()
            ], 422);
        }

        try {
            $settings = $request->except('_token', 'maintenance_image'); // Exclude CSRF token and image
            $groupId = $request->group_id;

            // Handle image upload
            if ($request->hasFile('maintenance_image')) {
                $image = $request->file('maintenance_image');
                $imagePath = $image->store('maintenance', 'public'); // Store in 'storage/app/public/maintenance'

                // Save image path to general settings
                GeneralSetting::updateOrCreate(
                    ['key' => 'maintenance_image'],
                    [
                        'value' => "$imagePath", // Public path for serving the image
                        'group_id' => $request->group_id ?? null
                    ]
                );
            }
            // Save other settings with group_id
            foreach ($settings as $key => $value) {
                GeneralSetting::updateOrCreate(
                    ['key' => $key], // Find by key
                    [
                        'value' => $value,
                        'group_id' => $request->group_id ?? null // Optional group_id if needed
                    ]
                );
            }

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

    public function storeCookiesSettings(Request $request)
    {
        $request->validate([
            'group_id'           => 'required|integer',
            'language'           => 'required|integer',
            'cookiesContentText' => 'required|string|max:5000',
            'cookiesPosition'    => 'required|in:right,left',
            'agreeButtonText'    => 'required|string|min:2|max:255',
            'declineButtonText'  => 'required|string|min:2|max:255',
            'showDeclineButton'  => 'nullable|boolean',
            'cookiesPageLink'    => 'required|url|max:2048'
        ]);

        try {
            $fields = [
                'cookiesContentText' => $request->cookiesContentText,
                'cookiesPosition'    => $request->cookiesPosition,
                'agreeButtonText'    => $request->agreeButtonText,
                'declineButtonText'  => $request->declineButtonText,
                'showDeclineButton'  => $request->has('showDeclineButton') ? 1 : 0,
                'cookiesPageLink'    => $request->cookiesPageLink,
            ];

            foreach ($fields as $key => $value) {
                GeneralSetting::updateOrCreate(
                    [
                        'key'      => $key . '_' . $request->language,
                        'group_id' => $request->group_id,
                    ],
                    [
                        'value'       => $value,
                        'language_id' => $request->language
                    ]
                );
            }

            return response()->json([
                'status'  => 'success',
                'code'    => 200,
                'message' =>  __('admin.general_settings.cookies_success'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'code'    => 500,
                'message' =>  __('admin.general_settings.sretrive_error'),
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    public function cookiesSettingsList(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'group_id'    => 'required|integer',
            'language_id' => 'nullable|integer'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'code'    => 422,
                'message' =>  __('admin.general_settings.validation_error'),
                'errors'  => $validator->errors()
            ], 422);
        }

        try {
            $languageId = $request->language_id;

            if (!$languageId) {
                $defaultLanguage = Language::where('default', 1)->first();
                if (!$defaultLanguage) {
                    return response()->json([
                        'status'  => 'error',
                        'code'    => 500,
                        'message' => __('admin.general_settings.language_not_found'),
                    ], 500);
                }
                $languageId = $defaultLanguage->language_id;
            }

            $keys = [
                'cookiesContentText',
                'cookiesPosition',
                'agreeButtonText',
                'declineButtonText',
                'showDeclineButton',
                'cookiesPageLink'
            ];

            $settings = GeneralSetting::where('group_id', $request->group_id)
                ->whereIn('key', array_map(function ($key) use ($languageId) {
                    return $key . '_' . $languageId;
                }, $keys))
                ->pluck('value', 'key');

            $formatted = [];
            foreach ($settings as $key => $value) {
                $baseKey = explode('_' . $languageId, $key)[0];
                $formatted[$baseKey] = $value;
            }

            return response()->json([
                'status'  => 'success',
                'code'    => 200,
                'message' =>  __('admin.general_settings.cookies_retrive_success'),
                'data'    => $formatted
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

    public function listCompany(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'group_id' => 'required|integer'
        ]);

        // Handle validation failure
        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'code'    => 422,
                'message' =>  __('admin.general_settings.validation_error'),
                'errors'  => $validator->errors()
            ], 422);
        }

        try {
            // Fetch settings by group_id
            $settings = GeneralSetting::where('group_id', $request->group_id)->pluck('value', 'key');

            if ($settings->isEmpty()) {
                return response()->json([
                    'status'  => 'error',
                    'code'    => 404,
                    'message' => __('admin.common.no_data_found'),
                ], 404);
            }

            // Get industry and team size names based on their IDs
            $industryName = IndustryType::find($settings['industry'])->name ?? null;
            $teamSizeName = TeamSize::find($settings['team_size'])->name ?? null;

            $response = [
                'organization_name'    => $settings['organization_name'] ?? null,
                'owner_name'           => $settings['owner_name'] ?? null,
                'company_email'        => $settings['company_email'] ?? null,
                // 'company_phone'        => $settings['company_phone'] ?? null,
                'company_phone'        => $settings['international_phone_number'] ?? null,
                'industry'             => $settings['industry'] ?? null,
                'industry_name'        => $industryName,
                'team_size'            => $settings['team_size'] ?? null,
                'team_size_name'       => $teamSizeName,
                'company_address_line' => $settings['company_address_line'] ?? null,
                'country'              => $settings['country'] ?? null,
                'state'                => $settings['state'] ?? null,
                'city'                 => $settings['city'] ?? null,
                'company_postal_code'  => $settings['company_postal_code'] ?? null,
                'company_profile_photo' => $settings['company_profile_photo'] ?? null // Add profile photo here
            ];

            return response()->json([
                'status'  => 'success',
                'code'    => 200,
                'data'    => $response
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'code'    => 500,
                'message' =>  __('admin.general_settings.retrive_error'),
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    public function list(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'group_id' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'code'    => 422,
                'message' => __('admin.general_settings.validation_error'),
                'errors'  => $validator->errors()
            ], 422);
        }

        try {
            $settings = GeneralSetting::where('group_id', $request->group_id)->get();

            return response()->json([
                'status'  => 'success',
                'code'    => 200,
                'message' =>__('admin.general_settings.setting_retrive_success'),
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

    public function security(Request $request)
    {
        return view('generalsetting::security.index');
    }

    /**
     * Check if the provided current password matches the user's actual password.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkCurrentPassword(Request $request)
    {
        // Check if the current password is correct
        $password = $request->password;
        if (Hash::check($password, Auth::user()->password)) {
            return response()->json([
                'status'  => 'success',
                'code'    => 200,
                'message' => __('admin.general_settings.current_password_correct'),
            ]);
        } else {
            return response()->json([
                'status'  => 'error',
                'code'    => 422,
                'message' =>__('admin.general_settings.current_password_incorrect'),
            ]);
        }
    }

    /**
     * Check if the current phone number is correct
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function checkCurrentPhoneNumber(Request $request)
    {
        $currentPhoneNumber = $request->currentPhoneNumber;
        $user = Auth::user();
        if ($user->phone_number == "") {
            return response()->json([
                'status'  => 'error',
                'code'    => 422,
                'error'   => "null",
                'message' => __('admin.general_settings.phone_number_not_set'),
            ], 422);
        } elseif ($user->phone_number != $currentPhoneNumber) {
            return response()->json([
                'status'  => 'error',
                'code'    => 422,
                'error'   => "incorrect",
                'message' => __('admin.general_settings.phone_number_incorrect'),
            ], 422);
        } elseif ($user->phone_number == $currentPhoneNumber) {
            return response()->json([
                'status'  => 'success',
                'code'    => 200,
                'message' => __('admin.general_settings.phone_number_correct'),
            ]);
        } else {
            return response()->json([
                'status'  => 'error',
                'code'    => 422,
                'message' => __('admin.general_settings.phone_number_incorrect'),
            ], 422);
        }
    }
    /**
     * Update the user's password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updatePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'new_password'     => 'required|min:6',
            'confirm_password' => 'required|same:new_password',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'code'    => 422,
                'message' =>__('admin.general_settings.validation_error'),
                'errors'  => $validator->errors()->toArray()
            ], 422);
        }

        if (!Hash::check($request->current_password, Auth::user()->password)) {
            return response()->json([
                'status'  => 'error',
                'code'    => 500,
                'message' =>__('admin.general_settings.current_password_incorrect'),
                'errors'  => $validator->errors()->toArray()
            ], 500);
        }

        $user = Auth::user();
        $user->password = Hash::make($request->new_password);
        $user->last_password_changed_at = now();
        $user->save();

        return response()->json([
            'status'  => 'success',
            'code'    => 200,
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
    public function updatePhoneNumber(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'new_phonenumber' => 'required|digits_between:8,12|unique:users,phone_number',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'code'    => 200,
                'message'  => $validator->errors()->first()
            ], 200);
        }
        $currentPassword = $request->phone_current_password;
        if(!Hash::check($currentPassword, Auth::guard('admin')->user()->password)){
            return response()->json([
                'status'  => 'error',
                'code'    => 200,
                'message'  => __('admin.general_settings.current_password_incorrect')
            ]);
        }
        $currentPhoneNumber = $request->current_phonenumber;
        if (Auth::guard('admin')->user()->phone_number != $currentPhoneNumber && Auth::guard('admin')->user()->phone_number != "") {
            return response()->json([
                'status'  => 'error',
                'code'    => 200,
                'message'  => __('admin.general_settings.phone_number_incorrect')
            ]);
        }
        $user = Auth::user();
        $user->phone_number = $request->new_phonenumber;
        $user->save();

        return response()->json([
            'status'  => 'success',
            'code'    => 200,
            'message' =>  __('admin.general_settings.phone_number_updated_successfully'),
        ]);
    }

    public function updateEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'new_email' => 'required|email|unique:users,email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'code'    => 422,
                'message'  => $validator->errors()->first()
            ], 422);
        }
        $current_email = $request->current_email;
        $authUser = Auth::guard('admin')->user();
        if($authUser->email != $current_email){
            return response()->json([
                'status'  => 'error',
                'code'    => 200,
                'message'  => __('admin.general_settings.current_email_incorrect')
            ]);
        }
        $email_current_password = $request->email_current_password;
        if(!Hash::check($email_current_password, Auth::guard('admin')->user()->password)){
            return response()->json([
                'status'  => 'error',
                'code'    => 200,
                'message'  => __('admin.general_settings.current_password_incorrect')
            ]);
        }
        $user = Auth::user();
        $user->email = $request->new_email;
        $user->save();

        return response()->json([
            'status'  => 'success',
            'code'    => 200,
            'message' =>  __('admin.general_settings.email_updated_successfully'),
        ]);
    }

    public function getSecuritySettings()
    {
        $userDevices = UserDevice::where('user_id', Auth::user()->id)->orderBy('created_at', 'desc')->take(5)->get()->map(function ($device) {
            return [
                'id' => $device->id,
                'device_type' => $device->device_type,
                'browser' => $device->browser,
                'os' => $device->os,
                'ip_address' => $device->ip_address,
                'location' => $device->location,
                'date'     => Carbon::parse($device->created_at)->format('d M Y, h:i A')
            ];
        });
        $response    = [
            'user' => Auth::user(),
            'last_password_changed_at' => Auth::user()->last_password_changed_at ? Carbon::parse(Auth::user()->last_password_changed_at)->format('d M Y, h:i A') : "null",
            'devices' => $userDevices
        ];
        return response()->json([
            'status'  => 'success',
            'code'    => 200,
            'data'    => $response
        ]);
    }

    public function logoutDevice(Request $request)
    {

        if ($request->isAll === "true") {
            UserDevice::where('user_id', Auth::user()->id)->delete();
            Auth::guard('admin')->logout();
            return response()->json([
                'status'  => 'success',
                'code'    => 200,
                'message' => __('admin.general_settings.all_device_removed_successfully'),
            ]);
        } else {
            $device = UserDevice::find($request->id);
            if ($device) {
                $device->delete();
                return response()->json([
                    'status'  => 'success',
                    'code'    => 200,
                    'message' => __('admin.general_settings.device_removed_successfully'),
                ]);
            }
        }
    }

    public function updateGoogleAuth(Request $request)
    {
        try {
            $user = Auth::user();
            $user->google_auth_enabled = $request->googleAuthEnabled === "true" ? 1 : 0;
            $user->save();
            $message = $user->google_auth_enabled === 1 ? "Google Authentication Enabled Successfully" : "Google Authentication Disabled Successfully";
            return response()->json([
                'status' => 'success',
                'code'   => 200,
                'message' => $message
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'code'   => 422,
                'message' => 'Something went wrong',
                'error'   => $th->getMessage()
            ]);
        }
    }

    public function updatePrefixes(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'group_id' => 'required|integer',
            'reservation_prefix' => 'required',
            'quotation_prefix' => 'required',
            'enquiry_prefix' => 'required',
            'company_prefix' => 'required',
            'inspection_prefix' => 'required',
            'invoice_prefix'  => 'required',
            'report_prefix' => 'required',
            'customer_prefix' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'code'    => 422,
                'message' => 'Validation failed!',
                'errors'  => $validator->errors()
            ], 422);
        }

        try {
            $settings = $request->all();

            foreach ($settings as $key => $value) {
                if ($key != 'group_id') {
                    GeneralSetting::updateOrCreate(
                        ['key' => $key],
                        [
                            'value'    => $value,
                            'group_id' => $request->group_id
                        ]
                    );
                }
            }

            return response()->json([
                'status'  => 'success',
                'code'    => 200,
                'message' => __('admin.s.prefix_settings_update_success')
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
                'status'  => 'error',
                'code'    => 422,
                'message' =>  __('admin.s.validation_error'),
                'errors'  => $validator->errors()
            ], 422);
        }

        try {
            $settings = $request->all();

            foreach ($settings as $key => $value) {
                if ($key != 'group_id') {
                    GeneralSetting::updateOrCreate(
                        ['key' => $key],
                        [
                            'value'    => $value,
                            'group_id' => $request->group_id
                        ]
                    );
                }
            }

            return response()->json([
                'status'  => 'success',
                'code'    => 200,
                'message' => __('admin.s.ai_configuration_update_success')
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

    public function paymentIndex(Request $request)
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

                // Update PayPal Keys in .env
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
                'message' => __('admin.s.payment_updated_successfull'),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => __('admin.s.global_settings_error') . $e->getMessage()
            ], 500);
        }
    }

    private function updateEnvFile(string $key, string $value): void
    {
        $path = base_path('.env');

        if (file_exists($path)) {
            $envContent = file_get_contents($path);
            $pattern = "/^{$key}=.*/m";

            // If the key exists, replace it; otherwise, add a new entry
            if (preg_match($pattern, $envContent)) {
                $envContent = preg_replace($pattern, "{$key}={$value}", $envContent);
            } else {
                $envContent .= "\n{$key}={$value}";
            }

            file_put_contents($path, $envContent);
        }
    }

    public function updatepaymentStatus(Request $request)
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

    public function paymentList(Request $request)
    {
        $orderBy = $request->order_by ?? 'desc';

        try {

            $data = GeneralSetting::orderBy('id', $orderBy)->where('group_id', 13)->get();

            return response()->json([
                'code' => 200,
                'message' =>__('admin.general_settings.general_settings_success'),
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

    public function updateThemeSettings(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'group_id' => 'required|integer',
            'default_theme' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'code'    => 422,
                'message' =>  __('admin.general_settings.validation_error'),
                'errors'  => $validator->errors()
            ], 422);
        }

        try {
            $settings = $request->all();

            foreach ($settings as $key => $value) {
                if ($key != 'group_id') {
                    GeneralSetting::updateOrCreate(
                        ['key' => $key],
                        [
                            'value'    => $value,
                            'group_id' => $request->group_id
                        ]
                    );
                }
            }
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
