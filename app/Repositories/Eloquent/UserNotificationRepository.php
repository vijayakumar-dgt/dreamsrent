<?php

namespace App\Repositories\Eloquent;

use App\Models\Notification;
use App\Models\User;
use App\Repositories\Contracts\UserNotificationRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserNotificationRepository implements UserNotificationRepositoryInterface
{
    public function getUserNotifications(): array
    {
        $seo_title = __('web.user.notifications');
        $user = Auth::guard('web')->user();

        return [
            'user'      => $user,
            'seo_title' => $seo_title,
        ];
    }

    public function updateNotificationSettings(Request $request): array
    {
        try {
            $user = Auth::guard('web')->user();

            if ($user instanceof User) {
                $user->booking_confirmation = $request->booking_confirmation == "1" ? 1 : 0;
                $user->desktop_notifications = $request->desktop_notifications == "1" ? 1 : 0;
                $user->email_notifications = $request->email_notifications == "1" ? 1 : 0;
                $user->save();
            }

            return [
                'status'  => 'success',
                'code'    => 200,
                'message' => __('web.common.default_update_success'),
            ];
        } catch (\Exception $e) {
            return [
                'status'  => 'error',
                'code'    => 500,
                'message' => __('web.common.default_update_error'),
                'error'   => $e->getMessage(),
            ];
        }
    }

    public function getNotifications(): array
    {
        $notifications = [];
        $notificationCount = 0;

        if (Auth::guard('web')->check()) {
            $authUserId = Auth::guard('web')->user()->id ?? 0;
            $notifications = Notification::where('user_id', $authUserId)
                ->where('readed', 0)
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();
            $notificationCount = Notification::where('user_id', $authUserId)
                ->where('readed', 0)
                ->count();
        }

        $html = view('frontend.user.notifications-popup', ['notifications' => $notifications])->render();

        return [
            'status' => 'success',
            'code'   => 200,
            'html'   => $html,
            'count'  => $notificationCount,
        ];
    }

    public function markAllAsRead(): array
    {
        $authUserId = Auth::guard('web')->user()->id ?? 0;

        if (
            Notification::where('user_id', $authUserId)
                ->where('readed', 0)
                ->count() > 0
        ) {
            Notification::where('user_id', $authUserId)->update(['readed' => 1]);

            return [
                'status'  => 'success',
                'code'    => 200,
                'message' => __('web.user.all_notofocations_marked_as_read'),
            ];
        }

        return [
            'status'  => 'error',
            'code'    => 500,
            'message' => __('web.user.all_notofocations_marked_as_read'),
        ];
    }

    public function notifications(): object
    {
        $authUserId = Auth::guard('web')->user()->id ?? 0;

        return Notification::where('user_id', $authUserId)
            ->orderBy('created_at', 'desc')
            ->paginate(10);
    }

    public function markNotificationAsRead(int $id): array
    {
        Notification::where('id', $id)->update(['readed' => 1]);

        return [
            'status'  => 'success',
            'code'    => 200,
            'message' => __('web.user.notification_marked_as_read'),
        ];
    }

    public function deleteNotification(int $id): array
    {
        Notification::where('id', $id)->delete();

        return [
            'status'  => 'success',
            'code'    => 200,
            'message' => __('web.user.notification_deleted'),
        ];
    }

    public function deleteAllNotification(): array
    {
        $authUserId = Auth::guard('web')->user()->id ?? 0;
        Notification::where('user_id', $authUserId)->delete();

        return [
            'status'  => 'success',
            'code'    => 200,
            'message' => __('web.user.all_notofocations_deleted'),
        ];
    }
}

