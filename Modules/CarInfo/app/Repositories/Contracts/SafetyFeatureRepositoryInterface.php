<?php

namespace Modules\CarInfo\Repositories\Contracts;

use Illuminate\Http\Request;
use Modules\CarInfo\Http\Requests\SafetyFeatureRequest;

interface SafetyFeatureRepositoryInterface
{
    public function store(SafetyFeatureRequest $request): array;
    public function list(Request $request): array;
    public function edit(int $id): array;
    public function delete(int $id): array;
}
