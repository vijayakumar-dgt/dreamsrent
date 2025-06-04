<?php

namespace Modules\RolesPermission\Repositories\Contracts;

use Illuminate\Http\Request;

interface RolesPermissionRepositoryInterface
{
    public function store(Request $request);
    public function list(Request $request);
    public function edit(int $id);
    public function delete(int $id);
    public function permissions(?int $roleId, ?int $userId);
    public function permissionUpdate(Request $request);
    public function getUserPermissionsData();
}
