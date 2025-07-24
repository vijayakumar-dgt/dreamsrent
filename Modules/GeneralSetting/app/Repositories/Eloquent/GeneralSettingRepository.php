<?php

namespace Modules\GeneralSetting\Repositories\Eloquent;

use App\Services\ImageResizer;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Intervention\Image\Laravel\Facades\Image;
use Modules\GeneralSetting\Models\GeneralSetting;
use Modules\GeneralSetting\Models\Language;
use Modules\GeneralSetting\Models\UserDevice;
use Modules\GeneralSetting\Repositories\Contracts\GeneralSettingInterface;

class GeneralSettingRepository implements GeneralSettingInterface
{
    protected ImageResizer $imageResizer;

    public function __construct(ImageResizer $imageResizer)
    {
        $this->imageResizer = $imageResizer;
    }
    protected array $imageKeys = [
        'logo_image',
        'favicon_image',
        'small_image',
        'dark_logo',
        'invoice_logo',
        'maintenance_image',
        'metaImage'
    ];

    public function getSettingsByGroup(int $groupId)
    {
        return GeneralSetting::where('group_id', $groupId)->get()->map(function ($setting) {
            if (in_array($setting->key, $this->imageKeys)) {
                $setting->value = uploadedAsset($setting->value, 'default2');
            }
            return $setting;
        });
    }

    public function storeCompanySettings(array $data): void
    {
        $file = $data['company_profile_photo'] ?? null;
        unset($data['company_profile_photo']);

        if (!empty($file) && $file instanceof \Illuminate\Http\UploadedFile) {
            $companyPhotoPath = 'company';

            $companyPhotoStoragePath = $this->imageResizer->uploadFile($file, 'company', $companyPhotoPath);

            $existing = GeneralSetting::where('key', 'company_profile_photo')->first();
            if ($existing && $existing->value) {
                File::delete(storage_path('app/public/' . $existing->value));
                File::delete(storage_path('app/public/' . str_replace('company/', 'company/thumbnail/', $existing->value)));
            }

            GeneralSetting::updateOrCreate(
                ['key' => 'company_profile_photo'],
                [
                    'value'    => $companyPhotoStoragePath,
                    'group_id' => $data['group_id'] ?? null
                ]
            );
        }

        // Save other general settings
        foreach ($data as $key => $value) {
            GeneralSetting::updateOrCreate(
                ['key' => $key],
                [
                    'value'    => $value,
                    'group_id' => $data['group_id'] ?? null
                ]
            );
        }
    }

    public function getCompanySettings(int $groupId): array|null
    {
        $settings = GeneralSetting::where('group_id', $groupId)->pluck('value', 'key');

        if ($settings->isEmpty()) {
            return null;
        }

        return [
            'organization_name'     => $settings['organization_name'] ?? null,
            'owner_name'            => $settings['owner_name'] ?? null,
            'company_email'         => $settings['company_email'] ?? null,
            'company_phone'         => $settings['international_phone_number'] ?? null,
            'industry'              => $settings['industry'] ?? null,
            'team_size'             => $settings['team_size'] ?? null,
            'company_address_line'  => $settings['company_address_line'] ?? null,
            'country'               => $settings['country'] ?? null,
            'state'                 => $settings['state'] ?? null,
            'city'                  => $settings['city'] ?? null,
            'company_postal_code'   => $settings['company_postal_code'] ?? null,
            'company_profile_photo' => uploadedAsset($settings['company_profile_photo'] ?? null, 'default')
        ];
    }

    public function saveNotificationSettings(array $data): void
    {
        foreach ($data as $key => $value) {
            if ($key === 'group_id') {
                continue;
            }

            GeneralSetting::updateOrCreate(
                ['key' => $key],
                [
                    'value'    => $value,
                    'group_id' => $data['group_id']
                ]
            );
        }
    }

    /**
     * Update prefix settings
     *
     * @param array $settings
     * @param int $groupId
     * @return void
     * @throws \Exception
     */
    public function updatePrefixes(array $settings, int $groupId)
    {
        foreach ($settings as $key => $value) {
            if ($key !== 'group_id') {
                GeneralSetting::updateOrCreate(
                    ['key' => $key],
                    [
                        'value'    => $value,
                        'group_id' => $groupId
                    ]
                );
            }
        }
    }

