<?php

namespace App\Repositories\Contracts;

use Illuminate\Http\Request;

interface UserNotificationRepositoryInterface
{
    public function getUserNotifications();

    public function updateNotificationSettings(Request $request);

    public function getNotifications();

    public function markAllAsRead();

    public function notifications();

    public function markNotificationAsRead(int $id);

    public function deleteNotification(int $id);

    public function deleteAllNotification();
}
