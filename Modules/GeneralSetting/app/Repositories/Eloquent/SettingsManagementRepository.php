<?php

namespace Modules\GeneralSetting\Repositories\Eloquent;

use App\Services\ImageResizer;
use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Modules\GeneralSetting\Exceptions\OtpSettingsSaveException;
use Modules\GeneralSetting\Models\GeneralSetting;
use Modules\GeneralSetting\Repositories\Contracts\SettingsManagementInterface;

class SettingsManagementRepository implements SettingsManagementInterface
{
    public const APP_PUBLIC = 'app/public/';

    public function __construct(protected ImageResizer $imageResizer)
    {
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
                $this->deleteFiles([
                    storage_path(self::APP_PUBLIC . $existing->value),
                    storage_path(self::APP_PUBLIC . str_replace('company/', 'company/thumbnail/', $existing->value)),
                ]);
            }

            GeneralSetting::updateOrCreate(
                ['key' => 'company_profile_photo'],
                [
                    'value'    => $companyPhotoStoragePath,
                    'group_id' => $data['group_id'] ?? null,
                ]
            );
        }

        foreach ($data as $key => $value) {
            GeneralSetting::updateOrCreate(
                ['key' => $key],
                [
                    'value'    => $value,
                    'group_id' => $data['group_id'] ?? null,
                ]
            );
        }
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
                    'group_id' => $data['group_id'],
                ]
            );
        }
    }

    public function updatePrefixes(array $settings, int $groupId)
    {
        foreach ($settings as $key => $value) {
            if ($key !== 'group_id') {
                GeneralSetting::updateOrCreate(
                    ['key' => $key],
                    [
                        'value'    => $value,
                        'group_id' => $groupId,
                    ]
                );
            }
        }
    }

    public function storeSeoSettings(array $data, ?int $groupId = 6): void
    {
        $file = $data['metaImage'] ?? null;
        unset($data['metaImage'], $data['_token']);

        $existing = GeneralSetting::where('key', 'metaImage')->first();
        $oldPath = $existing?->value;

        if (!empty($file) && $file instanceof \Illuminate\Http\UploadedFile) {
            $seoPhotoPath = 'seo';
            $seoPhotoStoragePath = $this->imageResizer->uploadFile($file, $seoPhotoPath, $oldPath);

            GeneralSetting::updateOrCreate(
                ['key' => 'metaImage'],
                [
                    'value'    => $seoPhotoStoragePath,
                    'group_id' => $groupId,
                ]
            );
        }

        foreach ($data as $key => $value) {
            GeneralSetting::updateOrCreate(
                ['key' => $key],
                [
                    'value'    => $value,
                    'group_id' => $groupId,
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

            if (!empty($file) && $file instanceof \Illuminate\Http\UploadedFile) {
                $existing = GeneralSetting::where('key', 'maintenance_image')->first();
                $oldPath = $existing->value ?? null;

                $relativePath = $this->imageResizer->uploadFile($file, 'maintenance', $oldPath);

                GeneralSetting::updateOrCreate(
                    ['key' => 'maintenance_image'],
                    ['value' => $relativePath, 'group_id' => $groupId]
                );
            }

            if ($isRemove) {
                $existing = GeneralSetting::where('key', 'maintenance_image')->first();
                if ($existing && $existing->value) {
                    $this->deleteFiles([
                        storage_path(self::APP_PUBLIC . $existing->value),
                        storage_path(self::APP_PUBLIC . str_replace('maintenance/', 'maintenance/thumbnail/', $existing->value)),
                    ]);
                    $existing->update(['value' => '']);
                }
            }

            foreach ($data as $key => $value) {
                GeneralSetting::updateOrCreate(
                    ['key' => $key],
                    ['value' => $value, 'group_id' => $groupId]
                );
            }
        } catch (Exception $e) {
            \Log::error('Maintenance settings update failed: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

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
                            'group_id' => $groupId,
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
                \Log::error("Failed to save OTP setting: $key");
                throw new OtpSettingsSaveException("Failed to save $key");
            }
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
                'language_id' => $data['language'],
            ]
        );
    }

    public function saveRentalSettings(array $data)
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

    public function saveInvoiceSettings(array $data)
    {
        try {
            $groupId = $data['group_id'] ?? 9;

            $this->handleInvoiceLogo($data['invoice_logo'] ?? null, $data['is_remove_image'] ?? false, $groupId);

            $settings = [
                'invoice_prefix'       => $data['invoice_prefix'] ?? null,
                'invoice_due'          => $data['invoice_due'] ?? null,
                'invoice_round_off'    => $data['invoice_round_off'] ?? null,
                'round_off_enabled'    => ($data['round_off_enabled'] ?? 'off') === 'on' ? 1 : 0,
                'show_company_details' => ($data['show_company_details'] ?? 'off') === 'on' ? 1 : 0,
                'invoice_terms'        => $data['invoice_terms'] ?? null,
            ];

            $this->saveSettings($settings, $groupId);
        } catch (Exception $e) {
            \Log::error('Invoice settings update failed: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }

    public function storeCookiesSettings(array $data)
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
                    'language_id' => $data['language'],
                ]
            );
        }
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

    protected function handleInvoiceLogo(?\Illuminate\Http\UploadedFile $file, bool $isRemove, int $groupId): void
    {
        if ($file instanceof \Illuminate\Http\UploadedFile) {
            $existing = GeneralSetting::where('key', 'invoice_logo')->first();
            $oldPath = $existing->value ?? null;

            $relativePath = $this->imageResizer->uploadFile(
                $file,
                'invoices',
                $oldPath,
                [
                    'width'     => 300,
                    'height'    => 150,
                    'thumbnail' => true,
                ]
            );

            GeneralSetting::updateOrCreate(
                ['key' => 'invoice_logo'],
                ['value' => $relativePath, 'group_id' => $groupId]
            );
        }

        if ($isRemove) {
            $this->removeInvoiceLogo();
        }
    }

    protected function removeInvoiceLogo(): void
    {
        $existing = GeneralSetting::where('key', 'invoice_logo')->first();
        if (!$existing || empty($existing->value)) {
            return;
        }

        $this->deleteFiles([
            storage_path(self::APP_PUBLIC . $existing->value),
            storage_path(self::APP_PUBLIC . str_replace('invoices/', 'invoices/thumbnail/', $existing->value)),
        ]);

        $existing->update(['value' => '']);
    }

    protected function saveSettings(array $settings, int $groupId): void
    {
        foreach ($settings as $key => $value) {
            GeneralSetting::updateOrCreate(
                ['key' => $key],
                ['value' => $value, 'group_id' => $groupId]
            );
        }
    }

    protected function deleteFiles(array $paths): void
    {
        foreach ($paths as $path) {
            if ($path && File::exists($path)) {
                File::delete($path);
            }
        }
    }
}
