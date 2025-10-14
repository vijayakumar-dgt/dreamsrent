<?php

namespace App\Repositories\Contracts;

use Illuminate\Http\Request;

interface UserNotificationRepositoryInterface
{
    public function getUserNotifications(): array;

    public function updateNotificationSettings(Request $request): array;

    public function getNotifications(): array;

    public function markAllAsRead(): array;

    public function notifications(): object;

    public function markNotificationAsRead(int $id): array;

    public function deleteNotification(int $id): array;

    public function deleteAllNotification(): array;
}
