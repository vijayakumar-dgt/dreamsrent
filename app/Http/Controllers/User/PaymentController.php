<?php

namespace App\Http\Controllers\User;

use App\Http\Resources\UserBookings;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PaymentController extends BaseUserController
{
    public function index(): View
    {
        $seoTitle = __('web.user.payments');

        return view('frontend.user.payments', ['seo_title' => $seoTitle]);
    }

    public function transactions(Request $request): AnonymousResourceCollection
    {
        $bookings = $this->bookingRepository->getTransactionsAjax($request);

        return UserBookings::collection($bookings)->additional([
            'status' => 'success',
        ]);
    }
}
