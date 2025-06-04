<?php

namespace Modules\GeneralSetting\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;
use Modules\GeneralSetting\Models\GeneralSetting;
use Modules\GeneralSetting\Models\Timezone;

interface LocalizationInterface
{
    public function getTimezones(): Collection;

    public function searchTimezones(string $search): SupportCollection;

    public function getCurrentTimezone(): ?GeneralSetting;

    public function updateLocalization(array $data): void;

    public function getTimezoneById(int $id): ?Timezone;
}
