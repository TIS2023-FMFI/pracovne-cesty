<?php

use App\Http\Controllers\BusinessTripController;
use App\Http\Controllers\SPPController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// General homepage
Route::get('/', static function () {
    return view('homepage');
})
    ->middleware('guest')
    ->name('guest-home');

// Show trip index for logged-in user
Route::get('/dashboard', [BusinessTripController::class, 'index'])
    ->middleware('auth')
    ->name('user-home');

// Business trip management
Route::controller(BusinessTripController::class)
    ->middleware('auth')
    ->prefix('trips')
    ->group(static function () {
        // Show the create form
        Route::get('/create', 'create')
            ->name('trip-create');

        // Show trip details
        Route::get('/{trip}', 'show')
            ->name('trip-details');

        // Show the edit form
        Route::get('/{trip}/edit', 'edit')
            ->name('trip-edit');

        // Save a newly created business trip
        // Intended for the submit button in the create form
        Route::post('/', 'store')
            ->name('trip-store');

        // Update an existing business trip
        // Intended for the submit button in the edit form
        Route::put('/{trip}', 'update')
            ->name('trip-update');

        // Trip modifiers
        Route::put('/{trip}/cancel', 'cancel')
            ->name('trip-cancel');

        Route::put('/{trip}/confirm', 'confirm')
            ->name('trip-confirm');

        Route::put('/{trip}/close', 'close')
            ->name('trip-close');

        // Export the trip details as the selected document
        Route::post('/{trip}/export', 'exportPdf')
            ->name('export-pdf');
    });

// User management
Route::controller(UserController::class)
    ->prefix('user')
    ->group(static function () {
        // Log user in
        // Intended for the login button
        Route::post('/', 'authenticate')
            ->middleware('guest')
            ->name('login');

        Route::middleware('auth')
            ->group(static function () {
                // Log user out
                Route::post('/logout', 'logout')
                    ->name('logout');

                // Show the register form
                Route::get('/register', 'create')
                    ->name('register');

                // Invite a new user
                // Intended for the invitation submit button
                Route::post('/invite', 'invite')
                    ->name('invite');
            });
    });

// SPP management
Route::controller(SPPController::class)
    ->middleware('auth')
    ->prefix('spp')
    ->group(static function () {
        // Save a newly created SPP symbol
        // Intended for the submit button in the SPP form
        Route::post('/', 'store');

        // Show the SPP management form
        Route::get('/{spp}', 'manage');

        // Deactivate an SPP symbol
        Route::post('/{spp}/deactivate', 'deactivate');
    });
