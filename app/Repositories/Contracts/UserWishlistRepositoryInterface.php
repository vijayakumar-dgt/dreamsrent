<?php

namespace App\Repositories\Contracts;

use Illuminate\Support\Collection;

interface UserWishlistRepositoryInterface
{
    public function getWishlistData(): string;

    public function addToWishlist(int $id): array;

    public function getWishlistDataAjax(): Collection;
}
