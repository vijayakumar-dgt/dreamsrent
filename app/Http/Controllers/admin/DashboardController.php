<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\DashboardRepositoryInterface;
use Illuminate\View\View;

class DashboardController extends Controller
{
    protected DashboardRepositoryInterface $dashboardRepository;

    public function __construct(DashboardRepositoryInterface $dashboardRepository)
    {
        $this->dashboardRepository = $dashboardRepository;
    }

    public function index(): View
    {
        $data = $this->dashboardRepository->index();
        return view('admin.dashboard.index', [...$data]);
    }
}
