<?php

namespace App\Repositories\Eloquent;

use App\Models\Notification;
use App\Models\User;
use App\Models\UserDetail;
use App\Repositories\Contracts\AdminUserRepositoryInterface;
use App\Services\ImageResizer;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
        $userId = currentUser()->id ?? null;
        $roles = Role::select('id', 'role_name')
            ->where('status', 1)
            ->where('created_by', $userId)
            ->get();
        return ['roles' => $roles];
    }

    public function store(Request $request): array
    {
        $id = $request->id ?? '';
        $isNew = empty($id);

        $successMsg = $isNew
            ? __('admin.user_management.user_create_success')
            : __('admin.user_management.user_update_success');

        $errorMsg = $isNew
            ? __('admin.common.default_create_error')
            : __('admin.common.default_update_error');

        try {
            DB::beginTransaction();

            // Prepare user data
            $userData = [
                'email'        => $request->email,
                'phone_number' => $request->phone_number,
                'role_id'      => $request->role_id,
                'user_type'    => 2,
                'status'       => $request->status ?? 1,
            ];

            $userDetailsData = [
                'first_name' => $request->first_name,
                'last_name'  => $request->last_name,
                'parent_id'  => currentUser()->id ?? $request->user_id,
            ];

            // Handle file upload if exists
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                if ($file instanceof UploadedFile) {
                    $oldImage = '';
                    if (!$isNew) {
                        $userDetail = UserDetail::where('user_id', $id)->first();
                        $oldImage = $userDetail->profile_image ?? '';
                    }
                    $userDetailsData['profile_image'] = $this->imageResizer->uploadFile($file, 'profile', $oldImage);
                }
            }

            if ($isNew) {
                // Create new user
                $userData['password'] = Hash::make($request->password);
                $user = User::create($userData);

                $userDetailsData['user_id'] = $user->id;
                UserDetail::create($userDetailsData);
            } else {
                // Update existing user
                User::where('id', $id)->update($userData);
                UserDetail::updateOrCreate(['user_id' => $id], $userDetailsData);
            }

            DB::commit();

            return [
                'status'  => 'success',
                'code'    => 200,
                'message' => $successMsg,
            ];
        } catch (\Throwable $e) {
            DB::rollBack();

            return [
                'status'  => 'error',
                'code'    => 500,
                'message' => $errorMsg,
                'error'   => $e->getMessage(),
            ];
        }
    }

    public function list(Request $request): array
{
    try {
        $start       = $request->start ?? 0;
        $length      = $request->length ?? 10;
        $columnIndex = $request->order[0]['column'] ?? 0;
        $columnName  = $request->columns[$columnIndex]['data'] ?? 'full_name';
        $orderDir    = $request->order[0]['dir'] ?? 'asc';
        $userId      = currentUser()->id ?? $request->user_id;

        // Base query
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
        ->where('user_details.parent_id', $userId);

        // Search filter
        if ($search = $request->search ?? null) {
            $query->where(function ($q) use ($search) {
                $q->orWhere('users.email', 'LIKE', "%{$search}%")
                  ->orWhere('users.phone_number', 'LIKE', "%{$search}%")
                  ->orWhere('user_details.first_name', 'LIKE', "%{$search}%")
                  ->orWhere('user_details.last_name', 'LIKE', "%{$search}%");
            });
        }

        // Role filter
        if (!empty($request->role_ids ?? [])) {
            $query->whereIn('users.role_id', $request->role_ids);
        }

        // Status filter
        if (isset($request->sort_by_status)) {
            $query->where('users.status', $request->sort_by_status);
        }

        // Sorting
        $this->applySorting($query, $request, $columnName, $orderDir);

        // Records count
        $totalRecords    = User::leftJoin('user_details', 'users.id', '=', 'user_details.user_id')
                                ->where('user_details.parent_id', $userId)
                                ->count();
        $filteredRecords = $query->count();

        // Pagination
        $users = $query->offset($start)->limit($length)->get();

        // Format users
        $users->map(function ($user) {
            $user->profile_image = uploadedAsset(is_string($user->profile_image) ? $user->profile_image : '', 'profile');
            $user->full_name     = $user->full_name ? ucwords($user->full_name) : '';
            return $user;
        });

        return [
            'draw'            => intval($request->draw),
            'recordsTotal'    => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data'            => $users,
            'code'            => 200,
        ];
    } catch (\Throwable $e) {
        return [
            'code'    => 500,
            'message' => __('admin.common.default_retrieve_error'),
        ];
    }
}

