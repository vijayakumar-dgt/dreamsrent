<?php

namespace Modules\RolesPermission\Repositories\Eloquent;

use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Modules\RolesPermission\Models\Module as ModuleModel;
use Modules\RolesPermission\Models\Permission;
use Modules\RolesPermission\Models\Role;
use Modules\RolesPermission\Repositories\Contracts\RolesPermissionRepositoryInterface;

class RolesPermissionRepository implements RolesPermissionRepositoryInterface
{
    public function store(Request $request): array
    {
        $id = $request->id ?? '';
        $authId = currentUser()->id ?? $request->user_id;

        $successMsg = empty($id) ? __('admin.user_management.role_create_success') : __('admin.user_management.role_update_success');
        $errorMsg = empty($id) ? __('admin.common.default_create_error') : __('admin.common.default_update_error');

        try {
            $data = [
                'role_name'  => $request->role,
                'created_by' => $authId,
            ];

            if (empty($id)) {
                Role::create($data);
            } else {
                $data['status'] = $request->status ?? 1;
                Role::where('id', $id)->update($data);
            }

            return [
                'status'  => 'success',
                'code'    => 200,
                'message' => $successMsg
            ];
        } catch (\Exception $e) {
            return [
                'status'  => 'error',
                'code'    => 500,
                'message' => $errorMsg,
            ];
        }
    }

    public function list(Request $request): array
    {
        try {
            $userId = currentUser()->id ?? $request->user_id;
            $query = Role::query();

            $query->where('created_by', $userId);

            if (!empty($request->search)) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('role_name', 'like', "%{$search}%");
                });
            }

            if ($request->has('sort_by_status') && !empty($request->sort_by_status) || $request->sort_by_status == '0') {
                $status = $request->sort_by_status;
                $query->where('roles.status', $status);
            }

            $columnIndex = $request->order[0]['column'] ?? 1;
            $columnName = $request->columns[$columnIndex]['data'] ?? 'role_name';
            $orderDir = $request->order[0]['dir'] ?? 'asc';

            $query->orderBy($columnName, $orderDir);

            $start = $request->start ?? 0;
            $length = $request->length ?? 10;

            $filterTotalRecords = $query->count();
            $totalRecords = Role::where('created_by', $userId)->count();

            $data = $query->skip($start)->take($length)->get()->map(function ($role) {
                $role->encrypted_role_id = customEncrypt($role->id, Role::$roleSecretKey);
                $role->created_date = formatDateTime($role->created_at, false);
                unset($role->created_at);

                return $role;
            });

            return [
                'draw'            => intval($request->draw),
                'recordsTotal'    => $totalRecords,
                'recordsFiltered' => $filterTotalRecords,
                'data'            => $data,
                'code'            => 200
            ];
        } catch (\Exception $e) {
            return [
                'code'    => 500,
                'message' => __('admin.common.default_retrieve_error'),
            ];
        }
    }

    public function edit(int $id): array
    {
        $data = Role::find($id);
        Cache::forget('permissions_' . $id);

        if (!$data) {
            return [
                'status'  => 'error',
                'code'    => 404,
                'message' => __('admin.common.no_data_found')
            ];
        }

        return [
            'status' => 'success',
            'code'   => 200,
            'data'   => $data
        ];
    }

    public function delete(int $id): array
    {
        try {
            /** @var Role $role */
            $role = Role::findOrFail($id);
            $role->delete();
            Cache::forget('permissions_' . $id);

            return [
                'status'  => 'success',
                'code'    => 200,
                'message' => __('admin.user_management.role_delete_success')
            ];
        } catch (ModelNotFoundException $e) {
            return [
                'status'  => 'error',
                'code'    => 404,
                'message' => __('admin.common.no_data_found'),
            ];
        } catch (\Exception $e) {
            return [
                'status'  => 'error',
                'code'    => 500,
                'message' => __('admin.common.default_delete_error')
            ];
        }
    }

    public function permissions(?int $roleId, ?int $userId): array
    {
        $userType = User::where('id', $userId)->value('user_type');
        if ($userType == 2) {
            $userType = 1;
        }

        $role = Role::select('id', 'role_name')->where('id', $roleId)->firstOrFail();

        $modules = ModuleModel::select('id', 'module_name', 'module_slug', 'parent_id')
            ->with([
                'childModules' => function ($q) {
                    $q->select('id', 'module_name', 'module_slug', 'parent_id');
                },
                'childModules.permissions' => function ($q) use ($roleId) {
                    $q->select('id', 'role_id', 'module_id', 'create', 'edit', 'delete', 'view', 'allow_all')
                    ->where('role_id', $roleId);
                }
            ])
            ->whereNull('parent_id')
            ->where('user_type', $userType)
            ->get();

        return [
            'role'    => $role,
            'modules' => $modules,
        ];
    }

    public function permissionUpdate(Request $request): array
    {
        $roleId = $request->role_id;
        $permissions = $request->input('permissions', []);

        try {
            foreach ($permissions as $permission) {
                Permission::updateOrCreate(
                    ['id' => $permission['id'], 'role_id' => $roleId],
                    [
                        'module_id' => $permission['module_id'],
                        'create'    => $permission['create'] ?? 0,
                        'view'      => $permission['view'] ?? 0,
                        'edit'      => $permission['edit'] ?? 0,
                        'delete'    => $permission['delete'] ?? 0,
                        'allow_all' => $permission['allow_all'] ?? 0
                    ]
                );
            }
            Cache::forget('permissions_' . $roleId);

            return [
                'code'    => 200,
                'message' => __('admin.user_management.permission_update_success'),
            ];
        } catch (\Throwable $e) {
            return [
                'code'    => 500,
                'message' => __('admin.common.default_update_error'),
            ];
        }
    }

    public function getUserPermissionsData(): array
    {
        try {
            $permissions = getUserPermissions();

            return [
                'code'    => 200,
                'data'    => $permissions,
                'message' => __('admin.common.default_retrieve_success'),
            ];
        } catch (\Throwable $e) {
            return [
                'code'    => 500,
                'message' => __('admin.common.default_retrieve_error'),
            ];
        }
    }
}
