<?php

namespace Modules\CarInfo\Repositories\Contracts;

use Illuminate\Http\Request;

interface TagRepositoryInterface
{
    public function store(Request $request);
    public function getAll(Request $request);
    public function getById(int $id);
    public function delete(int $id);
}
