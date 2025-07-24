<?php

namespace App\Repositories\Contracts;

use Illuminate\Http\Request;

interface CalendarRepositoryInterface
{
    public function index();

    public function getCalenderBooking(Request $request);

    public function getBookingDetail(int $bookingId);
}
