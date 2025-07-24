<?php

namespace Modules\Communication\Repositories\Contracts;

use Illuminate\Http\Request;

interface AnnouncementRepositoryInterface
{
    public function store(Request $request);

    public function list(Request $request): array;

    public function delete(Request $request): array;
}
