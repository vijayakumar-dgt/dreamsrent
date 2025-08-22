<?php

namespace Modules\Report\Repositories\Contracts;

interface ReportRepositoryInterface
{
    public function incomeReport(): array;

    public function earningReport(): array;

    public function getMonthlyEarnings();

    public function getEarningsBreakdown();
}
