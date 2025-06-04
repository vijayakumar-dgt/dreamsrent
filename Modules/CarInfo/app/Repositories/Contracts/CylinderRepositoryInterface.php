<?php

namespace Modules\CarInfo\Repositories\Contracts;

use Illuminate\Http\Request;
use Modules\CarInfo\Http\Requests\CylinderRequest;

interface CylinderRepositoryInterface
{
    public function storeCylinderType(CylinderRequest $request): array;
    public function getCylinders(): array;
    public function getCylinder($id): array;
    public function deleteCylinder(Request $request): array;
    public function getCylinderServerside(Request $request): array;
}
