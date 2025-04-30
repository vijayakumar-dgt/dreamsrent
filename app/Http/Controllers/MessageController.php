<?php

namespace App\Http\Controllers;

use App\Http\Resources\MessageResource;
use App\Models\Message;
use App\Models\User;
use App\Services\MqttService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MessageController extends Controller
{
    public function index()
    {
        $sender = current_user();
        $receiver = User::where('user_type', 1)->first();
        $lastMessage = Message::where(function ($query) use ($sender, $receiver) {
            $query->where(function ($query) use ($sender, $receiver) {
                $query->where('sender_id', $sender->id)
                      ->orWhere('receiver_id', $sender->id);
            });
        })->orderBy('id', 'desc')->first();
        $seo_title = __('web.user.messages');
        return view('frontend.user.messages', compact('sender', 'receiver', 'lastMessage', 'seo_title'));
    }

    public function sendMessage(Request $request)
    {
        // DB::beginTransaction();
        // try {
        if ($request->messageType == 'file' && $request->hasFile('file')) {
            $foldername = 'chat_attachments';
            $file       = $request->file('file');
            $filename   = $file->getClientOriginalName();
            $mime_type  = $file->getClientMimeType();
            $size       = $file->getSize();
            $path = uploadFile($file, $foldername, $filename);
            $_message = new Message();
            $_message->sender_id = $request->sender_id;
            $_message->receiver_id = $request->receiver_id;
            $_message->type        = 'file';
            $_message->file        = $path;
            $_message->mime_type   = $mime_type;
            $_message->size        = $size;
            $_message->message     = $filename;
            $_message->save();
        }
        if (!empty($request->message)) {
            $message = new Message();
            $message->sender_id = $request->sender_id;
            $message->receiver_id = $request->receiver_id;
            $message->message     = $request->message;
            $message->save();
        }
            $publishMessage = $request->messageType == 'file' ? $path : $request->message;
            $mqtt = new MqttService();
            $mqtt->publish($request->topic, $publishMessage);
            $response = [
                'success' => true,
                'message' => __('admin.others.message_send_success')
            ];

        //     DB::commit();
        // } catch (\Throwable $th) {
        //     DB::rollBack();
        //     $response = [
        //         'success' => false,
        //         'message' => $th->getMessage()
        //     ];
        // }

            return response()->json($response);
    }

    public function adminMessages()
    {
        $users = User::where('user_type', 3)->orderBy('id', 'desc')->get();
        $sender = current_user();
        return view('admin.chat.messages', compact('users', 'sender'));
    }

    public function fetchMessages(Request $request)
    {
        $last_offset = $request->last_offset ?? "";
        $perPage = $last_offset ? (intval($last_offset)) : 10;
        if ($perPage > 10) {
            $perPage = 10;
        }
        $authUser = current_user();
        $authUserId = $authUser ? $authUser->id : 0;
        $messagePartnerId = $request->user_id;
        $totalMessages = Message::where(function ($query) use ($authUserId, $messagePartnerId) {
                            $query->where('sender_id', $authUserId)
                                ->where('receiver_id', $messagePartnerId);
        })
                        ->orWhere(function ($query) use ($authUserId, $messagePartnerId) {
                            $query->where('sender_id', $messagePartnerId)
                                ->where('receiver_id', $authUserId);
                        })
                        ->count();
        if (!$request->has('offset') || $request->offset == "") {
            $offset = max(0, ($totalMessages - $perPage) + 1);
        } else {
            $offset = max(0, (int) $request->offset);
        }
        $messages = Message::where(function ($query) use ($authUserId, $messagePartnerId) {
                                $query->where('sender_id', $authUserId)
                                    ->where('receiver_id', $messagePartnerId);
        })
                            ->orWhere(function ($query) use ($authUserId, $messagePartnerId) {
                                $query->where('sender_id', $messagePartnerId)
                                    ->where('receiver_id', $authUserId);
                            })
            ->orderBy('id', 'asc')
            ->offset($offset)
            ->limit($perPage)
            ->get();

        if ($offset === 0) {
            $nextOffset = null;
        } else {
            $nextOffset = max(0, $offset - $perPage);
        }
        $lastMessage = Message::where(function ($query) use ($authUserId, $messagePartnerId) {
            $query->where('sender_id', $authUserId)
                ->where('receiver_id', $messagePartnerId);
        })
            ->orWhere(function ($query) use ($authUserId, $messagePartnerId) {
                $query->where('sender_id', $messagePartnerId)
                    ->where('receiver_id', $authUserId);
            })
            ->orderBy('id', 'desc')
            ->first();
        $lastMessageResp = null;
        if ($lastMessage) {
            $messageText = strlen($lastMessage->message) > 20 ?
            substr($lastMessage->message, 0, 20) . '...' : $lastMessage->message;
            $lastMessageResp = [
                'id' => $lastMessage->id,
                'message' => $lastMessage->type == 'text' ? $messageText : '<i class="fa fa-link"></i> ' . $messageText,
                'created_at' => $lastMessage->created_at->diffForHumans(),
            ];
        }
        return response()->json([
            'status' => true,
            'code' => 200,
            'messages' => MessageResource::collection($messages),
            'next_offset' => $nextOffset,
            'last_offset' => $offset,
            'last_message' => $lastMessageResp
        ]);
    }
}
