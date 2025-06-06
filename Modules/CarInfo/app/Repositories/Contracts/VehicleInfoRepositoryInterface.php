<?php

namespace Modules\CarInfo\Repositories\Contracts;

use Illuminate\Http\Request;

interface VehicleInfoRepositoryInterface
{
    public function index();
    public function createVehicle();
    public function editVehicle(string $slug, Request $request);
    public function delete(int|array $id);
    public function getVehicleList();
    public function setPopular(Request $request);
    public function setRecommended(Request $request);
    public function setStatus(Request $request);
    public function getDamageDetails(Request $request);
    public function vehicleInterestLists(Request $request);
    public function deleteVehiclePolicy(Request $request);
    public function deleteVehicleImage(Request $request);
    public function vehicleDetailsList(Request $request);
    public function getModel(?int $brandId);
    public function getTypeAndModel(?int $categoryId);
    public function insurance(?int $vehicleId);
    public function damage(?int $vehicleId);
    public function faq(?int $vehicleId);
    public function documents(?int $vehicleId);
    public function seasonalInfo(?int $vehicleId);
    public function tariffInfo(?int $vehicleId);
    public function checkVehicle(Request $request);
    public function vehicleLists(Request $request);
    public function adminVehicleList(Request $request);
    public function createVehicleInfo(Request $request);
    public function updateVehicleInfo(Request $request);
}
