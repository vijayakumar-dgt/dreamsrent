<?php

namespace Modules\GeneralSetting\Repositories;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Intervention\Image\Laravel\Facades\Image;
use Modules\GeneralSetting\Models\GeneralSetting;
use App\Services\ImageResizer;
use Exception;
use Modules\GeneralSetting\Models\Language;


class GeneralSettingRepository
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
                    'value' => $companyPhotoStoragePath,
                    'group_id' => $data['group_id'] ?? null
                ]
            );
        }

        // Save other general settings
        foreach ($data as $key => $value) {
            GeneralSetting::updateOrCreate(
                ['key' => $key],
                [
                    'value' => $value,
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
            'organization_name' => $settings['organization_name'] ?? null,
            'owner_name' => $settings['owner_name'] ?? null,
            'company_email' => $settings['company_email'] ?? null,
            'company_phone' => $settings['international_phone_number'] ?? null,
            'industry' => $settings['industry'] ?? null,
            'team_size' => $settings['team_size'] ?? null,
            'company_address_line' => $settings['company_address_line'] ?? null,
            'country' => $settings['country'] ?? null,
            'state' => $settings['state'] ?? null,
            'city' => $settings['city'] ?? null,
            'company_postal_code' => $settings['company_postal_code'] ?? null,
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
                    'value' => $value,
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
                        'value' => $value,
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
                    'value' => $seoPhotoStoragePath,
                    'group_id' => $groupId
                ]
            );
        }

        foreach ($data as $key => $value) {
            GeneralSetting::updateOrCreate(
                ['key' => $key],
                [
                    'value' => $value,
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
            'logo_image' => 'logo',
            'favicon_image' => 'favicon',
            'small_image' => 'small',
            'dark_logo' => 'dark',
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
                            'value' => $value,
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
                'otp_type' => $data['otp_type'],
                'otp_digit_limit' => $data['otp_digit_limit'],
                'otp_expire_time' => $data['otp_expire_time'],
                'login' => $data['login'] ?? false,
                'register' => $data['register'] ?? false,
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
                'key' => 'copy_right_' . $data['language'],
                'group_id' => $data['group_id'],
            ],
            [
                'value' => $data['copy_right_description'],
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
            'cancellationBuffer' => $data['cancellationBuffer'] ?? null,
            'rescheduleBuffer' => $data['rescheduleBuffer'] ?? null,
            'faq' => $data['faq'] ?? null,
            'damages' => $data['damages'] ?? null,
            'extraService' => $data['extraService'] ?? null,
            'booking' => $data['booking'] ?? null,
            'enquiries' => $data['enquiries'] ?? null,
            'reservation' => $data['reservation'] ?? null,
            'seasonalPricing' => $data['seasonalPricing'] ?? null,
            'pricing' => $data['pricing'] ?? null,
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
                        'width' => 300,  // Set desired width
                        'height' => 150,  // Set desired height
                        'thumbnail' => true  // Generate thumbnail
                    ]
                );

                GeneralSetting::updateOrCreate(
                    ['key' => 'invoice_logo'],
                    ['value' => $relativePath, 'group_id' => $groupId]
                );
            }

            // Remove image if requested
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

            // Handle other invoice settings
            $settings = [
                'invoice_prefix' => $data['invoice_prefix'] ?? null,
                'invoice_due' => $data['invoice_due'] ?? null,
                'invoice_round_off' => $data['invoice_round_off'] ?? null,
                'round_off_enabled' => ($data['round_off_enabled'] ?? 'off') === 'on' ? 1 : 0,
                'show_company_details' => ($data['show_company_details'] ?? 'off') === 'on' ? 1 : 0,
                'invoice_terms' => $data['invoice_terms'] ?? null,
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
            ->whereIn('key', array_map(fn($key) => $key . '_' . $languageId, $keys))
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
            'cookiesPosition' => $data['cookiesPosition'],
            'agreeButtonText' => $data['agreeButtonText'],
            'declineButtonText' => $data['declineButtonText'],
            'showDeclineButton' => isset($data['showDeclineButton']) ? 1 : 0,
            'cookiesPageLink' => $data['cookiesPageLink'],
        ];
        foreach ($fields as $key => $value) {
            GeneralSetting::updateOrCreate(
                [
                    'key' => $key . '_' . $data['language'],
                    'group_id' => $data['group_id'],
                ],
                [
                    'value' => $value,
                    'language_id' => $data['language']
                ]
            );
        }
    }


}