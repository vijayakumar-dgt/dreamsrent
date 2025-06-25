<?php

namespace Modules\CarInfo\Repositories\Contracts;

use Illuminate\Http\Request;

interface EnquiryRepositoryInterface
{
    public function store(Request $request);

    public function getAll(Request $request);

    public function update(Request $request);

    public function delete(int $id);
}
