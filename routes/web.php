<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| Public
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

/*
|--------------------------------------------------------------------------
| Admin Area
|--------------------------------------------------------------------------
*/

Route::middleware(['auth','role:admin'])->group(function () {

    Route::get('/dashboard', function () {
        return view('dashboard');
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
});

require __DIR__.'/auth.php';
