<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PaketTourController;
use App\Http\Controllers\PaketTourPhotoController;
use App\Http\Controllers\PricingTierController;
use App\Http\Controllers\TanggalAvailableController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\VendorController;
use App\Http\Controllers\WhatsappSettingController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\PricingRuleController;
use App\Http\Controllers\AreaController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FrontendController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SettingController;
use Illuminate\Support\Facades\File;

/*
|--------------------------------------------------------------------------
| FRONTEND (PUBLIC)
|--------------------------------------------------------------------------
*/

// Homepage
Route::get('/', [FrontendController::class, 'home'])->name('home');

// Detail Paket
Route::get('/detail/{id}', [FrontendController::class, 'detail'])->name('detail');

// Booking
Route::get('/booking/{id}', [FrontendController::class, 'booking'])->name('booking');
Route::post('/booking/store', [FrontendController::class, 'storeBooking'])->name('booking.store');

// Payment
Route::get('/payment/{id}', [FrontendController::class, 'payment'])->name('payment');
Route::get('/pembayaran/lanjut/{booking_code}', [FrontendController::class, 'resumePayment'])->name('payment.resume');
Route::get('/pembayaran/status/{booking_code}', [FrontendController::class, 'pendingBookingStatus'])->name('payment.status');

// Success Page
Route::get('/success', [FrontendController::class, 'success'])->name('success');

// Midtrans Callback (public, no auth, no CSRF)
Route::post('/midtrans/callback', [PaymentController::class, 'callback'])->name('midtrans.callback');

// Ulasan
Route::post('/reviews/store', [ReviewController::class, 'store'])->name('reviews.store');

// Public storage fallback for environments without storage:link
Route::get('/media/storage/{path}', function ($path) {
    $fullPath = storage_path('app/public/' . $path);

    abort_unless(File::exists($fullPath), 404);

    return response()->file($fullPath);
})->where('path', '.*')->name('public.storage');


/*
|--------------------------------------------------------------------------
| AUTH USER
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    // PROFILE
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // PAKET TOUR
    Route::get('paket-tours/export', [PaketTourController::class, 'export'])->name('paket-tours.export');
    Route::post('paket-tours/import', [PaketTourController::class, 'import'])->name('paket-tours.import');
    Route::resource('paket-tours', PaketTourController::class);
    Route::resource('paket-tour-photos', PaketTourPhotoController::class);
        Route::delete('paket-tour-photos/delete-by-paket/{paket_tour_id}', [PaketTourPhotoController::class, 'destroyByPaket'])->name('paket-tour-photos.delete-by-paket');
    Route::resource('pricingtiers', PricingTierController::class);
    Route::resource('pricingrules', PricingRuleController::class);

    // AVAILABLE DATE
    Route::get('tanggal-available/export', [TanggalAvailableController::class, 'export'])->name('tanggal-available.export');
    Route::post('tanggal-available/import', [TanggalAvailableController::class, 'import'])->name('tanggal-available.import');
    Route::get('tanggal-available/download-template', [TanggalAvailableController::class, 'downloadTemplate'])->name('tanggal-available.download-template');
    Route::resource('tanggal-available', TanggalAvailableController::class);

    // USERS
    Route::get('/users/export', [UserController::class, 'export'])->name('users.export');
    Route::resource('users', UserController::class);

    // ROLES
    Route::resource('roles', RoleController::class);
});


/*
|--------------------------------------------------------------------------
| ADMIN AREA
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {

    // DASHBOARD (Super Admin & Vendor)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // BOOKINGS (Super Admin & Vendor)
    Route::resource('bookings', BookingController::class);

    // PAYMENTS (Super Admin & Vendor)
    Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
    Route::post('/payments/{payment}/paid', [PaymentController::class, 'markAsPaid'])->name('payments.paid');
    Route::post('/payments/{payment}/cancel',[PaymentController::class, 'markAsFailed'])->name('payments.cancel');
    Route::get('/payments/{payment}/invoice', [PaymentController::class, 'invoice'])->name('payments.invoice');
    Route::post('payments/{id}/send-email', [PaymentController::class, 'sendEmail'])->name('payments.send_email');

    // REVIEWS (Super Admin & Vendor)
    Route::get('/review', [ReviewController::class, 'index'])->name('review.index');
    Route::post('/review/{id}/approve', [ReviewController::class, 'approve'])->name('review.approve');
    Route::post('/review/{id}/reject', [ReviewController::class, 'reject'])->name('review.reject');
    Route::post('/review/{id}/reply', [ReviewController::class, 'reply'])->name('review.reply');
    Route::delete('/review/{id}', [ReviewController::class, 'destroy'])->name('review.destroy');

    // REPORTS (Super Admin & Vendor)
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/sales', [ReportController::class, 'salesReport'])->name('sales');
        Route::get('/booking', [ReportController::class, 'bookingReport'])->name('booking');
        Route::get('/performance', [ReportController::class, 'tourPerformanceReport'])->name('performance');

        // Super Admin Only Reports
        Route::middleware(['role:Super Admin'])->group(function () {
            Route::get('/user', [ReportController::class, 'userReport'])->name('user');
            Route::get('/vendor-revenue', [ReportController::class, 'vendorRevenueReport'])->name('vendor_revenue');
            Route::get('/transaction-logs', [ReportController::class, 'transactionLogs'])->name('transaction_logs');
        });
    });

    // SUPER ADMIN ONLY
    Route::middleware(['role:Super Admin'])->group(function () {
        // VENDORS
        Route::get('/vendors/{id}/contact', [VendorController::class, 'contact'])->name('vendors.contact');
        Route::resource('vendors', VendorController::class);

        // AREAS
        Route::resource('areas', AreaController::class);

        // WHATSAPP SETTINGS
        Route::resource('whatsappsetting', WhatsappSettingController::class);

        // GLOBAL SETTINGS
        Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
        Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');
        Route::get('/settings/delete-logo/{id}', [SettingController::class, 'deleteLogo'])->name('settings.deleteLogo');
    });
});


Route::get('/pembayaran/invoice/{booking_code}', [App\Http\Controllers\PaymentController::class, 'publicInvoice'])->name('frontend.invoice');


require __DIR__.'/auth.php';
