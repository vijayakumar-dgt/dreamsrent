<?php

namespace Modules\GeneralSetting\Repositories\Contracts;

interface TaxRateSettingInterface
{
    public function createOrUpdateTaxRate(array $data): bool;

    public function getTaxRateList(string $orderBy): mixed;

    public function deleteTaxRate(int $id): bool;

    public function findTaxRate(int $id): mixed;

    public function createOrUpdateTaxGroup(array $data): bool;

    public function getTaxGroupList(string $orderBy): mixed;

    public function deleteTaxGroup(int $id): bool;

    public function findTaxGroup(int $id): mixed;

    public function getActiveTaxRates(): mixed;
}
