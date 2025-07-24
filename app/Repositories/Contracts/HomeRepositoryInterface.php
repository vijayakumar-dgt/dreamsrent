<?php

namespace App\Repositories\Contracts;

use Illuminate\Http\Request;

interface HomeRepositoryInterface
{
    public function getHomeData();

    public function getVehicles(Request $request);

    public function getVehicleDetails(string $slug);

    public function searchLocations(string $keyword);

    public function getMaintenanceData();

    public function getContactData();
}
