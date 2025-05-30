<?php

namespace Modules\GeneralSetting\Repositories\Contracts;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

interface BlogCategoryRepositoryInterface
{
    public function blogCategory(): array;
}
