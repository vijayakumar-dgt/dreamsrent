<?php

namespace App\Http\Controllers;

use App\Http\Resources\MessageResource;
use App\Models\Message;
use App\Models\User;
use App\Repositories\Contracts\MessageRepositoryInterface;
use App\Services\MqttService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
        $sender = current_user();
        return view('admin.chat.messages', compact('users', 'sender'));
    }

    public function fetchMessages(Request $request): JsonResponse
    {
        $response = $this->messageRepository->fetchMessages($request);
        return response()->json($response, $response['code'] ?? 200);
    }
}
