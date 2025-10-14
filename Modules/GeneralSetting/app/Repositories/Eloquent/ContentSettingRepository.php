<?php

namespace Modules\GeneralSetting\Repositories\Eloquent;

use Modules\GeneralSetting\Exceptions\LanguageNotFoundException;
use Modules\GeneralSetting\Models\GeneralSetting;
use Modules\GeneralSetting\Models\Language;
use Modules\GeneralSetting\Repositories\Contracts\ContentSettingRepositoryInterface;

class ContentSettingRepository implements ContentSettingRepositoryInterface
{
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