    /**
     * Get all prefix settings for a group
     *
     * @param int $groupId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getPrefixesByGroup(int $groupId)
    {
        return GeneralSetting::where('group_id', $groupId)
            ->whereIn('key', [
                'reservation_prefix',
                'quotation_prefix',
                'enquiry_prefix',
                'company_prefix',
                'inspection_prefix',
                'report_prefix',
                'customer_prefix'
            ])
            ->get();
    }

    public function storeSeoSettings(array $data, ?int $groupId = 6): void
    {
        $file = $data['metaImage'] ?? null;
        unset($data['metaImage']);
        unset($data['_token']);
        // Fetch old image path
        $existing = GeneralSetting::where('key', 'metaImage')->first();
        $oldPath = $existing?->value;

        if (!empty($file) && $file instanceof \Illuminate\Http\UploadedFile) {
            $seoPhotoPath = 'seo';
            $seoPhotoStoragePath = $this->imageResizer->uploadFile($file, $seoPhotoPath, $oldPath);

            GeneralSetting::updateOrCreate(
                ['key' => 'metaImage'],
                [
                    'value'    => $seoPhotoStoragePath,
                    'group_id' => $groupId
                ]
            );
        }

        foreach ($data as $key => $value) {
            GeneralSetting::updateOrCreate(
                ['key' => $key],
                [
                    'value'    => $value,
                    'group_id' => $groupId
                ]
            );
        }

        Cache::forget('seo_settings');
    }

    public function storeLogoSettings(array $files, int $groupId = 16): array
    {
        $paths = [];
        $logoFields = [
            'logo_image'    => 'logo',
            'favicon_image' => 'favicon',
            'small_image'   => 'small',
            'dark_logo'     => 'dark',
        ];

        foreach ($logoFields as $field => $folderName) {
            if (!empty($files[$field])) {
                $file = $files[$field];

                $existing = GeneralSetting::where('key', $field)->first();
                $oldPath = $existing->value ?? null;

                $relativePath = $this->imageResizer->uploadFile($file, 'logo', $oldPath);
                GeneralSetting::updateOrCreate(
                    ['key' => $field],
                    ['value' => $relativePath, 'group_id' => $groupId]
                );

                $paths[$field] = $relativePath;
            }
        }

        return $paths;
    }

    public function storeMaintenanceSettings(array $data): void
    {
        try {
            $groupId = $data['group_id'];
            $file = $data['maintenance_image'] ?? null;
            $isRemove = $data['is_remove_image'] ?? false;
            unset($data['_token'], $data['maintenance_image'], $data['is_remove_image']);

            // Handle image upload
            if (!empty($file) && $file instanceof \Illuminate\Http\UploadedFile) {
                $existing = GeneralSetting::where('key', 'maintenance_image')->first();
                $oldPath = $existing->value ?? null;

                $relativePath = $this->imageResizer->uploadFile($file, 'maintenance', $oldPath);

                GeneralSetting::updateOrCreate(
                    ['key' => 'maintenance_image'],
                    ['value' => $relativePath, 'group_id' => $groupId]
                );
            }

            // Remove image if requested
            if ($isRemove) {
                $existing = GeneralSetting::where('key', 'maintenance_image')->first();
                if ($existing && $existing->value) {
                    $paths = [
                        storage_path('app/public/' . $existing->value),
                        storage_path('app/public/' . str_replace('maintenance/', 'maintenance/thumbnail/', $existing->value)),
                    ];
                    foreach ($paths as $path) {
                        if (File::exists($path)) {
                            File::delete($path);
                        }
                    }
                    $existing->update(['value' => '']);
                }
            }

            // Save other settings
            foreach ($data as $key => $value) {
                GeneralSetting::updateOrCreate(
                    ['key' => $key],
                    ['value' => $value, 'group_id' => $groupId]
                );
            }
        } catch (Exception $e) {
            // Optionally log the error
            \Log::error('Maintenance settings update failed: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            // Rethrow the exception to be caught in the controller
            throw $e;
        }
    }

    public function updateThemeSettings(array $data): void
    {
        try {
            $groupId = $data['group_id'];

            foreach ($data as $key => $value) {
                if ($key !== 'group_id') {
                    GeneralSetting::updateOrCreate(
                        ['key' => $key],
                        [
                            'value'    => $value,
                            'group_id' => $groupId
                        ]
                    );
                }
            }
        } catch (Exception $e) {
            \Log::error('Theme settings update failed: ' . $e->getMessage());
            throw $e;
        }
    }

    public function storeOtpSettings(array $data): void
    {
        try {
            $settings = [
                'otp_type'        => $data['otp_type'],
                'otp_digit_limit' => $data['otp_digit_limit'],
                'otp_expire_time' => $data['otp_expire_time'],
                'login'           => $data['login'] ?? false,
                'register'        => $data['register'] ?? false,
            ];

            foreach ($settings as $key => $value) {
                $saved = GeneralSetting::updateOrCreate(
                    ['key' => $key],
                    ['value' => is_array($value) ? json_encode($value) : $value]
                );

                if (!$saved) {
                    throw new Exception("Failed to save $key");
                }
            }
        } catch (Exception $e) {
            \Log::error('Failed to store OTP settings: ' . $e->getMessage());
            throw $e;
        }
    }

    public function updateCopyright(array $data): void
    {
        GeneralSetting::updateOrCreate(
            [
                'key'      => 'copy_right_' . $data['language'],
                'group_id' => $data['group_id'],
            ],
            [
                'value'       => $data['copy_right_description'],
                'language_id' => $data['language']
            ]
        );
    }

    public function getCopyright(array $data)
    {
        $languageId = $data['language_id'] ?? Language::where('default', 1)->value('language_id');

        return GeneralSetting::where('group_id', $data['group_id'])
            ->where('key', 'copy_right_' . $languageId)
            ->first();
    }

    public function saveRentalSettings(array $data): void
    {
        $settings = [
            'minAdvanceReservation' => $data['minAdvanceReservation'] ?? null,
            'maxAdvanceReservation' => $data['maxAdvanceReservation'] ?? null,
            'cancellationBuffer'    => $data['cancellationBuffer'] ?? null,
            'rescheduleBuffer'      => $data['rescheduleBuffer'] ?? null,
            'faq'                   => $data['faq'] ?? null,
            'damages'               => $data['damages'] ?? null,
            'extraService'          => $data['extraService'] ?? null,
            'booking'               => $data['booking'] ?? null,
            'enquiries'             => $data['enquiries'] ?? null,
            'reservation'           => $data['reservation'] ?? null,
            'seasonalPricing'       => $data['seasonalPricing'] ?? null,
            'pricing'               => $data['pricing'] ?? null,
        ];

        foreach ($settings as $key => $value) {
            GeneralSetting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }
    }

    public function saveInvoiceSettings(array $data): void
    {
        try {
            $groupId = $data['group_id'] ?? 9;
            $file = $data['invoice_logo'] ?? null;
            $isRemove = $data['is_remove_image'] ?? false;

            unset($data['_token'], $data['invoice_logo'], $data['is_remove_image']);

            // Handle invoice logo upload
            if (!empty($file) && $file instanceof \Illuminate\Http\UploadedFile) {
                $existing = GeneralSetting::where('key', 'invoice_logo')->first();
                $oldPath = $existing->value ?? null;

                // Upload new file and get relative path
                $relativePath = $this->imageResizer->uploadFile(
                    $file,
                    'invoices',
                    $oldPath,
                    [
                        'width'     => 300,  // Set desired width
                        'height'    => 150,  // Set desired height
                        'thumbnail' => true  // Generate thumbnail
                    ]
                );

                GeneralSetting::updateOrCreate(
                    ['key' => 'invoice_logo'],
                    ['value' => $relativePath, 'group_id' => $groupId]
                );
            }

            if ($isRemove) {
                $existing = GeneralSetting::where('key', 'invoice_logo')->first();
                if ($existing && $existing->value) {
                    $paths = [
                        storage_path('app/public/' . $existing->value),
                        storage_path('app/public/' . str_replace('invoices/', 'invoices/thumbnail/', $existing->value)),
                    ];
                    foreach ($paths as $path) {
                        if (File::exists($path)) {
                            File::delete($path);
                        }
                    }
                    $existing->update(['value' => '']);
                }
            }

            $settings = [
                'invoice_prefix'       => $data['invoice_prefix'] ?? null,
                'invoice_due'          => $data['invoice_due'] ?? null,
                'invoice_round_off'    => $data['invoice_round_off'] ?? null,
                'round_off_enabled'    => ($data['round_off_enabled'] ?? 'off') === 'on' ? 1 : 0,
                'show_company_details' => ($data['show_company_details'] ?? 'off') === 'on' ? 1 : 0,
                'invoice_terms'        => $data['invoice_terms'] ?? null,
            ];

            foreach ($settings as $key => $value) {
                GeneralSetting::updateOrCreate(
                    ['key' => $key],
                    ['value' => $value, 'group_id' => $groupId]
                );
            }
        } catch (Exception $e) {
            \Log::error('Invoice settings update failed: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }

    protected function updateOrCreateSetting(string $key, mixed $value, ?int $groupId = null): bool
    {
        $attributes = ['key' => $key];
        $values = ['value' => $value];

        if ($groupId !== null) {
            $values['group_id'] = $groupId;
        }

        return (bool) GeneralSetting::updateOrCreate($attributes, $values);
    }

    public function getCookiesSettings(int $groupId, ?int $languageId = null): array
    {
        if (!$languageId) {
            $defaultLanguage = Language::where('default', 1)->first();

            if (!$defaultLanguage) {
                throw new \Exception(__('admin.general_settings.language_not_found'));
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

        $settings = GeneralSetting::where('group_id', $groupId)
            ->whereIn('key', array_map(fn ($key) => $key . '_' . $languageId, $keys))
            ->pluck('value', 'key');

        $formatted = [];
        foreach ($settings as $key => $value) {
            $baseKey = explode('_' . $languageId, $key)[0];
            $formatted[$baseKey] = $value;
        }

        return $formatted;
    }

    public function storeCookiesSettings(array $data): void
    {
        $fields = [
            'cookiesContentText' => $data['cookiesContentText'],
            'cookiesPosition'    => $data['cookiesPosition'],
            'agreeButtonText'    => $data['agreeButtonText'],
            'declineButtonText'  => $data['declineButtonText'],
            'showDeclineButton'  => isset($data['showDeclineButton']) ? 1 : 0,
            'cookiesPageLink'    => $data['cookiesPageLink'],
        ];
        foreach ($fields as $key => $value) {
            GeneralSetting::updateOrCreate(
                [
                    'key'      => $key . '_' . $data['language'],
                    'group_id' => $data['group_id'],
                ],
                [
                    'value'       => $value,
                    'language_id' => $data['language']
                ]
            );
        }
    }

    public function updatePassword(array $data)
    {
        $user = Auth::guard('admin')->user();

        if (!Hash::check($data['current_password'], $user->password)) {
            return ['success' => false, 'message' => __('admin.general_settings.current_password_incorrect')];
        }

        $user->password = Hash::make($data['new_password']);
        $user->last_password_changed_at = now();
        $user->save();

        return ['success' => true, 'message' => __('admin.general_settings.password_updated_successfully')];
    }

    public function updatePhoneNumber(array $data)
    {
        $user = Auth::guard('admin')->user();

        if (!Hash::check($data['phone_current_password'], $user->password)) {
            return ['success' => false, 'message' => __('admin.general_settings.current_password_incorrect')];
        }

        if ($user->phone_number !== $data['current_phonenumber']) {
            return ['success' => false, 'message' => __('admin.general_settings.phone_number_incorrect')];
        }

        $user->phone_number = $data['new_phonenumber'];
        $user->save();

        return ['success' => true, 'message' => __('admin.general_settings.phone_number_updated_successfully')];
    }

    public function updateEmail(array $data)
    {
        $user = Auth::guard('admin')->user();

        if (!Hash::check($data['email_current_password'], $user->password)) {
            return ['success' => false, 'message' => __('admin.general_settings.current_password_incorrect')];
        }

        if ($user->email !== $data['current_email']) {
            return ['success' => false, 'message' => __('admin.general_settings.current_email_incorrect')];
        }

        $user->email = $data['new_email'];
        $user->save();

        return ['success' => true, 'message' => __('admin.general_settings.email_updated_successfully')];
    }

    public function getSecuritySettings()
    {
        $user = Auth::guard('admin')->user();

        $devices = UserDevice::where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->take(5)
            ->get()
            ->map(fn ($device) => [
                'id'          => $device->id,
                'device_type' => $device->device_type,
                'browser'     => $device->browser,
                'os'          => $device->os,
                'ip_address'  => $device->ip_address,
                'location'    => $device->location,
                'date'        => formatDateTime($device->created_at)
            ]);

        return [
            'user'                     => $user,
            'last_password_changed_at' => $user->last_password_changed_at ? formatDateTime($user->last_password_changed_at) : 'null',
            'devices'                  => $devices
        ];
    }

    public function logoutDevice(array $data)
    {
        $user = Auth::guard('admin')->user();

        if ($data['isAll'] === "true") {
            UserDevice::where('user_id', $user->id)->delete();
            Auth::guard('admin')->logout();

            return ['success' => true, 'message' => __('admin.general_settings.all_device_removed_successfully')];
        }

        $device = UserDevice::find($data['id']);
        if ($device) {
            $device->delete();
        }

        return ['success' => true, 'message' => __('admin.general_settings.device_removed_successfully')];
    }

    public function updatePaymentSettings(array $data): bool
    {
        try {
            $group_id = $data['group_id'];
            $envUpdates = [];

            foreach ($data as $key => $value) {
                if ($key !== 'group_id') {
                    $this->updateOrCreateSettingPayment(
                        ['key' => $key, 'group_id' => $group_id],
                        ['value' => $value]
                    );

                    // Track environment variable updates
                    switch ($key) {
                        case 'paypal_key':
                            $envUpdates['PAYPAL_SANDBOX_CLIENT_ID'] = $value;
                            break;
                        case 'paypal_secret':
                            $envUpdates['PAYPAL_SANDBOX_CLIENT_SECRET'] = $value;
                            break;
                        case 'stripe_key':
                            $envUpdates['STRIPE_KEY'] = $value;
                            break;
                        case 'stripe_secret':
                            $envUpdates['STRIPE_SECRET'] = $value;
                            break;
                    }
                }
            }

            if (!empty($envUpdates)) {
                $this->updateEnvVariables($envUpdates);
            }

            return true;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function updatePaymentStatus(array $data): bool
    {
        return $this->updateOrCreateSettingPayment(
            ['key' => $data['key'], 'group_id' => $data['group_id']],
            ['value' => $data['value']]
        );
    }

    public function getPaymentSettings(int $groupId, string $orderBy = 'desc'): array
    {
        return GeneralSetting::where('group_id', $groupId)
            ->orderBy('id', $orderBy)
            ->get()
            ->toArray();
    }

    protected function updateOrCreateSettingPayment(array $conditions, array $data): bool
    {
        return GeneralSetting::updateOrCreate($conditions, $data) ? true : false;
    }

    public function updateEnvVariables(array $envData): bool
    {
        $path = base_path('.env');

        if (!file_exists($path)) {
            return false;
        }

        $envContent = file_get_contents($path);
        if ($envContent === false) {
            return false;
        }

        foreach ($envData as $key => $value) {
            $pattern = "/^{$key}=.*/m";

