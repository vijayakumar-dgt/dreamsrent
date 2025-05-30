<?php

namespace App\Repositories\Contracts;

use Illuminate\Http\Request;

interface UserRepositoryInterface
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
    public function getWishlistData();
    public function addToWishlist(int $id);
    public function getWishlistDataAjax();
    public function getProfileSettings();
    public function updateProfile(Request $request);
    public function getPreferenceSettings();
    public function getUserNotifications();
    public function updateNotificationSettings(Request $request);
    public function checkCurrentPassword(Request $request);
    public function updatePassword(Request $request);
    public function getSecuritySettings();
    public function logoutDevice(Request $request);
    public function updatePreference(Request $request);
    public function getPreferences(Request $request);
    public function storeEnquiry(Request $request);
    public function getNotifications();
    public function markAllAsRead();
    public function getTransactionsAjax(Request $request);
    public function notifications();
    public function markNotificationAsRead(int $id);
    public function deleteNotification(int $id);
    public function deleteAllNotification();
    public function deleteAccount();
}