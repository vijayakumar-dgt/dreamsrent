<?php

namespace App\Http\Controllers;

use App\Http\Requests\AdminUserRequest;
use App\Repositories\Contracts\AdminUserRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminUserController extends Controller
{
    protected AdminUserRepositoryInterface $adminUserRepository;

    public function __construct(AdminUserRepositoryInterface $adminUserRepository)
    {
        $this->adminUserRepository = $adminUserRepository;
    }

    public function index(): View
    {
        $data = $this->adminUserRepository->index();
        return view('admin.users', $data);
    }

    public function store(AdminUserRequest $request): JsonResponse
    {
        $response = $this->adminUserRepository->store($request);
        return response()->json($response, $response['code']);
    }

    public function list(Request $request): JsonResponse
    {
        $response = $this->adminUserRepository->list($request);
        return response()->json($response, $response['code']);
    }

    public function edit(Request $request): JsonResponse
    {
        $id = $request->id;
        $response = $this->adminUserRepository->edit($id);
        return response()->json($response, $response['code']);
    }

    public function delete(Request $request): JsonResponse
    {
        $id = $request->id;
        $response = $this->adminUserRepository->delete($id);
        return response()->json($response, $response['code']);
    }

    public function getNotifications(Request $request): JsonResponse
    {
        $response = $this->adminUserRepository->getNotifications();
        return response()->json($response, $response['code']);
    }

    public function markAllAsRead(Request $request): JsonResponse
    {
        $response = $this->adminUserRepository->markAllAsRead();
        return response()->json($response, $response['code']);
    }

    public function notifications(Request $request): View|JsonResponse
    {
        $notifications = $this->adminUserRepository->notifications($request);
        if ($request->ajax()) {
            $view = view('admin.partials.notification-items', ['notifications' => $notifications])->render();
            return response()->json([
                'html'          => $view,
                'current_page'  => $notifications->currentPage(),
                'last_page'     => $notifications->lastPage(),
                'prev_page_url' => $notifications->previousPageUrl(),
                'next_page_url' => $notifications->nextPageUrl(),
                'count'         => $notifications->total()
            ]);
        }
        return view('admin.partials.notifications', ['notifications' => $notifications]);
    }

    public function markNotificationAsRead(Request $request): JsonResponse
    {
        $response = $this->adminUserRepository->markNotificationAsRead($request);
        return response()->json($response, $response['code']);
    }

    public function deleteNotification(Request $request): JsonResponse
    {
        $id = $request->id;
        $response = $this->adminUserRepository->deleteNotification($id);
        return response()->json($response, $response['code']);
    }

    public function deleteAllNotification(Request $request): JsonResponse
    {
        $response = $this->adminUserRepository->deleteAllNotification($request);
        return response()->json($response, $response['code']);
    }
}
