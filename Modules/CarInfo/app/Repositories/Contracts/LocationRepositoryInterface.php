<?php

namespace Modules\CarInfo\Repositories\Contracts;

use Illuminate\Http\Request;

interface LocationRepositoryInterface
{
    public function store(Request $request);
    public function getAll(Request $request);
    public function delete(int $id);
    public function getById(int $id);
    public function getCountries(Request $request);
    public function getStates(int $id);
    public function getCities(int $id);
    public function getAllLocations(Request $request);
}