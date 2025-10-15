<?php

namespace Modules\GeneralSetting\Repositories\Eloquent;

use Modules\GeneralSetting\Models\GeneralSetting;
use Modules\GeneralSetting\Models\Language;
use Modules\GeneralSetting\Repositories\Contracts\SettingsRetrievalInterface;
use Modules\GeneralSetting\Exceptions\LanguageNotFoundException;

class SettingsRetrievalRepository implements SettingsRetrievalInterface
{
    /**
     * Settings that contain image paths and require asset transformation.
     *
     * @var array<int, string>
     */
    protected array $imageKeys = [
        'logo_image',
        'favicon_image',
        'small_image',
        'dark_logo',
        'invoice_logo',
        'maintenance_image',
        'metaImage',
    ];

    public function getSettingsByGroup(int $groupId)
    {
        return GeneralSetting::where('group_id', $groupId)
            ->get()
            ->map(function ($setting) {
                if (in_array($setting->key, $this->imageKeys, true)) {
                    $setting->value = uploadedAsset($setting->value, 'default2');
                }

                return $setting;
            });
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
            'company_profile_photo' => uploadedAsset($settings['company_profile_photo'] ?? null, 'default'),
        ];
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
                'customer_prefix',
            ])
            ->get();
    }

    public function getCookiesSettings(int $groupId, ?int $languageId = null): array
    {
        if (!$languageId) {
            $defaultLanguage = Language::where('default', 1)->first();

            if (!$defaultLanguage) {
                throw new LanguageNotFoundException();
            }

            $languageId = $defaultLanguage->language_id;
        }

        $keys = [
            'cookiesContentText',
            'cookiesPosition',
            'agreeButtonText',
            'declineButtonText',
            'showDeclineButton',
            'cookiesPageLink',
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

    public function getPaymentSettings(int $groupId, string $orderBy = 'desc'): array
    {
        return GeneralSetting::where('group_id', $groupId)
            ->orderBy('id', $orderBy)
            ->get()
            ->toArray();
    }

    public function getHowItWorks(array $data)
    {
        $languageId = $data['language_id'] ?? Language::where('default', 1)->value('language_id');

        return GeneralSetting::where('group_id', $data['group_id'])
            ->where('key', 'how_it_works_' . $languageId)
            ->first();
    }

    public function getCopyright(array $data)
    {
        $languageId = $data['language_id'] ?? Language::where('default', 1)->value('language_id');

        return GeneralSetting::where('group_id', $data['group_id'])
            ->where('key', 'copy_right_' . $languageId)
            ->first();
    }
}
