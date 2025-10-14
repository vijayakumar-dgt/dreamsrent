<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Contracts\UserBookingRepositoryInterface;
use App\Repositories\Contracts\UserNotificationRepositoryInterface;
use App\Repositories\Contracts\UserProfileRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\Contracts\UserSecurityRepositoryInterface;
use App\Repositories\Contracts\UserWishlistRepositoryInterface;

class UserRepository implements UserRepositoryInterface
{
    public function __construct(
        private readonly UserBookingRepositoryInterface $bookingRepository,
        private readonly UserWishlistRepositoryInterface $wishlistRepository,
        private readonly UserProfileRepositoryInterface $profileRepository,
        private readonly UserNotificationRepositoryInterface $notificationRepository,
        private readonly UserSecurityRepositoryInterface $securityRepository
    ) {
    }

    public function bookings(): UserBookingRepositoryInterface
    {
        return $this->bookingRepository;
    }

    public function wishlist(): UserWishlistRepositoryInterface
    {
        return $this->wishlistRepository;
    }

    public function profile(): UserProfileRepositoryInterface
    {
        return $this->profileRepository;
    }

    public function notifications(): UserNotificationRepositoryInterface
    {
        return $this->notificationRepository;
    }

    public function security(): UserSecurityRepositoryInterface
    {
        return $this->securityRepository;
    }
}

