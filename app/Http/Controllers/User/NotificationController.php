<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificationController extends BaseUserController
{
    public function index(Request $request): View|JsonResponse
    {
        $notifications = $this->userRepository->notifications();

        if ($request->ajax()) {
            $view = view('frontend.user.partials.notification-items', ['notifications' => $notifications])->render();

            return response()->json([
                'html'          => $view,
                'current_page'  => $notifications->currentPage(),
                'last_page'     => $notifications->lastPage(),
                'prev_page_url' => $notifications->previousPageUrl(),
                'next_page_url' => $notifications->nextPageUrl(),
                'count'         => $notifications->total(),
            ]);
        }

        return view('frontend.user.notifications', ['notifications' => $notifications]);
    }

    public function list(): JsonResponse
    {
        $response = $this->userRepository->getNotifications();

        return response()->json($response, $response['code'] ?? 200);
    }

    public function markAllAsRead(): JsonResponse
    {
        $response = $this->userRepository->markAllAsRead();

        return response()->json($response, $response['code'] ?? 200);
    }

    public function markAsRead(Request $request): JsonResponse
    {
        $response = $this->userRepository->markNotificationAsRead($request->id);

        return response()->json($response, $response['code'] ?? 200);
    }

    public function delete(Request $request): JsonResponse
    {
        $response = $this->userRepository->deleteNotification($request->id);

        return response()->json($response, $response['code'] ?? 200);
    }

    public function deleteAll(): JsonResponse
    {
        $response = $this->userRepository->deleteAllNotification();

        return response()->json($response, $response['code'] ?? 200);
    }
}
