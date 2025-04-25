<?php

use App\Http\Controllers\admin\CalanderController;
use App\Http\Controllers\admin\DashboardController;
use App\Http\Controllers\admin\LoginController;
use App\Http\Controllers\admin\PaymentController;
use App\Http\Controllers\admin\InvoiceController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\auth\ForgotpasswordController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\StateController;
use App\Http\Controllers\TranslationController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;
use Modules\Page\Http\Controllers\PageController;
use App\Http\Controllers\user\auth\UserLoginRegisterController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WalletController;
use Modules\Booking\Http\Controllers\UserBookingController;
use Illuminate\Support\Facades\Session;
use Modules\CarInfo\Http\Controllers\CarInfoController;
use Modules\CarInfo\Http\Controllers\MaintenanceController;
use Modules\GeneralSetting\Http\Controllers\LanguageController;

Route::get('/documentation', function () {
    return response()->file(public_path('documentation/index.html'));
});
Route::get('/storage-link', function () {
    Artisan::call('storage:link');
    return redirect()->route('home');
})->name('storage-link');

Route::get('/storage-linkadmin', function () {
    Artisan::call('storage:link');
    return redirect()->route('admin-login');
})->name('storage-linkadmin');

Route::group(['middleware' => ['setLocale', 'checkInstallerStatus']], function () {

Route::get('/db-backup', function () {
    try {
        Artisan::call('backup:database');
        Session::flash('success', 'Database backup completed successfully.');
    } catch (\Exception $e) {
        Session::flash('error', 'Backup failed: ' . $e->getMessage());
    }
    return redirect()->route('admin.database-settings');
})->name('backup');

Route::get('/system-backup', function () {
    try {
        Artisan::call('backup:system');
        Session::flash('success', 'Database backup completed successfully.');
    } catch (\Exception $e) {
        Session::flash('error', 'Backup failed: ' . $e->getMessage());
    }

    return redirect()->route('admin.system-backup-settings');
})->name('system-backup');

Route::get('/clear', function () {
    Artisan::call('cache:clear');
    Artisan::call('route:clear');
    Artisan::call('config:clear');
    Artisan::call('optimize:clear');
    return 'Cleared!';
});

Route::get('/admin', [DashboardController::class, 'index'])->middleware(['admin', 'permission'])->name('dashboard');
Route::get('admin/login', [LoginController::class, 'index'])->name('admin-login');
Route::post('admin/verify-login', [LoginController::class, 'verifyLogin'])->name('verify-login');
Route::get('admin-logout', [LoginController::class, 'logout'])->middleware('admin')->name('admin.logout');
Route::get('forgot-password', [ForgotpasswordController::class, 'index'])->name('forgot-password');
Route::post('forgot-password/send-otp', [ForgotpasswordController::class, 'sendOtp'])->name('send-otp');
Route::get('forgot-password/verify-otp', [ForgotpasswordController::class, 'verifyOtp'])->name('verify-otp');
Route::post('forgot-password/resend-otp', [ForgotpasswordController::class, 'resendOtp'])->name('send-otp');
Route::post('forgot-password/confirm-otp', [ForgotpasswordController::class, 'confirmOtp'])->name('confirm-otp');
Route::get('reset-password', [ForgotpasswordController::class, 'resetPassword'])->name('reset-password');
Route::post('forgot-password/update-password', [ForgotpasswordController::class, 'updatePassword'])->name('update-password');

Route::group(['prefix' => 'admin', 'middleware' => 'admin'], function () {

    Route::get('/translations/{file}/{module}', [TranslationController::class, 'getFileTranslations'])->name('admin.translations');

    //Country
    Route::get('country', [CountryController::class, 'index'])->name('country.index')->middleware('permission');
    Route::post('country/store', [CountryController::class, 'store'])->name('country.store');
    Route::get('country/datatable', [CountryController::class, 'list'])->name('country.list');
    Route::get('country/edit/{id}', [CountryController::class, 'edit'])->name('country.edit');
    Route::post('country/update', [CountryController::class, 'update'])->name('country.update');
    Route::post('country/delete', [CountryController::class, 'delete'])->name('country.delete');
    Route::post('country/delete-bulk', [CountryController::class, 'bulkDelete'])->name('country.bulkDelete');
    //State
    Route::get('state', [StateController::class, 'index'])->name('state.index')->middleware('permission');
    Route::post('state/store', [StateController::class, 'store'])->name('state.store');
    Route::get('state/datatable', [StateController::class, 'list'])->name('state.list');
    Route::get('state/edit/{id}', [StateController::class, 'edit'])->name('state.edit');
    Route::post('state/update', [StateController::class, 'update'])->name('state.update');
    Route::post('state/delete', [StateController::class, 'delete'])->name('state.delete');
    Route::post('state/delete-bulk', [StateController::class, 'bulkDelete'])->name('state.bulkDelete');
    //city
    Route::get('city', [CityController::class, 'index'])->name('city.index')->middleware('permission');
    Route::post('city/store', [CityController::class, 'store'])->name('city.store');
    Route::get('city/datatable', [CityController::class, 'list'])->name('city.list');
    Route::get('city/edit/{id}', [CityController::class, 'edit'])->name('city.edit');
    Route::post('city/update', [CityController::class, 'update'])->name('city.update');
    Route::post('city/delete', [CityController::class, 'delete'])->name('city.delete');
    Route::post('city/delete-bulk', [CityController::class, 'bulkDelete'])->name('city.bulkDelete');
    //Payment Transation
    Route::get('payments', [PaymentController::class, 'index'])->name('payment.payment')->middleware('permission');
    Route::post('payments-info', [PaymentController::class, 'paymentList'])->name('payment.list');
    //calander
    Route::get('calendar', [CalanderController::class, 'index'])->name('calendar.index');
    Route::post('/calendar-info', [CalanderController::class, 'getCalenderBooking'])->name('calendar.info');
    Route::get('calendar-detail', [CalanderController::class, 'getBookingDetail'])->name('calendar.booking.info');
    // Customers
    Route::get('/customers', [CustomerController::class, 'index'])->name('admin.customers')->middleware('permission');
    Route::post('/customer/save', [CustomerController::class, 'store'])->name('admin.save-customer');
    Route::post('/customer/list', [CustomerController::class, 'list'])->name('admin.customer-list');
    Route::get('/customer/edit/{id}', [CustomerController::class, 'edit'])->name('admin.customer-edit');
    Route::get('/customer-details/{id}', [CustomerController::class, 'customerDetails'])->name('admin.customer-details');
    Route::get('/customer-details/{id}/recent-rents', [CustomerController::class, 'customerDetails'])->name('admin.customer-recent-rents');
    Route::post('/customer/delete', [CustomerController::class, 'delete'])->name('admin.customer-delete');
    // Users
    Route::get('/users', [AdminUserController::class, 'index'])->name('admin.users')->middleware('permission');
    Route::prefix('user')->group(function () {
        Route::post('/save', [AdminUserController::class, 'store'])->name('admin.save-user');
        Route::post('/list', [AdminUserController::class, 'list'])->name('admin.user-list');
        Route::get('/edit/{id}', [AdminUserController::class, 'edit'])->name('admin.user-edit');
        Route::post('/delete', [AdminUserController::class, 'delete'])->name('admin.user-delete');
    });

    Route::get('get-notifications', [AdminUserController::class, 'getNotifications'])->name('admin.notifications');
    Route::post('mark-all-notifications-as-read', [AdminUserController::class, 'markAllAsRead']);
    Route::get('/notifications', [AdminUserController::class, 'notifications'])->name('admin.notifications');
    Route::post('/mark-notification-as-read', [AdminUserController::class, 'markNotificationAsRead']);
    Route::post('/delete-notification', [AdminUserController::class, 'deleteNotification']);
    Route::post('/delete-all-notifications', [AdminUserController::class, 'deleteAllNotification']);

      //Invoices
      Route::get('invoices', [InvoiceController::class, 'index'])->name('admin.invoice')->middleware('permission');
      Route::get('add-invoice', [InvoiceController::class, 'addInvoice'])->name('admin.addInvoice')->middleware('permission');
      Route::post('store', [InvoiceController::class, 'store'])->name('admin.invoiceStore');
      Route::get('edit-invoice/{id}', [InvoiceController::class, 'edit'])->name('invoices.edit')->middleware('permission');
      Route::get('delete-invoices/{id}', [InvoiceController::class, 'destroy'])->name('invoices.destroy');
      Route::post('update-invoice/{id}', [InvoiceController::class, 'update'])->name('update.invoice');

    // Newsletters
    Route::get('/newsletters', [NewsletterController::class, 'index'])->name('admin.newsletters')->middleware('permission');
    Route::post('/newsletter/list', [NewsletterController::class, 'list'])->name('admin.newsletter-list');
    Route::post('/newsletter/delete', [NewsletterController::class, 'delete'])->name('admin.newsletter-delete');
    Route::post('/send-newsletter', [NewsletterController::class, 'sendNewsletter'])->name('admin.send-newsletter');

    Route::post('/flag-change-language', [LanguageController::class, 'changeLanguage']);
    // Reviews
    Route::get('/reviews', [ReviewController::class, 'adminReviews'])->name('admin.reviews')->middleware('permission');
    Route::post('/reviews/list', [ReviewController::class, 'adminReviewsList'])->name('admin.reviews-list');
    Route::post('review/delete', [ReviewController::class, 'delete'])->name('admin.review-delete');

});

Route::prefix('admin')->middleware('admin')->controller(MessageController::class)->group(function () {
    Route::get('messages', 'adminMessages')->name('admin.messages');
});


});


