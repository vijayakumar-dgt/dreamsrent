<?php

namespace Modules\GeneralSetting\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Modules\GeneralSetting\Repositories\Contracts\GeneralSettingInterface;

abstract class GeneralSettingBaseController extends Controller
{
    protected GeneralSettingInterface $repository;

    public function __construct(GeneralSettingInterface $repository)
    {
        $this->repository = $repository;
    }
}
