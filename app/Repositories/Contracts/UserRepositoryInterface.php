<?php

namespace App\Repositories\Contracts;

interface UserRepositoryInterface extends
    UserBookingRepositoryInterface,
    UserWishlistRepositoryInterface,
    UserProfileRepositoryInterface,
    UserNotificationRepositoryInterface,
    UserSecurityRepositoryInterface
{
}
