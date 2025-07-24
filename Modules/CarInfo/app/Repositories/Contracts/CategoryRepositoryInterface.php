<?php

namespace Modules\CarInfo\Repositories\Contracts;

use Illuminate\Http\Request;

interface CategoryRepositoryInterface
{
    public function list(Request $request);

    public function store(Request $request);

    public function edit(Request $id);

    public function delete(Request $id);

    public function bulkDelete(Request $request): array;

    public function pdfExport(Request $request);
}
