<?php

namespace Modules\Booking\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Srmklive\PayPal\Services\PayPal as PayPalClient;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Modules\Booking\Repositories\Contracts\UserBookingRepositoryInterface;

class UserBookingController extends Controller
{
    private $provider;
    protected UserBookingRepositoryInterface $userBookingRepository;
    public function __construct(UserBookingRepositoryInterface $userBookingRepository)
    {
        if (empty(env('PAYPAL_SANDBOX_CLIENT_ID')) || empty(env('PAYPAL_SANDBOX_CLIENT_SECRET'))) {
            $this->provider = null;
        } else {
            $this->provider = new PayPalClient();
            $this->provider->getAccessToken();
        }
        $this->userBookingRepository = $userBookingRepository;
    }

    public function redirectToBooking(Request $request): View|RedirectResponse
    {
        if (session()->has('intended_booking')) {
            $booking = session('intended_booking');
            session()->forget('intended_booking');
            /** @var array{slug: string, data: mixed} $booking */
            $slug = $booking['slug'];
            $data = $booking['data'];
            return view('frontend.redirect-to-booking', compact('slug', 'data'));
        }
        return redirect()->route('home');
    }

    public function index(Request $request, string $slug): View|RedirectResponse
    {
        $data = $this->userBookingRepository->getVehicleInfo($request, $slug);
        if($data && isset($data['redirect_url'])){
            return redirect()->to($data['redirect_url']);
        }
        return view('booking::user_booking.index', $data)->with($request->all());
    }

    public function getStates(int $country_id): JsonResponse
    {
        $response = $this->userBookingRepository->getStates($country_id);
        return response()->json($response);
    }

    public function getCities(int $state_id): JsonResponse
    {  
        $response = $this->userBookingRepository->getCities($state_id);
        return response()->json($response);
    }

    public function checkBooking(Request $request): JsonResponse
    {
        $response = $this->userBookingRepository->checkBooking($request);
        return response()->json($response, $response['code'] ?? 200);
    }

    public function paymentSuccess(string $transaction_id): View
    {
        $data = $this->userBookingRepository->paymentSuccess($transaction_id);
        return view("booking::user_booking.success_page", $data);
    }

    public function paymentFail(string $transaction_id): View
    {
        $booking = $this->userBookingRepository->getBooking($transaction_id);
        return view("booking::user_booking.fail_page", compact("transaction_id", "booking"));
    }

    public function userPayments(Request $request): JsonResponse
    {
        $response = $this->userBookingRepository->userPayments($request);
        if($response){
            return response()->json($response, $response['code'] ?? 200);
        }
        return response()->json(['error' => __('web.home.failed_to_create_booking')], 500);
    }

    public function paypalPaymentSuccess(Request $request): JsonResponse|RedirectResponse
    {
        $response = $this->userBookingRepository->paypalPaymentSuccess($request);
        if($response && isset($response['redirect_url'])){
            return redirect($response['redirect_url']);
        }
        return response()->json($response, $response['code'] ?? 200);
    }

    public function paypalPaymentFailed(Request $request): JsonResponse|RedirectResponse
    {
        $response = $this->userBookingRepository->paypalPaymentFailed($request);
        if($response && isset($response['redirect_url'])){
            return redirect($response['redirect_url']);
        }
        return response()->json($response, $response['code'] ?? 200);
    }

    public function stripPaymentSuccess(Request $request): JsonResponse|RedirectResponse
    {
        $response = $this->userBookingRepository->stripPaymentSuccess($request);
        if($response && isset($response['redirect_url'])){
            return redirect($response['redirect_url']);
        }
        return response()->json($response, $response['code'] ?? 200);
    }

    public function transaction(Request $request): JsonResponse
    {
        $response = $this->userBookingRepository->getTransaction($request);
        return response()->json($response, $response['code'] ?? 200);
    }

    public function getBenefits(Request $request)
    {
        $response = $this->userBookingRepository->getBenefits($request);
        return response()->json($response, $response['code'] ?? 200);
    }
}
