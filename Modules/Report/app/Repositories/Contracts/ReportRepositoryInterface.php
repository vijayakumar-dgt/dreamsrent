<?php

namespace Modules\Report\Repositories\Contracts;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

interface ReportRepositoryInterface
{
    public function incomeReport(): array;
    public function earningReport(): array;
    public function getMonthlyEarnings();
    public function getEarningsBreakdown();
}
