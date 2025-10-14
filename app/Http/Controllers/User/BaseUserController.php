<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\UserBookingRepositoryInterface;
use App\Repositories\Contracts\UserNotificationRepositoryInterface;
use App\Repositories\Contracts\UserProfileRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\Contracts\UserSecurityRepositoryInterface;
use App\Repositories\Contracts\UserWishlistRepositoryInterface;

abstract class BaseUserController extends Controller
{
    protected UserRepositoryInterface $userRepository;
    protected UserBookingRepositoryInterface $bookingRepository;
    protected UserWishlistRepositoryInterface $wishlistRepository;
    protected UserProfileRepositoryInterface $profileRepository;
    protected UserNotificationRepositoryInterface $notificationRepository;
    protected UserSecurityRepositoryInterface $securityRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
        $this->bookingRepository = $userRepository->bookings();
        $this->wishlistRepository = $userRepository->wishlist();
        $this->profileRepository = $userRepository->profile();
        $this->notificationRepository = $userRepository->notifications();
        $this->securityRepository = $userRepository->security();
    }
}
