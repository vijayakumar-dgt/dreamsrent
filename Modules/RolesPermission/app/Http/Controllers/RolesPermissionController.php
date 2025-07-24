<?php

namespace Modules\RolesPermission\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\RolesPermission\Http\Requests\RoleRequest;
use Modules\RolesPermission\Models\Role;
use Modules\RolesPermission\Repositories\Contracts\RolesPermissionRepositoryInterface;

class RolesPermissionController extends Controller
{
    protected RolesPermissionRepositoryInterface $rolesPermissionRepository;

    public function __construct(RolesPermissionRepositoryInterface $rolesPermissionRepository)
    {
        $this->rolesPermissionRepository = $rolesPermissionRepository;
    }

    public function index(): View
    {
        return view('rolespermission::admin.roles-permissions');
    }

    public function store(RoleRequest $request): JsonResponse
    {
        $response = $this->rolesPermissionRepository->store($request);
        return response()->json($response, $response['code']);
    }

    public function list(Request $request): JsonResponse
    {
        $response = $this->rolesPermissionRepository->list($request);
        return response()->json($response, $response['code']);
    }

    public function edit(Request $request): JsonResponse
    {
        $id = $request->id;
        $response = $this->rolesPermissionRepository->edit($id);
        return response()->json($response, $response['code']);
    }

    public function delete(Request $request): JsonResponse
    {
        $id = $request->id;
        $response = $this->rolesPermissionRepository->delete($id);
        return response()->json($response, $response['code']);
    }

    public function permissions(Request $request): View
    {
        $roleId = customDecrypt($request->encrypted_role_id, Role::$roleSecretKey);
        $userId = current_user()->id ?? $request->user_id;
        $data = $this->rolesPermissionRepository->permissions($roleId, $userId);

        return view('rolespermission::admin.permissions', $data);
    }

    public function permissionUpdate(Request $request): JsonResponse
    {
        $response = $this->rolesPermissionRepository->permissionUpdate($request);
        return response()->json($response, $response['code']);
    }

    public function getUserPermissionsData(Request $request): JsonResponse
    {
        $response = $this->rolesPermissionRepository->getUserPermissionsData($request);
        return response()->json($response, $response['code']);
    }
}
