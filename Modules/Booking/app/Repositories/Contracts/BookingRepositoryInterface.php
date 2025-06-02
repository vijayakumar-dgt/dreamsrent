<?php

namespace Modules\Booking\Repositories\Contracts;

use Illuminate\Http\Request;

interface BookingRepositoryInterface
{
   public function create(): array;
   public function getCustomerDetails(Request $request);
   public function getFilterVehicles(Request $request);
   public function store(Request $request);
   public function edit(Request $request, string|int|null $id);
   public function delete(Request $request);
   public function complete(Request $request);
   public function bookingList(Request $request);
   public function getBookingDetails(Request $request);
   public function reservationViewDetails(Request $request, string|int|null $id);
   public function calculateTotalPrice(Request $request);
   public function cancelBooking(Request $request);

}