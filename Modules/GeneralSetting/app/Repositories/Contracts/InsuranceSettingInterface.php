<?php

namespace Modules\GeneralSetting\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;
use Modules\GeneralSetting\Models\Insurance;

interface InsuranceSettingInterface
{
    public function getPricingTypes(): Collection;

    public function saveInsurance(array $data, array $benefits, ?int $id = null): Insurance;

    public function getInsuranceList(array $params): array;

    public function getInsuranceWithBenefits(int $id): Insurance;

    public function deleteInsurance(int $id): void;

    public function getVehicleInsurances(array $vehicleIds): Collection;
}
