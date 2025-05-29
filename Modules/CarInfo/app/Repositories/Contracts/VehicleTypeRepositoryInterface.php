<?php

namespace Modules\CarInfo\Repositories\Contracts;

use Illuminate\Http\Request;

interface VehicleTypeRepositoryInterface
{
    public function store(Request $request);
    public function getAll(Request $request);
    public function delete(int $id);
    public function getById(int $id);
    public function getVehicleTypes(Request $request);
}