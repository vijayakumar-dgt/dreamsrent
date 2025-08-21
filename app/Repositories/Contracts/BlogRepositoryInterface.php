<?php

namespace App\Repositories\Contracts;

use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

interface BlogRepositoryInterface
{
    public function blogList(Request $request): View| JsonResponse;

    public function blogDetail(int|string $id): array;

    public function storeReview(Request $request): RedirectResponse;
}
