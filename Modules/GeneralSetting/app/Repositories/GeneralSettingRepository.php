<?php

namespace Modules\GeneralSetting\Repositories;
use Illuminate\Support\Facades\File;
use Intervention\Image\Laravel\Facades\Image;
use Modules\GeneralSetting\Models\GeneralSetting;
use App\Services\ImageResizer;


class GeneralSettingRepository
{
    protected ImageResizer $imageResizer;

    public function __construct(ImageResizer $imageResizer)
    {
        $this->imageResizer = $imageResizer;
    }
    public function storeCompanySettings(array $data): void
    {
        $file = $data['company_profile_photo'] ?? null;
        unset($data['company_profile_photo']);

        if (!empty($file) && $file instanceof \Illuminate\Http\UploadedFile) {
            $companyPhotoPath = 'company';

            $companyPhotoStoragePath = $this->imageResizer->uploadFile($file, $companyPhotoPath, 'company');

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

}