/**
 * Apply sorting and date filters
 */
private function applySorting($query, Request $request, string $columnName, string $orderDir): void
{
    $sortBy = strtolower($request->sort_by ?? '');

    if ($sortBy) {
        switch ($sortBy) {
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
                $query->whereBetween('users.created_at', [now()->subMonth()->startOfMonth(), now()->subMonth()->endOfMonth()]);
                break;
            case 'last 7 days':
                $query->whereBetween('users.created_at', [now()->subDays(7)->startOfDay(), now()->endOfDay()]);
                break;
            default:
                $query->orderBy('users.created_at', 'desc');
                break;
        }
    }

    // Column ordering
    if ($columnName === 'full_name') {
        $query->orderByRaw("LOWER(CONCAT_WS(' ', user_details.first_name, user_details.last_name)) {$orderDir}");
    } else {
        $query->orderBy($columnName, $orderDir);
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
            ->where(['users.id' => $id])
            ->first();

        if ($data) {
            $profileImage = is_string($data->profile_image) ? $data->profile_image : '';
            $data->profile_image = uploadedAsset($profileImage, "profile");
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
            User::where('id', $id)->delete();
            UserDetail::where('user_id', $id)->delete();

            return [
                'status'  => 'success',
                'code'    => 200,
                'message' => __('admin.user_management.user_delete_success')
            ];
        } catch (\Exception $e) {
            return [
                'status'  => 'error',
                'code'    => 500,
                'message' => __('admin.common.default_delete_error')
            ];
        }
    }

    public function getNotifications(): array
    {
        $authUser = Auth::guard('admin')->user();

        if ($authUser !== null) {
            $notifications = Notification::where('user_id', $authUser->id)->where('readed', 0)->orderBy('created_at', 'desc')->limit(10)->get();
            $notificationCount = Notification::where('user_id', $authUser->id)->where('readed', 0)->count();
        } else {
            $notifications = [];
            $notificationCount = 0;
        }
        $html = view('admin.partials.notification-popup', ['notifications' => $notifications])->render();
        return [
            'status' => 'success',
            'code'   => 200,
            'html'   => $html,
            'auth'   => $authUser,
            'count'  => $notificationCount
        ];
    }

    public function markAllAsRead(): array
    {
        $authUser = Auth::guard('admin')->user();
        if ($authUser !== null && Notification::where('user_id', $authUser->id)->where('readed', 0)->count() > 0) {
            Notification::where('user_id', $authUser->id)->update(['readed' => 1]);
            return [
                'status'  => 'success',
                'code'    => 200,
                'message' => __('web.user.all_notofocations_marked_as_read')
            ];
        } else {
            return [
                'status'  => 'error',
                'code'    => 500,
                'message' => __('web.user.all_notofocations_marked_as_read')
            ];
        }
    }

    public function notifications(Request $request): LengthAwarePaginator
    {
        $authUser = Auth::guard('admin')->user();
        return Notification::where('user_id', $authUser->id)->orderBy('created_at', 'desc')->paginate(10);
    }

    public function markNotificationAsRead(Request $request): array
    {
        Notification::where('id', $request->id)->update(['readed' => 1]);
        return [
            'status'  => 'success',
            'code'    => 200,
            'message' => __('web.user.notification_marked_as_read')
        ];
    }

    public function deleteNotification(int $id): array
    {
        Notification::where('id', $id)->delete();
        return [
            'status'  => 'success',
            'code'    => 200,
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
            'status'  => 'success',
            'code'    => 200,
            'message' => __('web.user.all_notofocations_deleted')
        ];
    }
}
