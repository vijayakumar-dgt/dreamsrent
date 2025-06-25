<?php

namespace Modules\GeneralSetting\Repositories\Contracts;

interface CurrencySettingInterface
{
    public function createOrUpdateCurrency(array $data): bool;

    public function getCurrencyList(array $filters = [], int $perPage = 10): mixed;

    public function findCurrency(int $id): mixed;

    public function deleteCurrency(int $id): bool;
}
