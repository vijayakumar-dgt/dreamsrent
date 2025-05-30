<?php

namespace App\Repositories\Contracts;

use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

interface BlogRepositoryInterface
{
    public function BlogList(Request $request): View| JsonResponse;
    public function BlogDetail(int|string $id): array;
    public function storeReview(Request $request): RedirectResponse;
}