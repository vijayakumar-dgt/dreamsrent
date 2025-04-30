<?php

namespace Modules\RolesPermission\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Modules\RolesPermission\Models\Module as ModuleModel;
use Modules\RolesPermission\Models\Permission;
use Modules\RolesPermission\Models\Role;

class RolesPermissionController extends Controller
{
    protected $authUser;
    public function __construct()
    {
        $this->authUser = current_user();
    }

    public function index(): View
    {
        return view('rolespermission::admin.roles-permissions');
    }

    public function store(Request $request): JsonResponse
    {
        $id = $request->id ?? '';
        $authId = $this->authUser->id ?? $request->user_id;

        $validator = Validator::make($request->all(), [
            'role' => [
                'required',
                'max:30',
                'min:3',
                Rule::unique('roles', 'role_name')->ignore($id)->whereNull('deleted_at')->where('created_by', $authId)
            ],
        ], [
            'role.required' => __('admin.user_management.role_required'),
            'role.max' => __('admin.user_management.role_maxlength'),
            'role.min' => __('admin.user_management.role_minlength'),
            'role.unique' => __('admin.user_management.role_unique'),
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'code'   => 422,
                'errors' => $validator->errors()->toArray()
            ], 422);
        }

        $successMsg = empty($id) ? __('admin.user_management.role_create_success') : __('admin.user_management.role_update_success');
        $errorMsg = empty($id) ? __('admin.common.default_create_error') : __('admin.common.default_update_error');

        try {
            $data = [
                'role_name' => $request->role,
                'created_by' => $this->authUser->id ?? $request->user_id,
            ];

            if (empty($id)) {
                Role::create($data);
            } else {
                $data['status'] = $request->status ?? 1;
                Role::where('id', $id)->update($data);
            }

            return response()->json([
                'status' => 'success',
                'code'   => 200,
                'message' => $successMsg
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'code'   => 500,
                'message' => $errorMsg,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function list(Request $request): JsonResponse
    {
        try {
            $userId = $this->authUser->id ?? $request->user_id;
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

            return response()->json([
                'draw' => intval($request->draw),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $filterTotalRecords,
                'data' => $data,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => __('admin.common.default_error'),
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function edit(Request $request): JsonResponse
    {
        $id = $request->id;
        $data = Role::find($id);

        return response()->json([
            'status' => 'success',
            'code'   => 200,
            'data' => $data
        ], 200);
    }

    public function delete(Request $request): JsonResponse
    {
        try {
            $id = $request->id;
            Role::where('id', $id)->delete();

            return response()->json([
                'status' => 'success',
                'code'   => 200,
                'message' => __('admin.user_management.role_delete_success')
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'code'   => 500,
                'message' => __('admin.common.default_delete_error')
            ], 500);
        }
    }

    public function permissions(Request $request)
    {
        $roleId = customDecrypt($request->encrypted_role_id, Role::$roleSecretKey);
        $userId = $this->authUser->id ?? $request->user_id;

        $userType = User::where('id', $userId)->value('user_type');
        if ($userType == 2) {
            $userType = 1;
        }

        $role = Role::select('id', 'role_name')->where('id', $roleId)->first();
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

        return view('rolespermission::admin.permissions', compact('modules', 'role'));
    }

    public function permissionUpdate(Request $request): JsonResponse
    {
        $roleId = $request->role_id;
        $permissions = $request->input('permissions', []);

        try {
            foreach ($permissions as $permission) {
                Permission::updateOrCreate(
                    ['id' => $permission['id'], 'role_id' => $roleId],
                    [
                        'module_id' => $permission['module_id'],
                        'create' => $permission['create'] ?? 0,
                        'view' => $permission['view'] ?? 0,
                        'edit' => $permission['edit'] ?? 0,
                        'delete' => $permission['delete'] ?? 0,
                        'allow_all' => $permission['allow_all'] ?? 0
                    ]
                );
            }

            return response()->json([
                'code' => 200,
                'message' => __('admin.user_management.permission_update_success'),
            ], 200);
        } catch (\Throwable $e) {
            return response()->json([
                'code' => 500,
                'message' => __('admin.common.default_update_error'),
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getUserPermissionsData(Request $request): JsonResponse
    {
        try {
            $permissions = getUserPermissions();

            return response()->json([
                'code' => 200,
                'data' => $permissions,
                'message' => __('admin.common.default_retrieve_success'),
            ], 200);
        } catch (\Throwable $e) {
            return response()->json([
                'code' => 500,
                'message' => __('admin.common.default_retrieve_error'),
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
