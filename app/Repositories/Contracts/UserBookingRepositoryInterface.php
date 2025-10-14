<?php

namespace App\Repositories\Contracts;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;

interface UserBookingRepositoryInterface
{
    public function getDashboardData(): array;

    public function getUserBookings(): array;

    public function getAjaxLastBookings(Request $request): Collection;

    public function getAjaxBookings(Request $request): Collection;

    public function getBookingDetails(int $id): object;

    public function cancelBooking(Request $request): array;

    public function completeBooking(Request $request): array;

    public function startRide(Request $request): array;

    public function deleteRide(Request $request): array;

    public function getTransactionsAjax(Request $request): Collection;
}
