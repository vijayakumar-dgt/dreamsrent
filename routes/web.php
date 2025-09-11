<?php

use App\Http\Controllers\BlogController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\TranslationController;
use App\Http\Controllers\user\auth\UserLoginRegisterController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WalletController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;
use Modules\Booking\Http\Controllers\UserBookingController;
use Modules\CarInfo\Http\Controllers\CarInfoController;
use Modules\GeneralSetting\Http\Controllers\Admin\LanguageController;
use Modules\Page\Http\Controllers\PageController;

Route::get('/documentation', function () {
    return response()->file(public_path('documentation/index.html'));
});
Route::get('/storage-link', function () {
    Artisan::call('storage:link');
    return redirect()->route('home');
})->name('storage-link');

Route::group(['middleware' => ['checkInstallerStatus', 'setLocaleUser', 'securityHeader']], function () {

    Route::get('/user/translations/{file}/{module}', [TranslationController::class, 'getFileTranslations'])->name('translations');
    Route::get('/', [PageController::class, 'pageBuilderApi'])->middleware('checkInstallerStatus', 'maintenance')->name('home');
    Route::middleware('maintenance')->controller(HomeController::class)->group(function () {
        Route::get('/vehicles', 'list')->name('list');
        Route::get('/vehicle-details/{slug}', 'vehicleDetails')->name('vehicleDetails');
        Route::get('search-locations', 'searchLocations');
        Route::get('/contact-us', 'contactUs')->name('contact-us');
    });
    Route::get('theme/{slug}', [PageController::class, 'pageBuilderApi'])->name('theme')->middleware('maintenance');

    Route::middleware('maintenance')->group(function () {
        Route::get('/login', [UserLoginRegisterController::class, 'userLogin'])->name('user-login');
        Route::get('/register', [UserLoginRegisterController::class, 'userRegister'])->name('user-register');
        Route::get('/user/forgot-password', [UserLoginRegisterController::class, 'forgotPassword'])->name('user-forgot-password');
        Route::get('/user/reset-password', [UserLoginRegisterController::class, 'resetPassword'])->name('user-reset-password');
        Route::post('/user/check-current-password-reset', [UserController::class, 'checkCurrentPassword']);
        Route::post('/user/reset-password-update', [UserLoginRegisterController::class, 'resetPasswordUpdate']);
        Route::post('/user/delete-account', [UserController::class, 'deleteAccount'])->name('user.delete-account');

        Route::post('/user/register', [UserLoginRegisterController::class, 'register'])->name('user-registersave');
        Route::post('/user/login', [UserLoginRegisterController::class, 'login'])->name('user-logincheck');
        Route::get('/user-logout', [UserLoginRegisterController::class, 'userlogout'])->name('user.logout');
        Route::post('/otp-settings', [UserLoginRegisterController::class, 'getOtpSettings']);
        Route::post('/verify-otp', [UserLoginRegisterController::class, 'verifyOtp']);
    });

    //user profile
    Route::prefix('user')->middleware(['customer', 'maintenance'])->controller(UserController::class)->group(function () {
        Route::get('dashboard', 'dashboard')->name('user.dashboard');
        Route::get('bookings', 'bookings')->name('user.bookings');
        Route::post('ajax-last-bookings', 'ajaxLastBookings')->name('user.ajaxLastBookings');
        Route::post('ajax-bookings', 'ajaxBookings')->name('user.ajaxBookings');
        Route::get('booking-details/{id}', 'bookingDetails')->name('user.bookingDetails');
        Route::post('cancel-ride', 'cancelRide')->name('user.cancelRide');
        Route::post('complete-ride', 'completeRide')->name('user.completeRide');
        Route::post('start-ride', 'startRide')->name('user.startRide');
        Route::post('delete-ride', 'deleteRide')->name('user.deleteRide');
        Route::get('wishlists', 'wishlists')->name('user.wishlists');
        Route::post('add-to-wishlist', 'addToWishlist')->name('user.addToWishlist');
        Route::post('ajax-wishlists', 'ajaxWishlists')->name('user.ajaxWishlists');
        Route::get('usersettings', 'userprofilesettings')->name('user.usersettings');
        Route::get('preference', 'userpreference')->name('user.preference');
        Route::post('preference/update', 'updatePreference')->name('user.update-preference');
        Route::post('get-preferences', 'getPreferences')->name('user.get-preference');
        Route::get('integration', 'userintegration')->name('user.integration');
        Route::get('notification', 'usernotification')->name('user.notification');
        Route::get('security', 'usersecurity')->name('user.security');
        Route::post('check-current-password', 'checkCurrentPassword')->name('user.check-current-password');
        Route::post('update-password', 'updatePassword')->name('user.update-password');
        Route::get('security-details', 'getSecuritySettings')->name('user.security-details');
        Route::get('reviews', 'reviews')->name('user.reviews');
        Route::get('payments', 'payments')->name('user.payments');
        Route::post('ajax-transactions', 'ajaxTransactions')->name('user.ajax-transactions');
        Route::post('update-notification-settings', 'updateNotificationSettings')->name('user.update-notification-settings');
    });

    Route::post('/user/store_enquiry', [UserController::class, 'storeEnquiry'])->name('user.store-enquiry');
    Route::post('user/reviews-list', [ReviewController::class, 'userReviewsList'])->name('user.reviews-list');
    Route::post('user/save-newsletter-subscriber', [NewsletterController::class, 'store'])->name('user.save-newsletter-subscriber');

    Route::prefix('user')->middleware(['customer', 'maintenance'])->controller(WalletController::class)->group(function () {
        Route::get('wallet', 'wallet')->name('user.wallet');
        Route::post('addwallet', 'addWallet')->name('user.addwallet');
        Route::get('wallet-list', 'walletHistoryList')->name('user.walletHistoryList');
        Route::get('paypal-payment-success-wallet', 'paypalPaymentSuccessWallet')->name('user.paypalPaymentSuccessWallet');
        Route::get('stripe-payment-success', 'stripePaymentSuccessWallet')->name('user.stripe.payment.success.wallet');
        Route::get('payment-failed', 'paymentFailed')->name('payment-failed');
    });
    Route::post('/validate-email', [UserLoginRegisterController::class, 'validateEmail']);

    Route::middleware('maintenance')->group(function () {
        Route::post('/booking-checkout/{slug}', [UserBookingController::class, 'index'])->name('booking.checkout');
        Route::get('redirect-to-booking', [UserBookingController::class, 'redirectToBooking'])->name('user.booking.redirect');
        Route::get('/get-states/{country_id}', [UserBookingController::class, 'getStates']);
        Route::get('/get-cities/{state_id}', [UserBookingController::class, 'getCities']);
        Route::post('/create/payments', [UserBookingController::class, 'userPayments']);
        Route::get('/paypal-payment-success', [UserBookingController::class, 'paypalPaymentSuccess'])
        ->name('paypal.payment.success');
        Route::get('/booking/payment-success/{transaction_id}', [UserBookingController::class, 'paymentSuccess'])
        ->name('payment.success.page');
        Route::get('/strip-payment-success', [UserBookingController::class, 'stripPaymentSuccess'])
        ->name('strip.payment.success');
        Route::get('/paypal-payment-failed', [UserBookingController::class, 'paypalPaymentFailed'])
        ->name('paypal.payment.fail');
        Route::get('/booking/payment-fail/{transaction_id}', [UserBookingController::class, 'paymentFail'])
        ->name('payment.success.fail');
    });
    Route::middleware('maintenance')->prefix('user')->controller(ReviewController::class)->group(function () {
        Route::post('add-review', 'addReview')->name('user.add-review');
        Route::post('add-reply-review', 'addReply')->name('user.add-reply');
    });
    Route::post('get-reviews', [ReviewController::class, 'reviewsList'])->middleware('maintenance')->name('reviews-list');
    Route::post('review/delete', [ReviewController::class, 'delete'])->name('review-delete');

    Route::middleware(['maintenance', 'customer'])->prefix('user')
    ->controller(MessageController::class)->group(function () {
        Route::get('messages', 'index')->name('user.messages');
    });

    Route::post('user/send-message', [MessageController::class, 'sendMessage'])
    ->middleware('customer')->name('user.send-message');
    Route::post('user/fetch-messages', [MessageController::class, 'fetchMessages'])->middleware('customer')->name('fetch-messages');

    Route::post('admin/send-message', [MessageController::class, 'sendMessage'])->middleware('admin')->name('user.send-message');
    Route::post('admin/fetch-messages', [MessageController::class, 'fetchMessages'])->middleware('admin')
    ->name('fetch-messages');

    Route::get('maintenance', [HomeController::class, 'maintenance'])->name('maintenance');
    Route::get('/pages/{slug}', [PageController::class, 'getPage'])->name('pages');

    Route::get('blogs', [BlogController::class, 'blogList'])->name('blogs.list');
    Route::get('blog-details/{id}', [BlogController::class, 'blogDetail'])->name('blogs.detail');
    Route::post('/blog-review', [BlogController::class, 'storeReview'])->name('blogs.review.store');
    Route::post('/userprofile', [UserController::class, 'userprofile'])->name('userprofile');
    Route::post('user/mark-all-notifications-as-read', [UserController::class, 'markAllAsRead']);
    Route::get('user/get-notifications', [UserController::class, 'getNotifications'])->name('user.notifications');
    Route::get('user/notifications', [UserController::class, 'notifications'])->name('user.notifications')->middleware('customer');
    Route::post('user/mark-notification-as-read', [UserController::class, 'markNotificationAsRead']);
    Route::post('user/delete-notification', [UserController::class, 'deleteNotification']);
    Route::post('user/delete-all-notifications', [UserController::class, 'deleteAllNotification']);

    Route::get('vehicle-list-api', [CarInfoController::class,'vehicleLists'])->middleware('web');
    Route::post('vehicle-list-detail-api', [CarInfoController::class,'vehicleDetailsList'])->middleware('web');
    Route::get('recent-transation', [UserBookingController::class,'transaction'])->middleware('web');
    Route::post('vehicle-intrset-list', [CarInfoController::class,'vehicleIntrestLists'])->middleware('web');

    Route::post('user/flag-change-language', [LanguageController::class,'userFlagChangeLanguage'])
    ->name('user.flag-change-language');
});
