<?php

namespace Modules\CarInfo\Repositories\Contracts;

use Illuminate\Http\Request;

interface VehicleManagementRepositoryInterface
{
    public function createVehicle();

    public function editVehicle(string $slug, Request $request);

    public function delete(int|array $id);

    public function setPopular(Request $request);

    public function setRecommended(Request $request);

    public function setStatus(Request $request);

    public function deleteVehiclePolicy(Request $request);

    public function deleteVehicleImage(Request $request);

    public function createVehicleInfo(Request $request);

    public function updateVehicleInfo(Request $request);
}

