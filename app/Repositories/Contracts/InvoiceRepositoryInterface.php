<?php

namespace App\Repositories\Contracts;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

interface InvoiceRepositoryInterface
{
    public function addInvoice(): array;
    public function store(Request $request);
    public function index(): array;
    public function edit(int $id);
    public function delete(int $id);
    public function update(Request $request, int $id): RedirectResponse;
}
