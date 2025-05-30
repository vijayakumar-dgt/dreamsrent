<?php

namespace Modules\CarInfo\Repositories\Contracts;

use Illuminate\Http\Request;

interface DriverRepositoryInterface
{
    public function index();
    public function store(Request $request);
    public function list(Request $request);
    public function delete(Request $request);
    public function getById(int $id);
    public function changeStatus(Request $request);
    public function getDrivers(Request $request);
    public function getDriverDetails(int $driverId);
}