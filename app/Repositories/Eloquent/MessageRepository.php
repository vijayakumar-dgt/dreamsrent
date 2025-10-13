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
        $sender = currentUser();
        $receiver = User::where('user_type', 1)->first();
        $lastMessage = null;
        if ($sender instanceof \Illuminate\Contracts\Auth\Authenticatable) {
            $lastMessage = Message::where(function ($query) use ($sender) {
                $query->where(function ($query) use ($sender) {
                    $query->where('sender_id', $sender->getAuthIdentifier())
                        ->orWhere('receiver_id', $sender->getAuthIdentifier());
                });
            })->orderBy('id', 'desc')->first();
        }
        $seo_title = __('web.user.messages');
        return [
            'sender'      => $sender,
            'receiver'    => $receiver,
            'lastMessage' => $lastMessage,
            'seo_title'   => $seo_title
        ];
    }

    public function sendMessage(Request $request): array
    {
        $messageType = $request->messageType ?? 'text';
        $senderId    = $request->sender_id;
        $receiverId  = $request->receiver_id;

        $publishMessage = null;

        // Handle file message
        if ($messageType === 'file' && $request->hasFile('file')) {
            $publishMessage = $this->storeFileMessage($request, $senderId, $receiverId);
        }

        // Handle text message
        if (!empty($request->message)) {
            $publishMessage = $this->storeTextMessage($request, $senderId, $receiverId);
        }

        if ($publishMessage === null) {
            return [
                'success' => false,
                'message' => __('admin.others.message_encoding_failed')
            ];
        }

        // Publish message via MQTT
        $payload = json_encode([
            'sender_id'   => $senderId,
            'receiver_id' => $receiverId,
            'message'     => $publishMessage,
            'type'        => $messageType,
        ]);

        if ($payload === false) {
            return [
                'success' => false,
                'message' => __('admin.others.message_encoding_failed')
            ];
        }

        (new MqttService())->publish($request->topic, $payload);

        return [
            'success' => true,
            'message' => __('admin.others.message_sent_success')
        ];
    }

    /**
     * Store a file message and return the file path
     */
    private function storeFileMessage(Request $request, $senderId, $receiverId): ?string
    {
        $file = $request->file('file');
        if (!$file) return null;

        $filename  = $file->getClientOriginalName();
        $mime_type = $file->getClientMimeType();
        $size      = $file->getSize();
        $path      = uploadFile($file, 'chat', $filename);

        $message = new Message();
        $message->sender_id = $senderId;
        $message->receiver_id = $receiverId;
        $message->type = 'file';
        $message->file = $path;
        $message->mime_type = $mime_type;
        $message->size = $size !== null ? (string)$size : null;
        $message->message = $filename ?? '';
        $message->save();

        return $path;
    }

    /**
     * Store a text message and return the text
     */
    private function storeTextMessage(Request $request, $senderId, $receiverId): string
    {
        $message = new Message();
        $message->sender_id = $senderId;
        $message->receiver_id = $receiverId;
        $message->message = $request->message;
        $message->save();

        return $request->message;
    }

    public function fetchMessages(Request $request)
    {
        $last_offset = $request->last_offset ?? "";
        $perPage = $last_offset ? (intval($last_offset)) : 10;
        if ($perPage > 10) {
            $perPage = 10;
        }
        /** @var \Illuminate\Contracts\Auth\Authenticatable|null $authUser */
        $authUser = currentUser();
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

        $nextOffset = $offset === 0 ? null : max(0, $offset - $perPage);
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
                'id'         => $lastMessage->id,
                'message'    => $lastMessage->type == 'text' ? $messageText : '<i class="fa fa-link"></i> ' . $messageText,
                'created_at' => $lastMessage->created_at ? $lastMessage->created_at->diffForHumans() : null,
            ];
        }
        return [
            'status'       => true,
            'code'         => 200,
            'messages'     => MessageResource::collection($messages),
            'next_offset'  => $nextOffset,
            'last_offset'  => $offset,
            'last_message' => $lastMessageResp
        ];
    }
}
