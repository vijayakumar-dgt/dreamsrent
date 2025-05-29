<?php

namespace App\Repositories\Contracts;

interface HomeRepositoryInterface
{
    public function getHomeData();
    public function getVehicles();
    public function getVehicleDetails(string $slug);
    public function searchLocations(string $keyword);
    public function getMaintenanceData();
    public function getContactData();
}