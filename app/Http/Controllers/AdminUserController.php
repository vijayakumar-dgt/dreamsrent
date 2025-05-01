<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\User;
use App\Models\UserDetail;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Modules\RolesPermission\Models\Role;

class AdminUserController extends Controller
{
    public function index(Request $request): View
    {
        $userId = current_user()->id ?? $request->user_id;
        $roles = Role::select('id', 'role_name')
            ->where('status', 1)
            ->where('created_by', $userId)
            ->get();
        return view('admin.users', compact('roles'));
    }

    public function store(Request $request): JsonResponse
    {
        $id = $request->id ?? '';

        $validator = Validator::make($request->all(), [
            'username' => [
                'required',
                'max:100',
                Rule::unique('users', 'name')->ignore($id)->whereNull('deleted_at'),
            ],
            'first_name' => [
                'required',
                'min:3',
                'max:20',
            ],
            'last_name' => [
                'required',
                'max:20',
            ],
            'phone_number' => ['required'],
            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($id)->whereNull('deleted_at'),
            ],
            'image' => 'mimes:jpeg,jpg,png|max:2048',
            'role_id' => ['required'],
            'password' => ['nullable'],
            'confirm_password' => ['nullable', 'same:password'],
        ], [
            'first_name.required' => __('admin.common.first_name_required'),
            'first_name.min' => __('admin.common.first_name_minlength', ['min' => 3]),
            'first_name.max' => __('admin.common.first_name_maxlength', ['max' => 30]),
            'last_name.required' => __('admin.common.last_name_required'),
            'last_name.min' => __('admin.common.last_name_minlength', ['min' => 3]),
            'last_name.max' => __('admin.common.last_name_maxlength', ['max' => 30]),
            'username.required' => __('admin.common.username_required'),
            'username.max' => __('admin.common.username_maxlength'),
            'username.unique' => __('admin.common.username_unique'),
            'phone_number.required' => __('admin.common.phone_number_required'),
            'image.mimes' => __('admin.common.image_format'),
            'image.max' => __('admin.common.image_size', ['size' => 2]),
            'email.required' => __('admin.common.email_required'),
            'email.email' => __('admin.common.email_valid'),
            'email.unique' => __('admin.common.email_unique'),
            'role_id.required' => __('admin.user_management.role_required'),
            'password.required' => __('admin.common.password_required'),
            'confirm_password.required' => __('admin.common.confirm_password_required'),
            'confirm_password.same' => __('admin.common.confirm_password_equal_to'),
        ]);

