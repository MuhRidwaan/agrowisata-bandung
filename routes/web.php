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
| Admin Area
|--------------------------------------------------------------------------
*/

Route::middleware(['auth','role:admin'])->group(function () {

    Route::get('/dashboard', function () {
        return view('backend.dashboard');
    })->name('dashboard');




});

/*
|--------------------------------------------------------------------------
| Auth User
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');

    Route::resource('paket-tours', PaketTourController::class);

    Route::resource('paket-tour-photos', PaketTourPhotoController::class); // Gallery Foto

    Route::resource('pricing-tiers', PricingTierController::class); // Pricing Tier

    Route::resource('tanggal-available', TanggalAvailableController::class); // Tanggal Available

    // LIST DATA
    Route::get('/users', [UserController::class, 'index'])
        ->name('users.index');

    // FORM CREATE
    Route::get('/users/create', [UserController::class, 'create'])
        ->name('users.create');

    // STORE DATA
    Route::post('/users', [UserController::class, 'store'])
        ->name('users.store');

    // FORM EDIT
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])
        ->name('users.edit');

    // UPDATE DATA
    Route::put('/users/{user}', [UserController::class, 'update'])
        ->name('users.update');

    // DELETE DATA
    Route::delete('/users/{user}', [UserController::class, 'destroy'])
        ->name('users.destroy');

        Route::get('/users/export', [UserController::class, 'export'])
    ->name('users.export');

});


Route::middleware('auth')->group(function () {

    Route::resource('roles', RoleController::class);

});

Route::middleware(['auth','role:admin'])->group(function () {

    // ======================
    // ROUTES BOOKING
    // ======================
    Route::get('/bookings', 
        [BookingController::class, 'index']
    )->name('bookings.index');

    Route::get('/bookings/create', 
        [BookingController::class, 'create']
    )->name('bookings.create');

    Route::post('/bookings', 
        [BookingController::class, 'store']
    )->name('bookings.store');

    Route::get('/bookings/{booking}/edit', 
        [BookingController::class, 'edit']
    )->name('bookings.edit');

    Route::put('/bookings/{booking}', 
        [BookingController::class, 'update']
    )->name('bookings.update');

    Route::delete('/bookings/{booking}', 
        [BookingController::class, 'destroy']
    )->name('bookings.destroy');


    // ======================
    // ROUTES PAYMENTS
    // ======================
    Route::get('/payments',
        [PaymentController::class, 'index']
    )->name('payments.index');

    Route::post('/payments/{payment}/paid',
        [PaymentController::class, 'markAsPaid']
    )->name('payments.paid');

});


require __DIR__.'/auth.php';
