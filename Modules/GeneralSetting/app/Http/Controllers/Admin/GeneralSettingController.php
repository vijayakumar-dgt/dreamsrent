<?php

namespace Modules\GeneralSetting\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class GeneralSettingController extends Controller
{
    public function index(): View
    {
        /** @var view-string $view */
        $view = 'generalsetting::index';

        return view($view);
    }
}
