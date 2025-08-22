<?php

namespace App\Repositories\Contracts;

use Illuminate\Http\Request;

interface AdminUserRepositoryInterface
{
    public function index();

    public function store(Request $request);

    public function list(Request $request);

    public function edit(int $id);

    public function delete(int $id);

    public function getNotifications();

    public function markAllAsRead();

    public function notifications(Request $request);

    public function markNotificationAsRead(Request $request);

    public function deleteNotification(int $id);

    public function deleteAllNotification();
}
