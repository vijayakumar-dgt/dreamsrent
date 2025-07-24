<?php

namespace Modules\Booking\Repositories\Contracts;

use Illuminate\Http\Request;

interface UserBookingRepositoryInterface
{
    public function getVehicleInfo(Request $request, string $slug);

    public function getStates(int $country_id);

    public function getCities(int $state_id);

    public function checkBooking(Request $request);

    public function paymentSuccess(string $transaction_id);

    public function getBooking(string $transaction_id);

    public function userPayments(Request $request);

    public function paypalPaymentSuccess(Request $request);

    public function paypalPaymentFailed(Request $request);

    public function stripPaymentSuccess(Request $request);

    public function getTransaction(Request $request);

    public function getBenefits(Request $request);
}
