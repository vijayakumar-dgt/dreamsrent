<?php

namespace Modules\GeneralSetting\Repositories\Contracts;

interface BusinessSettingRepositoryInterface
{
    public function saveRentalSettings(array $data): void;

    public function saveInvoiceSettings(array $data): void;
}
