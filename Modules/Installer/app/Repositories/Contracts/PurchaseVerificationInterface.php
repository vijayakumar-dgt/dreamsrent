<?php

namespace Modules\Installer\Repositories\Contracts;

interface PurchaseVerificationInterface
{
    public function verifyPurchaseCode(string $purchaseCode): array;
}
