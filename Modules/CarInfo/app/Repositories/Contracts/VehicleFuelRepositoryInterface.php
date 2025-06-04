<?php

namespace Modules\CarInfo\Repositories\Contracts;

use Illuminate\Http\Request;

interface VehicleFuelRepositoryInterface
{
    public function list(Request $request);
    public function store(Request $request);
    public function edit(int $id);
    public function delete(int $id);
}
