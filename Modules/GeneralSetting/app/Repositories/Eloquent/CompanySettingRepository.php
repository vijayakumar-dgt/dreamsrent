<?php

namespace Modules\GeneralSetting\Repositories\Eloquent;

use App\Services\ImageResizer;
use Illuminate\Support\Facades\File;
use Modules\GeneralSetting\Models\GeneralSetting;
use Modules\GeneralSetting\Repositories\Contracts\CompanySettingRepositoryInterface;

class CompanySettingRepository implements CompanySettingRepositoryInterface
{
    private const APP_PUBLIC = 'app/public/';

    public function __construct(private readonly ImageResizer $imageResizer)
    {
    }

    /**
     * @var string[]
     */
    private array $imageKeys = [
        'logo_image',
        'favicon_image',
        'small_image',
        'dark_logo',
        'invoice_logo',
        'maintenance_image',
        'metaImage',
        'company_profile_photo',
    ];

    public function getSettingsByGroup(int $groupId)
    {
        return GeneralSetting::where('group_id', $groupId)->get()->map(function ($setting) {
            if (in_array($setting->key, $this->imageKeys, true)) {
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
                File::delete(storage_path(self::APP_PUBLIC . $existing->value));
                File::delete(storage_path(self::APP_PUBLIC . str_replace('company/', 'company/thumbnail/', $existing->value)));
            }

            GeneralSetting::updateOrCreate(
                ['key' => 'company_profile_photo'],
                [
                    'value'    => $companyPhotoStoragePath,
                    'group_id' => $data['group_id'] ?? null
                ]
            );
        }

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

    public function getCompanySettings(int $groupId): ?array
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

    public function updatePrefixes(array $settings, int $groupId): void
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
}
