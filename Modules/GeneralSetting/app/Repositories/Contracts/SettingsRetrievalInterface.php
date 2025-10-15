<?php

namespace Modules\GeneralSetting\Repositories\Contracts;

interface SettingsRetrievalInterface
{
    public function getSettingsByGroup(int $groupId);

    public function getCompanySettings(int $groupId): ?array;

    public function getPrefixesByGroup(int $groupId);

    public function getCookiesSettings(int $groupId, ?int $languageId = null): array;

    public function getPaymentSettings(int $groupId, string $orderBy = 'desc'): array;

    public function getHowItWorks(array $data);

    public function getCopyright(array $data);
}
