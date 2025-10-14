<?php

namespace App\Repositories\Eloquent;

use App\Models\Wishlist;
use App\Repositories\Contracts\UserWishlistRepositoryInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Modules\CarInfo\Models\VehicleInfo;

class UserWishlistRepository implements UserWishlistRepositoryInterface
{
    public function getWishlistData(): string
    {
        return __('web.user.wishlist');
    }

    public function addToWishlist(int $id): array
    {
        $authUserId = Auth::guard('web')->user()->id ?? 0;

        try {
            $vehicle = VehicleInfo::find($id);
            $wishlist = Wishlist::where('user_id', $authUserId)
                ->where('vehicle_id', $vehicle->id ?? '')
                ->first();

            if ($wishlist) {
                $wishlist->delete();

                return [
                    'status'  => 'success',
                    'code'    => 200,
                    'message' => __('web.user.removed_from_wishlist'),
                ];
            }

            Wishlist::create([
                'user_id'    => $authUserId,
                'vehicle_id' => $vehicle->id ?? '',
            ]);

            return [
                'status'  => 'success',
                'code'    => 200,
                'message' => __('web.user.added_to_wishlist'),
            ];
        } catch (\Throwable $th) {
            return [
                'status'  => 'error',
                'code'    => 500,
                'message' => __('web.user.error_occured'),
            ];
        }
    }

    public function getWishlistDataAjax(): Collection
    {
        $authUserId = Auth::guard('web')->user()->id ?? 0;

        return Wishlist::where('user_id', $authUserId)->get();
    }
}