            if (preg_match($pattern, $envContent)) {
                $envContent = preg_replace($pattern, "{$key}={$value}", $envContent);
            } else {
                $envContent .= "\n{$key}={$value}";
            }
        }

        return file_put_contents($path, $envContent) !== false;
    }

    public function updateStorageStatus(string $storageType, bool $status): bool
    {
        try {
            $oppositeStorageType = $storageType === 'local_storage' ? 'aws_storage' : 'local_storage';
            $oppositeStatus = !$status;

            $this->updateOrCreateStorageSetting(
                ['key' => $storageType],
                ['value' => $status, 'group_id' => 8]
            );

            $this->updateOrCreateStorageSetting(
                ['key' => $oppositeStorageType],
                ['value' => $oppositeStatus, 'group_id' => 8]
            );

            return true;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function updateAwsSettings(array $settings): bool
    {
        try {
            foreach ($settings as $key => $value) {
                $this->updateOrCreateStorageSetting(
                    ['key' => $key],
                    ['value' => $value, 'group_id' => 8]
                );
            }
            return true;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function updateOrCreateStorageSetting(array $conditions, array $data): bool
    {
        return (bool) GeneralSetting::updateOrCreate($conditions, $data);
    }

    public function storeHowItWorks(array $data): void
    {
        GeneralSetting::updateOrCreate(
            [
                'key'      => 'how_it_works_' . $data['language'],
                'group_id' => $data['group_id'],
            ],
            [
                'value'       => $data['howitwork_description'],
                'language_id' => $data['language'],
            ]
        );
    }

    public function getHowItWorks(array $data)
    {
        $languageId = $data['language_id'] ?? Language::where('default', 1)->value('language_id');

        return GeneralSetting::where('group_id', $data['group_id'])
            ->where('key', 'how_it_works_' . $languageId)
            ->first();
    }
}
