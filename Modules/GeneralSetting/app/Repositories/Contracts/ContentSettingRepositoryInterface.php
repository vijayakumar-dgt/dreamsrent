<?php

namespace Modules\GeneralSetting\Repositories\Contracts;

interface ContentSettingRepositoryInterface
{
    public function storeCookiesSettings(array $data): void;

    public function getCookiesSettings(int $groupId, ?int $languageId = null): array;

    public function updateCopyright(array $data): void;

    public function getCopyright(array $data);

    public function storeHowItWorks(array $data): void;

    public function getHowItWorks(array $data);
}
