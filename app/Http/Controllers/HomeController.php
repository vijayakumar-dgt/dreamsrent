<?php

namespace App\Http\Controllers;

use App\Repositories\Contracts\HomeRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeController extends Controller
{
    protected HomeRepositoryInterface $homeRepository;

    public function __construct(HomeRepositoryInterface $homeRepository)
    {
        $this->homeRepository = $homeRepository;
    }

    public function index(): View
    {
        $viewPath = $this->homeRepository->getHomeData();
        return view($viewPath);
    }

    public function list(Request $request): View|JsonResponse
    {
        $data = $this->homeRepository->getVehicles($request);
        return view('frontend.home.list.list', $data);
    }

    public function vehicleDetails(Request $request): View|JsonResponse
    {
        $response = $this->homeRepository->getVehicleDetails($request->slug);
        return view('frontend.home.list.vehicle-details', $response);
    }

    public function searchLocations(Request $request): JsonResponse
    {
        $keyword = $request->input('query');
        $locations = $this->homeRepository->searchLocations($keyword);
        return response()->json($locations);
    }

    public function maintenance(): View
    {
        $data = $this->homeRepository->getMaintenanceData();
        return view('frontend.home.maintenance', $data);
    }

    public function contactUs(Request $request): View
    {
        $data = $this->homeRepository->getContactData();
        return view('frontend.home.contact-us', $data);
    }
}
