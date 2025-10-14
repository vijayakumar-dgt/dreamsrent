<?php

namespace App\Repositories\Contracts;

interface UserRepositoryInterface
{
    public function bookings(): UserBookingRepositoryInterface;

    public function wishlist(): UserWishlistRepositoryInterface;

    public function profile(): UserProfileRepositoryInterface;

    public function notifications(): UserNotificationRepositoryInterface;

    public function security(): UserSecurityRepositoryInterface;
}
