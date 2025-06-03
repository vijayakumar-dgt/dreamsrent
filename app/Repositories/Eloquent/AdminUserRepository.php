<?php

namespace App\Repositories\Eloquent;

use App\Models\Notification;
use App\Models\User;
use App\Models\UserDetail;
use App\Repositories\Contracts\AdminUserRepositoryInterface;
use App\Services\ImageResizer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Modules\RolesPermission\Models\Role;

class AdminUserRepository implements AdminUserRepositoryInterface
{
    protected ImageResizer $imageResizer;

    public function __construct(ImageResizer $imageResizer)
    {
        $this->imageResizer = $imageResizer;
    }

    public function index(): array
    {
        $userId = current_user()->id ?? null;
        $roles = Role::select('id', 'role_name')
            ->where('status', 1)
            ->where('created_by', $userId)
            ->get();
        
        $data = ['roles' => $roles];
        return $data;
    }

    public function store(Request $request): array
    {
        $id = $request->id ?? '';

        $successMsg = empty($id) ? __('admin.user_management.user_create_success') : __('admin.user_management.user_update_success');
        $errorMsg = empty($id) ? __('admin.common.default_create_error') : __('admin.common.default_update_error');

        try {
            DB::beginTransaction();

            $userData = [
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
                    if ($file instanceof UploadedFile) {
                        $userDetailsData['profile_image'] = $this->imageResizer->uploadFile($file, 'profile');
                    }
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
                    if ($file instanceof UploadedFile) {
                        $userDetailsData['profile_image'] = $this->imageResizer->uploadFile($file, 'profile', $oldImage);
                    }
                }
                user::where('id', $id)->update($userData);
                UserDetail::updateOrCreate(
                    ['user_id' => $id],
                    $userDetailsData
                );
            }

            DB::commit();

            return [
                'status' => 'success',
                'code' => 200,
                'message' => $successMsg
            ];
        } catch (\Throwable $e) {
            DB::rollBack();
            return [
                'status' => 'error',
                'code' => 500,
                'message' => $errorMsg,
                'error' => $e->getMessage()
            ];
        }
    }

    public function list(Request $request): array
    {
        try {
            $start = $request->start ?? 0;
            $length = $request->length ?? 10;
            $columnIndex = $request->order[0]['column'] ?? 0;
            $columnName = $request->columns[$columnIndex]['data'] ?? 'full_name';
            $orderDir = $request->order[0]['dir'] ?? 'asc';

            $userId = current_user()->id ?? $request->user_id;

            $query = User::select(
                'users.id',
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
                    $q->orWhere('users.email', 'LIKE', "%{$search}%")
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
                        $query->orderByRaw("LOWER(CONCAT_WS(' ', user_details.first_name, user_details.last_name)) asc");
                        break;
                    case 'descending':
                        $query->orderByRaw("LOWER(CONCAT_WS(' ', user_details.first_name, user_details.last_name)) desc");
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
                $query->orderByRaw("LOWER(CONCAT_WS(' ', user_details.first_name, user_details.last_name)){$orderDir}");
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
                $profileImage = is_string($user->profile_image) ? $user->profile_image : '';
                $user->profile_image = uploadedAsset($profileImage, 'profile');
                $user->full_name = $user->full_name ? ucwords($user->full_name) : '';

                return $user;
            });

            return [
                'draw' => intval($request->draw),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $filteredRecords,
                'data' => $users,
                'code' => 200
            ];
        } catch (\Throwable $e) {
            return [
                'code' => 500,
                'message' => __('admin.common.default_retrieve_error'),
            ];
        }
    }

    public function edit(int $id): array
    {
        $data = User::select(
            'users.id',
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
            $profileImage = is_string($data->profile_image) ? $data->profile_image : '';
            $data->profile_image = uploadedAsset($profileImage, "profile");
        }

        return [
            'status' => 'success',
            'code' => 200,
            'data' => $data
        ];
    }

    public function delete(int $id): array
    {
        try {
            User::where('id', $id)->delete();
            UserDetail::where('user_id', $id)->delete();

            return [
                'status' => 'success',
                'code' => 200,
                'message' => __('admin.user_management.user_delete_success')
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'code' => 500,
                'message' => __('admin.common.default_delete_error')
            ];
        }
    }

    public function getNotifications(): array
    {
        $authUser = Auth::guard('admin')->user();
        $notifications = collect();
        if ($authUser !== null) {
            $notifications = Notification::where('user_id', $authUser->id)->where('readed', 0)->orderBy('created_at', 'desc')->limit(10)->get();
            $notificationCount = Notification::where('user_id', $authUser->id)->where('readed', 0)->count();
        } else {
            $notifications = [];
            $notificationCount = 0;
        }
        $html = view('admin.partials.notification-popup', compact('notifications'))->render();
        return [
            'status' => 'success',
            'code' => 200,
            'html' => $html,
            'auth' => $authUser,
            'count' => $notificationCount
        ];
    }

    public function markAllAsRead(): array
    {
        $authUser = Auth::guard('admin')->user();
        if ($authUser !== null && Notification::where('user_id', $authUser->id)->where('readed', 0)->count() > 0) {
            Notification::where('user_id', $authUser->id)->update(['readed' => 1]);
            return [
                'status' => 'success',
                'code' => 200,
                'message' => __('web.user.all_notofocations_marked_as_read')
            ];
        } else {
            return [
                'status' => 'error',
                'code' => 500,
                'message' => __('web.user.all_notofocations_marked_as_read')
            ];
        }
    }

    public function notifications(Request $request): array
    {
        $authUser = Auth::guard('admin')->user();
        $notifications = collect();
        if ($authUser !== null) {
            $notifications = Notification::where('user_id', $authUser->id)->orderBy('created_at', 'desc')->paginate(10);
            if ($request->ajax()) {
                $view = view('admin.partials.notification-items', compact('notifications'))->render();
                return [
                    'html' => $view,
                    'current_page' => $notifications->currentPage(),
                    'last_page' => $notifications->lastPage(),
                    'prev_page_url' => $notifications->previousPageUrl(),
                    'next_page_url' => $notifications->nextPageUrl(),
                    'count' => $notifications->total()
                ];
            }
        }
        return [
            'html' => '',
            'current_page' => 1,
            'last_page' => 1,
            'prev_page_url' => null,
            'next_page_url' => null,
            'count' => 0,
        ];
    }

    public function markNotificationAsRead(Request $request): array
    {
        Notification::where('id', $request->id)->update(['readed' => 1]);
        return [
            'status' => 'success',
            'code' => 200,
            'message' => __('web.user.notification_marked_as_read')
        ];
    }
    
    public function deleteNotification(int $id): array
    {
        Notification::where('id', $id)->delete();
        return [
            'status' => 'success',
            'code' => 200,
            'message' => __('web.user.notification_deleted')
        ];
    }

    public function deleteAllNotification(): array
    {
        $authUser = Auth::guard('admin')->user();
        if ($authUser !== null) {
            Notification::where('user_id', $authUser->id)->delete();
        }
        return [
            'status' => 'success',
            'code' => 200,
            'message' => __('web.user.all_notofocations_deleted')
        ];
    }
}