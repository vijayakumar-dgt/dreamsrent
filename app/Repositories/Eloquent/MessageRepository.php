<?php

namespace App\Repositories\Eloquent;

use App\Http\Resources\MessageResource;
use App\Models\Message;
use App\Models\User;
use App\Repositories\Contracts\MessageRepositoryInterface;
use App\Services\MqttService;
use Illuminate\Http\Request;

class MessageRepository implements MessageRepositoryInterface
{
    public function getUserData(): array
    {
        $sender = current_user();
        $receiver = User::where('user_type', 1)->first();
        $lastMessage = null;
        if ($sender) {
            $lastMessage = Message::where(function ($query) use ($sender) {
                $query->where(function ($query) use ($sender) {
                    $query->where('sender_id', $sender->getAuthIdentifier())
                        ->orWhere('receiver_id', $sender->getAuthIdentifier());
                });
            })->orderBy('id', 'desc')->first();
        }
        $seo_title = __('web.user.messages');
        return [
            'sender' => $sender,
            'receiver' => $receiver,
            'lastMessage' => $lastMessage,
            'seo_title' => $seo_title
        ];
    }

    public function sendMessage(Request $request): array
    {
        if ($request->messageType == 'file' && $request->hasFile('file')) {
            $foldername = 'chat';
            $file       = $request->file('file');
            $filename   = $file ? $file->getClientOriginalName() : null;
            $mime_type  = $file ? $file->getClientMimeType() : null;
            $size       = $file ? $file->getSize() : null;
            $path = $file ? uploadFile($file, $foldername, $filename) : null;
            $_message = new Message();
            $_message->sender_id = $request->sender_id;
            $_message->receiver_id = $request->receiver_id;
            $_message->type = 'file';
            $_message->file = $path;
            $_message->mime_type = $mime_type;
            $_message->size = $size !== null ? (string) $size : null;
            $_message->message = $filename ?? '';
            $_message->save();
        }
        if (!empty($request->message)) {
            $message = new Message();
            $message->sender_id = $request->sender_id;
            $message->receiver_id = $request->receiver_id;
            $message->message = $request->message;
            $message->save();
        }
        $publishMessage = ($request->messageType == 'file' && isset($path)) ? $path : $request->message;
        $payload = [
            'sender_id' => $request->sender_id,
            'receiver_id' => $request->receiver_id,
            'message' => $publishMessage,
            'type' => $request->messageType,
        ];
        $payload = json_encode($payload);
        if ($payload === false) {
            return [
                'success' => false,
                'message' => __('admin.others.message_encoding_failed')
            ];
        }
        $mqtt = new MqttService();
        $mqtt->publish($request->topic, $payload);
        return [
            'success' => true,
            'message' => __('admin.others.message_sent_success')
        ];
    }

    public function fetchMessages(Request $request)
    {
        $last_offset = $request->last_offset ?? "";
        $perPage = $last_offset ? (intval($last_offset)) : 10;
        if ($perPage > 10) {
            $perPage = 10;
        }
        /** @var \Illuminate\Contracts\Auth\Authenticatable|null $authUser */
        $authUser = current_user();
        $authUserId = $authUser ? $authUser->getAuthIdentifier() : 0;
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
                'created_at' => $lastMessage->created_at ? $lastMessage->created_at->diffForHumans() : null,
            ];
        }
        return [
            'status' => true,
            'code' => 200,
            'messages' => MessageResource::collection($messages),
            'next_offset' => $nextOffset,
            'last_offset' => $offset,
            'last_message' => $lastMessageResp
        ];
    }
}