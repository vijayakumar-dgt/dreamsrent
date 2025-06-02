<?php

namespace App\Repositories\Contracts;

use Illuminate\Http\Request;

interface CustomerRepositoryInterface
{
    public function index();
    public function store(Request $request);
    public function list(Request $request);
    public function edit(int $id);
    public function getCustomerDetails(?int $id);
    public function delete(Request $request);
}