<?php

namespace Modules\Booking\Repositories\Contracts;

use Illuminate\Http\Request;

interface QuotationRepositoryInterface
{
    public function create();
    public function store(Request $request);
    public function edit(Request $request, string|int|null $id);
    public function bookingList(Request $request);
    public function getBookingDetails(Request $request);
    public function reservationViewDetails(Request $request, string|int|null $id);
    public function delete(Request $request);
}
