<?php

namespace Modules\GeneralSetting\Repositories\Contracts;

use Illuminate\Http\Request;


interface BlogCategoryRepositoryInterface
{
    public function blogCategory(): array;
    public function categoryStore(Request $request);
}
