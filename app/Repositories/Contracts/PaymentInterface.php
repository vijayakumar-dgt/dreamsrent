<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;

interface PaymentInterface
{
    public function getDistinctPaymentTypes(): array;

    public function getPaymentList(array $params): array;

    public function formatPaymentData(Collection $bookings): array;
}
