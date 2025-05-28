<?php

use Illuminate\Support\Facades\Route;
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
use App\Http\Controllers\MessageController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\StateController;
use App\Http\Controllers\TranslationController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Session;
use Modules\GeneralSetting\Http\Controllers\LanguageController;

Route::get('/storage-linkadmin', function () {
    Artisan::call('storage:link');
    return redirect()->route('admin-login');
})->name('storage-linkadmin');

Route::group(['middleware' => ['setLocale', 'checkInstallerStatus', 'securityHeader']], function () {

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
    Route::get('admin/translations/{file}/{module}', [TranslationController::class, 'getFileTranslations'])->name('admin.translations');

    Route::group(['prefix' => 'admin', 'middleware' => 'admin'], function () {
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