<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class MessageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $authUser = current_user();
        $authUserId = $authUser ? $authUser->id : 0;

        return [
           'id' => $this->id,
           'message_type'   => $this->type,
           'file_path' => uploadedAsset($this->file),
           'message' => $this->message,
           'created_at' => $this->created_at->format('Y-m-d H:i:s'),
           'time' => $this->created_at->format('h:i A'),
           'alignment' => $this->sender_id == $authUserId ? 'right' : 'left',
           'is_sender' => $this->sender_id == $authUserId,
           'sender_id' => $this->sender_id,
           'receiver_id' => $this->receiver_id,
           'sender_username' => $this->sender->name ?? "",
           'sender_avatar' => $this->getAvatar($this->sender_id),
           'receiver_username' => $this->receiver->name ?? "",
           'receiver_avatar' => $this->getAvatar($this->receiver_id),
           'admin_avatar' => $this->getAdminAvatar(),
        ];
    }

    public function getAvatar($userId)
    {
        $user = User::find($userId);
        return uploadedAsset($user->userDetail ? $user->userDetail->profile_image : 'default', 'profile');
    }

    public function getAdminAvatar()
    {
        $user = User::where('user_type', 1)->first();
        return uploadedAsset($user->userDetail ? $user->userDetail->profile_image : 'default', 'profile');
    }
}
