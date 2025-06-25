<?php

namespace Modules\CarInfo\Repositories\Contracts;

use Illuminate\Http\Request;

interface VehicleColorRepositoryInterface
{
    public function list(Request $request);

    public function store(Request $request);

    public function edit(int $id);

    public function delete(int $id);

    public function getVehicleColors(Request $request);
}