        $validator->sometimes('password', 'required', function () use ($id) {
            return empty($id);
        });
        $validator->sometimes('confirm_password', 'required', function () use ($id) {
            return empty($id);
        });

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'code'   => 422,
                'errors' => $validator->errors()->toArray()
            ], 422);
        }

        $successMsg = empty($id) ?
         __('admin.user_management.user_create_success') : __('admin.user_management.user_update_success');
        $errorMsg = empty($id) ?  __('admin.common.default_create_error') : __('admin.common.default_update_error');

        try {
            DB::beginTransaction();

            $userData = [
                'name' => $request->username,
                'email' => $request->email,
                'phone_number' => $request->phone_number,
                'role_id' => $request->role_id,
                'user_type' => 2,
                'status' => $request->status ?? 1,
            ];
            $userDetailsData = [
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'parent_id' => current_user()->id ?? $request->user_id
            ];

            if (empty($id)) {
                if ($request->hasFile('image')) {
                    $file = $request->file('image');
                    $userDetailsData['profile_image'] = uploadFile($file, 'profile');
                }
                $userData['password'] = Hash::make($request->password);
                $user = User::create($userData);

                $userDetailsData['user_id'] = $user->id;
                UserDetail::create($userDetailsData);
            } else {
                $user = UserDetail::where('user_id', $id)->first();
                $oldImage = '';
                if ($user) {
                    $oldImage = $user->profile_image;
                }

                if ($request->hasFile('image')) {
                    $file = $request->file('image');
                    $userDetailsData['profile_image'] = uploadFile($file, 'profile', $oldImage);
                }

                user::where('id', $id)->update($userData);
                UserDetail::updateOrCreate(
                    ['user_id' => $id],
                    $userDetailsData
                );
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'code'   => 200,
                'message' => $successMsg
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
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
            $start = $request->start ?? 0;
            $length = $request->length ?? 10;
            $searchValue = $request->search ?? null;
            $columnIndex = $request->order[0]['column'] ?? 0;
            $columnName = $request->columns[$columnIndex]['data'] ?? 'full_name';
            $orderDir = $request->order[0]['dir'] ?? 'asc';

            $userId = current_user()->id ?? $request->user_id;

            $query = User::select(
                'users.id',
                'users.name as username',
                DB::raw("CONCAT(user_details.first_name, ' ', user_details.last_name) as full_name"),
                'users.email',
                'users.phone_number',
                'users.status',
                'user_details.profile_image',
                'users.role_id',
                'roles.role_name'
            )
                ->leftJoin('user_details', 'users.id', '=', 'user_details.user_id')
                ->join('roles', 'roles.id', '=', 'users.role_id')
                ->where(['user_details.parent_id' => $userId]);

            if ($request->has('search') && !empty($request->search)) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('users.name', 'LIKE', "%{$search}%")
                        ->orWhere('users.email', 'LIKE', "%{$search}%")
                        ->orWhere('users.phone_number', 'LIKE', "%{$search}%")
                        ->orWhere('user_details.first_name', 'LIKE', "%{$search}%")
                        ->orWhere('user_details.last_name', 'LIKE', "%{$search}%");
                });
            }

            if ($request->has('role_ids') && !empty($request->role_ids)) {
                $query->whereIn('users.role_id', $request->role_ids);
            }

            if (
                $request->has('sort_by_status')
                && !empty($request->sort_by_status) || $request->sort_by_status == '0'
            ) {
                $status = $request->sort_by_status;
                $query->where('users.status', $status);
            }

            if ($request->has('sort_by') && !empty($request->sort_by)) {
                switch (strtolower($request->sort_by)) {
                    case 'latest':
                        $query->orderBy('users.created_at', 'desc');
                        break;
                    case 'ascending':
                        $query->orderBy('users.id', 'asc');
                        break;
                    case 'descending':
                        $query->orderBy('users.id', 'desc');
                        break;
                    case 'last month':
                        $startDate = \Carbon\Carbon::now()->subMonth()->startOfMonth();
                        $endDate = \Carbon\Carbon::now()->subMonth()->endOfMonth();
                        $query->whereBetween('users.created_at', [$startDate, $endDate]);
                        break;
                    case 'last 7 days':
                        $startDate = \Carbon\Carbon::now()->subDays(7)->startOfDay();
                        $endDate = \Carbon\Carbon::now()->endOfDay();
                        $query->whereBetween('users.created_at', [$startDate, $endDate]);
                        break;
                }
            }

            if ($columnName === 'full_name') {
                $query
                ->orderByRaw("LOWER(CONCAT_WS(' ', user_details.first_name, user_details.last_name, users.name))
                 {$orderDir}");
            } else {
                $query->orderBy($columnName, $orderDir);
            }

            $totalRecords = User::leftJoin('user_details', 'users.id', '=', 'user_details.user_id')
                ->where(['user_details.parent_id' => $userId])
                ->count();
            $filteredRecords = $query->count();

            $query->offset($start)->limit($length);
            $users = $query->get();

            $users->map(function ($user) {
                $user->profile_image = uploadedAsset($user->profile_image, 'profile');

                return $user;
            });

            return response()->json([
                'draw' => intval($request->draw),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $filteredRecords,
                'data' => $users,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'code' => 500,
                'message' => __('admin.common.default_retrieve_error'),
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function edit(Request $request): JsonResponse
    {
        $id = $request->id;

        $data = User::select(
            'users.id',
            'users.name as username',
            'users.email',
            'users.phone_number',
            'users.role_id',
            'users.status',
            'user_details.first_name',
            'user_details.last_name',
            'user_details.profile_image',
        )
            ->leftJoin('user_details', 'users.id', '=', 'user_details.user_id')
            ->where(['users.user_type' => 2, 'users.id' => $id])
            ->first();

        if ($data) {
            $data->profile_image = uploadedAsset($data->profile_image, "profile");
        }

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

            User::where('id', $id)->delete();
            UserDetail::where('user_id', $id)->delete();

            return response()->json([
                'status' => 'success',
                'code'   => 200,
                'message' => __('admin.user_management.user_delete_success')
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'code'   => 500,
                'message' => __('admin.common.default_delete_error')
            ], 500);
        }
    }


    public function getNotifications(Request $request): JsonResponse
    {
        $authUser = Auth::guard('admin')->user();
        $notifications = collect();
        if ($authUser !== null) {
            $notifications = Notification::
                where('user_id', $authUser->id)->where('readed', 0)->orderBy('created_at', 'desc')->limit(10)->get();
            $notificationCount = Notification::where('user_id', $authUser->id)->where('readed', 0)->count();
        } else {
            $notifications = [];
            $notificationCount = 0;
        }
        $html = view('admin.partials.notification-popup', compact('notifications'))->render();
        return response()->json([
            'status' => 'success',
            'code'   => 200,
            'html' => $html,
            'auth' => $authUser,
            'count' => $notificationCount
        ]);
    }

    public function markAllAsRead(Request $request): JsonResponse
    {
        $authUser = Auth::guard('admin')->user();
        if ($authUser !== null && Notification::where('user_id', $authUser->id)->where('readed', 0)->count() > 0) {
            Notification::where('user_id', $authUser->id)->update(['readed' => 1]);
            return response()->json([
                'status' => 'success',
                'code'   => 200,
                'message' => __('web.user.all_notofocations_marked_as_read')
            ], 200);
        } else {
            return response()->json([
                'status' => 'error',
                'code'   => 500,
                'message' => __('web.user.all_notofocations_marked_as_read')
            ], 200);
        }
    }

    public function notifications(Request $request): View | JsonResponse
    {
        $authUser = Auth::guard('admin')->user();
        $notifications = collect();
        if ($authUser !== null) {
            $notifications = Notification::where('user_id', $authUser->id)
            ->orderBy('created_at', 'desc')->paginate(10);
            if ($request->ajax()) {
                $view = view('admin.partials.notification-items', compact('notifications'))->render();
                return response()->json([
                    'html' => $view,
                    'current_page' => $notifications->currentPage(),
                    'last_page' => $notifications->lastPage(),
                    'prev_page_url' => $notifications->previousPageUrl(),
                    'next_page_url' => $notifications->nextPageUrl(),
                    'count' => $notifications->total()
                ]);
            }
        }
        return view('admin.partials.notifications', compact('notifications'));
    }

    public function markNotificationAsRead(Request $request): JsonResponse
    {
        Notification::where('id', $request->id)->update(['readed' => 1]);
        return response()->json([
            'status' => 'success',
            'code'   => 200,
            'message' => __('web.user.notification_marked_as_read')
        ], 200);
    }

    public function deleteNotification(Request $request): JsonResponse
    {
        Notification::where('id', $request->id)->delete();
        return response()->json([
            'status' => 'success',
            'code'   => 200,
            'message' => __('web.user.notification_deleted')
        ], 200);
    }

    public function deleteAllNotification(Request $request): JsonResponse
    {
        $authUser = Auth::guard('admin')->user();
        if ($authUser !== null) {
            Notification::where('user_id', $authUser->id)->delete();
        }
        return response()->json([
            'status' => 'success',
            'code'   => 200,
            'message' => __('web.user.all_notofocations_deleted')
        ], 200);
    }
}
