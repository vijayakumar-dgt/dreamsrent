<?php

namespace App\Repositories\Contracts;

use Illuminate\Http\Request;

interface UserBookingRepositoryInterface
{
    public function getDashboardData();

    public function getUserBookings();

    public function getAjaxLastBookings(Request $request);

    public function getAjaxBookings(Request $request);

    public function getBookingDetails(int $id);

    public function cancelBooking(Request $request);

    public function completeBooking(Request $request);

    public function startRide(Request $request);

    public function deleteRide(Request $request);

    public function getTransactionsAjax(Request $request);
}
