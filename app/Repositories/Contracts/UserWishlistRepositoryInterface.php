<?php

namespace App\Repositories\Contracts;

interface UserWishlistRepositoryInterface
{
    public function getWishlistData();

    public function addToWishlist(int $id);

    public function getWishlistDataAjax();
}
