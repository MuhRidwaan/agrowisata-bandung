<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
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


/*
|--------------------------------------------------------------------------
| Public
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('frontend.home');
});

/*
|--------------------------------------------------------------------------
| Auth User (Bisa diakses user yang login)
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    // ================= PROFILE =================
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // ================= PAKET TOUR & OPTIONS =================
    Route::resource('paket-tours', PaketTourController::class);
    Route::resource('paket-tour-photos', PaketTourPhotoController::class);
    Route::resource('pricingtiers', PricingTierController::class);
    Route::resource('pricingrules', PricingRuleController::class);

    Route::resource('tanggal-available', TanggalAvailableController::class);

    // ================= DATA USERS =================
    Route::get('/users/export', [UserController::class, 'export'])->name('users.export');
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

    // ================= ROLES =================
    Route::resource('roles', RoleController::class);

});


/*
|--------------------------------------------------------------------------
| Admin Area (Hanya bisa diakses role Admin)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth','role:admin'])->group(function () {

    // ================= DASHBOARD =================
    Route::get('/dashboard', function () {
        return view('backend.dashboard');
    })->name('dashboard');

    // ================= BOOKINGS =================
    Route::get('/bookings', [BookingController::class, 'index'])->name('bookings.index');
    Route::get('/bookings/create', [BookingController::class, 'create'])->name('bookings.create');
    Route::post('/bookings', [BookingController::class, 'store'])->name('bookings.store');
    Route::get('/bookings/{booking}/edit', [BookingController::class, 'edit'])->name('bookings.edit');
    Route::put('/bookings/{booking}', [BookingController::class, 'update'])->name('bookings.update');
    Route::delete('/bookings/{booking}', [BookingController::class, 'destroy'])->name('bookings.destroy');

    // ================= PAYMENTS =================
    Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
    Route::post('/payments/{payment}/paid', [PaymentController::class, 'markAsPaid'])->name('payments.paid');
    Route::post('/midtrans/callback', [PaymentController::class, 'callback']);
    Route::get('/payments/{payment}/invoice', [PaymentController::class, 'invoice'])->name('payments.invoice');
    Route::post('/payments/{payment}/cancel', [PaymentController::class, 'markAsFailed'])->name('payments.cancel');
    // ================= VENDORS =================
    Route::get('/vendors/{id}/contact', [VendorController::class, 'contact'])->name('vendors.contact');
    Route::resource('vendors', VendorController::class);

    // ================= REVIEWS =================
    Route::get('review', [ReviewController::class, 'index'])->name('review.index');
    Route::post('review/{id}/approve', [ReviewController::class, 'approve'])->name('review.approve');
    Route::post('review/{id}/reject', [ReviewController::class, 'reject'])->name('review.reject');
    Route::post('review/{id}/reply', [ReviewController::class, 'reply'])->name('review.reply');

    // ================= WHATSAPP SETTING =================
    Route::resource('whatsappsetting', WhatsappSettingController::class);

    // ================= AREAS =================
    Route::resource('areas', AreaController::class);


    // Frontend
    Route::get('/home', function () {
        return view('frontend.detail');
    })->name('detail');
});

require __DIR__.'/auth.php';