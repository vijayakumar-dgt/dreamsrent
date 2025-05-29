<?php

namespace Modules\CarInfo\Repositories\Contracts;

use Illuminate\Http\Request;

interface DamageTypeRepositoryInterface
{
    public function store(Request $request);
    public function getALl(Request $request);
    public function getById(int $id);
    public function delete(int $id);
}