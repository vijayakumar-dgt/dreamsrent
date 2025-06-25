<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateUserPasswordRequest;
use App\Http\Requests\UserProfileRequest;
use App\Http\Resources\UserBookings;
use App\Http\Resources\UserWishlist;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\View\View;

class UserController extends Controller
{
    protected UserRepositoryInterface $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function dashboard(): View
    {
        $data = $this->userRepository->getDashboardData();
        return view('frontend.user.dashboard', $data);
    }

    public function bookings(): View
    {
        $data = $this->userRepository->getUserBookings();
        return view('frontend.user.bookings', $data);
    }

    public function ajaxLastBookings(Request $request): AnonymousResourceCollection
    {
        $bookings = $this->userRepository->getAjaxLastBookings($request);
        return UserBookings::collection($bookings)->additional([
            'status' => 'success',
        ]);
    }

    public function ajaxBookings(Request $request): AnonymousResourceCollection
    {
        $bookings = $this->userRepository->getAjaxBookings($request);
        return UserBookings::collection($bookings)->additional([
            'status' => 'success',
        ]);
    }

    public function bookingDetails(?int $id): JsonResponse
    {
        $booking = $this->userRepository->getBookingDetails($id);
        return response()->json([
            'status' => 'success',
            'data'   => new UserBookings($booking)
        ]);
    }

    public function cancelRide(Request $request): JsonResponse
    {
        $response = $this->userRepository->cancelBooking($request);
        return response()->json($response, $response['code'] ?? 200);
    }

    public function completeRide(Request $request): JsonResponse
    {
        $response = $this->userRepository->completeBooking($request);
        return response()->json($response, $response['code'] ?? 200);
    }

    public function startRide(Request $request): JsonResponse
    {
        $response = $this->userRepository->startRide($request);
        return response()->json($response, $response['code'] ?? 200);
    }

    public function deleteRide(Request $request): JsonResponse
    {
        $response = $this->userRepository->deleteRide($request);
        return response()->json($response, $response['code'] ?? 200);
    }

    public function wishlists(Request $request): View
    {
        $seo_title = $this->userRepository->getWishlistData();
        return view('frontend.user.wishlists', compact('seo_title'));
    }

    public function addToWishlist(Request $request): JsonResponse
    {
        $response = $this->userRepository->addToWishlist($request->id);
        return response()->json($response, $response['code'] ?? 200);
    }

    public function ajaxWishlists(Request $request): JsonResponse
    {
        $wishlists = $this->userRepository->getWishlistDataAjax();
        return response()->json([
            'status' => 'success',
            'code'   => 200,
            'data'   => UserWishlist::collection($wishlists)
        ]);
    }

    public function userprofilesettings(): View
    {
        $data = $this->userRepository->getProfileSettings();
        return view('frontend.user.usersettings', $data);
    }

    public function userprofile(UserProfileRequest $request): JsonResponse
    {
        $response = $this->userRepository->updateProfile($request);
        return response()->json($response, $response['code'] ?? 200);
    }

    public function userpreference(): View
    {
        $data = $this->userRepository->getPreferenceSettings();
        return view('frontend.user.preference', $data);
    }

    public function usernotification(): View
    {
        $data = $this->userRepository->getUserNotifications();
        return view('frontend.user.notification', $data);
    }

    public function updateNotificationSettings(Request $request): JsonResponse
    {
        $response = $this->userRepository->updateNotificationSettings($request);
        return response()->json($response, $response['code'] ?? 200);
    }

    public function usersecurity(): View
    {
        $seo_title = __('web.user.security');
        return view('frontend.user.security', compact('seo_title'));
    }

    public function checkCurrentPassword(Request $request): JsonResponse
    {
        $response = $this->userRepository->checkCurrentPassword($request);
        return response()->json($response, $response['code'] ?? 200);
    }

    public function updatePassword(UpdateUserPasswordRequest $request): JsonResponse
    {
        $response = $this->userRepository->updatePassword($request);
        return response()->json($response, $response['code'] ?? 200);
    }

    public function getSecuritySettings(): JsonResponse
    {
        $response = $this->userRepository->getSecuritySettings();
        return response()->json([
            'status' => 'success',
            'code'   => 200,
            'data'   => $response
        ]);
    }

    public function logoutDevice(Request $request): JsonResponse
    {
        $response = $this->userRepository->logoutDevice($request);
        return response()->json($response, $response['code'] ?? 200);
    }

    public function updatePreference(Request $request): JsonResponse
    {
        $response = $this->userRepository->updatePreference($request);
        return response()->json($response, $response['code'] ?? 200);
    }

    public function getPreferences(Request $request): JsonResponse
    {
        $response = $this->userRepository->getPreferences($request);
        return response()->json($response, $response['code'] ?? 200);
    }

    public function reviews(Request $request): View
    {
        $seo_title = __('web.common.reviews');
        return view('frontend.user.reviews', compact('seo_title'));
    }

    public function storeEnquiry(Request $request): JsonResponse
    {
        $response = $this->userRepository->storeEnquiry($request);
        return response()->json($response, $response['code'] ?? 200);
    }

    public function getNotifications(): JsonResponse
    {
        $response = $this->userRepository->getNotifications();
        return response()->json($response, $response['code'] ?? 200);
    }

    public function markAllAsRead(): JsonResponse
    {
        $response = $this->userRepository->markAllAsRead();
        return response()->json($response, $response['code'] ?? 200);
    }

    public function payments(Request $request): View
    {
        $seo_title = __('web.user.payments');
        return view('frontend.user.payments', compact('seo_title'));
    }

    public function ajaxTransactions(Request $request): AnonymousResourceCollection
    {
        $bookings = $this->userRepository->getTransactionsAjax($request);
        return UserBookings::collection($bookings)->additional([
            'status' => 'success',
        ]);
    }

    public function notifications(Request $request): View|JsonResponse
    {
        $notifications = $this->userRepository->notifications();

        if ($request->ajax()) {
            $view = view('frontend.user.partials.notification-items', compact('notifications'))->render();

            return response()->json([
                'html'          => $view,
                'current_page'  => $notifications->currentPage(),
                'last_page'     => $notifications->lastPage(),
                'prev_page_url' => $notifications->previousPageUrl(),
                'next_page_url' => $notifications->nextPageUrl(),
                'count'         => $notifications->total()
            ]);
        }

        return view('frontend.user.notifications', compact('notifications'));
    }

    public function markNotificationAsRead(Request $request): JsonResponse
    {
        $response = $this->userRepository->markNotificationAsRead($request->id);
        return response()->json($response, $response['code'] ?? 200);
    }

    public function deleteNotification(Request $request): JsonResponse
    {
        $response = $this->userRepository->deleteNotification($request->id);
        return response()->json($response, $response['code'] ?? 200);
    }

    public function deleteAllNotification(): JsonResponse
    {
        $response = $this->userRepository->deleteAllNotification();
        return response()->json($response, $response['code'] ?? 200);
    }

    public function deleteAccount(): JsonResponse
    {
        $response = $this->userRepository->deleteAccount();
        return response()->json($response, $response['code'] ?? 200);
    }
}
