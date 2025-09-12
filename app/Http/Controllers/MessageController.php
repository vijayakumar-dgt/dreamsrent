<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Repositories\Contracts\MessageRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MessageController extends Controller
{
    protected MessageRepositoryInterface $messageRepository;

    public function __construct(MessageRepositoryInterface $messageRepository)
    {
        $this->messageRepository = $messageRepository;
    }

    public function index(): View
    {
        $data = $this->messageRepository->getUserData();
        return view('frontend.user.messages', $data);
    }

    public function sendMessage(Request $request): JsonResponse
    {
        $response = $this->messageRepository->sendMessage($request);
        return response()->json($response, $response['code'] ?? 200);
    }

    public function adminMessages(): View
    {
        $users = User::where('user_type', 3)->orderBy('id', 'desc')->get();
        $sender = currentUser();
        return view('admin.chat.messages', ['users' => $users, 'sender' => $sender]);
    }

    public function fetchMessages(Request $request): JsonResponse
    {
        $response = $this->messageRepository->fetchMessages($request);
        return response()->json($response, $response['code'] ?? 200);
    }
}