// USER ROUTES //

Route::group(['middleware' => ['setLocaleUser', 'checkInstallerStatus']], function () {

Route::get('/user/translations/{file}/{module}', [TranslationController::class, 'getFileTranslations'])->name('translations');
Route::get('/', [PageController::class, 'pageBuilderApi'])->middleware('maintenance')->name('home');
Route::middleware('maintenance')->controller(HomeController::class)->group(function () {
    Route::get('/vehicles', 'list')->name('list');
    Route::get('/vehicle-details/{slug}', 'vehicleDetails')->name('vehicleDetails');
    Route::get('search-locations','searchLocations');
    Route::get('/contact-us', 'contactUs')->name('contact-us');
    Route::get('test','test');
});
Route::middleware('maintenance')->group(function () {
    Route::get('/login', [UserLoginRegisterController::class, 'userLogin'])->name('user-login');
    Route::get('/register', [UserLoginRegisterController::class, 'userRegister'])->name('user-register');
    Route::get('/user/forgot-password', [UserLoginRegisterController::class, 'forgotPassword'])->name('user-forgot-password');
    Route::get('/user/reset-password', [UserLoginRegisterController::class, 'resetPassword'])->name('user-reset-password');
    Route::post('/user/check-current-password-reset', [UserController::class, 'checkCurrentPassword']);
    Route::post('/user/reset-password-update', [UserLoginRegisterController::class, 'resetPasswordUpdate']);

    Route::post('/user/register', [UserLoginRegisterController::class, 'register'])->name('user-registersave');
    Route::post('/user/login', [UserLoginRegisterController::class, 'login'])->name('user-logincheck');
    Route::get('/user-logout', [UserLoginRegisterController::class, 'userlogout'])->name('user.logout');
    Route::post('/otp-settings', [UserLoginRegisterController::class, 'getOtpSettings']);
    Route::post('/verify-otp', [UserLoginRegisterController::class, 'verifyOtp']);
});

//user profile
Route::prefix('user')->middleware('customer', 'maintenance')->controller(UserController::class)->group(function () {
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
});
Route::post('/user/store_enquiry', [UserController::class, 'storeEnquiry'])->name('user.store-enquiry');

Route::post('user/reviews-list', [ReviewController::class, 'userReviewsList'])->name('user.reviews-list');
Route::post('user/save-newsletter-subscriber', [NewsletterController::class, 'store'])->name('user.save-newsletter-subscriber');

Route::prefix('user')->middleware('customer', 'maintenance')->controller(WalletController::class)->group(function () {
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
    Route::get('/paypal-payment-success', [UserBookingController::class, 'paypalPaymentSuccess'])->name('paypal.payment.success');
    Route::get('/booking/payment-success/{transaction_id}', [UserBookingController::class, 'paymentSuccess'])->name('payment.success.page');
    Route::get('/strip-payment-success', [UserBookingController::class, 'stripPaymentSuccess'])->name('strip.payment.success');
});
Route::middleware('maintenance')->prefix('user')->controller(ReviewController::class)->group(function () {
    Route::post('add-review', 'addReview')->name('user.add-review');
    Route::post('add-reply-review', 'addReply')->name('user.add-reply');
});
Route::post('get-reviews', [ReviewController::class, 'reviewsList'])->middleware('maintenance')->name('reviews-list');
Route::post('review/delete', [ReviewController::class, 'delete'])->name('review-delete');

Route::middleware(['maintenance', 'customer'])->prefix('user')->controller(MessageController::class)->group(function () {
    Route::get('messages', 'index')->name('user.messages');
});

Route::post('user/send-message', [MessageController::class, 'sendMessage'])->middleware('customer')->name('user.send-message');
Route::post('user/fetch-messages', [MessageController::class, 'fetchMessages'])->middleware('customer')->name('fetch-messages');

Route::post('admin/send-message', [MessageController::class, 'sendMessage'])->middleware('admin')->name('user.send-message');
Route::post('admin/fetch-messages', [MessageController::class, 'fetchMessages'])->middleware('admin')->name('fetch-messages');

Route::get('maintenance',[HomeController::class, 'maintenance'])->name('maintenance');
Route::get('/pages/{slug}', [PageController::class, 'getPage'])->name('pages');

Route::get('blogs',[BlogController::class, 'BlogList'])->name('blogs.list');
Route::get('blog-grid',[BlogController::class, 'BlogGrid'])->name('blogs.grid');
Route::get('blog-details/{id}',[BlogController::class, 'BlogDetail'])->name('blogs.detail');
Route::post('/blog-review', [BlogController::class, 'storeReview'])->name('blogs.review.store');
Route::post('/userprofile', [UserController::class, 'userprofile'])->name('userprofile');
Route::post('user/mark-all-notifications-as-read', [UserController::class, 'markAllAsRead']);
Route::get('user/get-notifications', [UserController::class, 'getNotifications'])->name('user.notifications');
Route::get('user/notifications', [UserController::class, 'notifications'])->name('user.notifications');
Route::post('user/mark-notification-as-read', [UserController::class, 'markNotificationAsRead']);
Route::post('user/delete-notification', [UserController::class, 'deleteNotification']);
Route::post('user/delete-all-notifications', [UserController::class, 'deleteAllNotification']);

Route::get('vehicle-list-api', [CarInfoController::class,'vehicleLists'])->middleware('web');
Route::post('vehicle-list-detail-api', [CarInfoController::class,'vehicleDetailsList'])->middleware('web');
Route::get('recent-transation', [UserBookingController::class,'transaction'])->middleware('web');
Route::get('vehicle-intrset-list', [CarInfoController::class,'vehicleIntrestLists'])->middleware('web');

Route::post('user/flag-change-language',[LanguageController::class,'userFlagChangeLanguage'])->name('user.flag-change-language');

});
