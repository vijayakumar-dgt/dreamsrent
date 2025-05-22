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
        $authUserId = $authUser ? $authUser->getAuthIdentifier() : 0;
        $resource = $this->resource;

        return [
            'id' => $resource->id,
            'message_type' => $resource->type,
            'file_path' => uploadedAsset($resource->file),
            'message' => $resource->message,
            'created_at' => $resource->created_at->format('Y-m-d H:i:s'),
            'time' => $resource->created_at->format('h:i A'),
            'alignment' => $resource->sender_id == $authUserId ? 'right' : 'left',
            'is_sender' => $resource->sender_id == $authUserId,
            'sender_id' => $resource->sender_id,
            'receiver_id' => $resource->receiver_id,
            'sender_username' => getCurrentUserFullname($resource->sender_id),
            'sender_avatar' => $this->getAvatar($resource->sender_id),
            'receiver_username' => getCurrentUserFullname($resource->receiver_id),
            'receiver_avatar' => $this->getAvatar($resource->receiver_id),
            'admin_avatar' => $this->getAdminAvatar(),
        ];
    }

    public function getAvatar(int $userId): string
    {
        $user = User::find($userId);
        if ($user && $user->userDetail) {
            return uploadedAsset($user->userDetail->profile_image, 'profile');
        }

        return uploadedAsset('default', 'profile');
    }

    public function getAdminAvatar(): string
    {
        $user = User::where('user_type', 1)->first();
        if ($user && $user->userDetail) {
            $profileImage = uploadedAsset($user->userDetail->profile_image, 'profile');
            return $profileImage;
        }
        return uploadedAsset('default', 'profile');
    }
}
