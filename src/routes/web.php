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
    ->name('homepage');

// Show trip index for logged-in user
Route::get('/dashboard', [BusinessTripController::class, 'index'])
    ->middleware('auth')
    ->name('dashboard');

// Business trip management
Route::controller(BusinessTripController::class)
    ->middleware('auth')
    ->prefix('trips')
    ->name('trip.')
    ->group(static function () {
        // Show the create form
        Route::get('/create', 'create')
            ->name('create');

        // Show trip details
        Route::get('/{trip}', 'show')
            ->name('details');

        // Show the edit form
        Route::get('/{trip}/edit', 'edit')
            ->name('edit');

        // Save a newly created business trip
        // Intended for the submit button in the create form
        Route::post('/', 'store')
            ->name('store');

        // Update an existing business trip
        // Intended for the submit button in the edit form
        Route::put('/{trip}', 'update')
            ->name('update');

        // Add report for the trip
        Route::put('/{trip}/add-report', 'addReport')
            ->name('add-report');

        // Trip modifiers
        Route::put('/{trip}/request-cancel', 'requestCancellation')
            ->name('request-cancel');

        Route::put('/{trip}/cancel', 'cancel')
            ->name('cancel');

        Route::put('/{trip}/confirm', 'confirm')
            ->name('confirm');

        Route::put('/{trip}/close', 'close')
            ->name('close');

        // Export the trip details as the selected document
        Route::post('/{trip}/export', 'exportPdf')
            ->name('export');

        // Download attachment in the trip
        Route::post('/{trip}/attachment', 'getAttachment')
            ->name('attachment');
    });

// User management
Route::controller(UserController::class)
    ->prefix('user')
    ->name('user.')
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
    ->name('spp.')
    ->group(static function () {
        // Save a newly created SPP symbol
        // Intended for the submit button in the SPP form
        Route::post('/', 'store')
            ->name('store');

        // Show the SPP management form
        Route::get('/', 'manage')
            ->name('manage');

        // Deactivate an SPP symbol
        Route::put('/deactivate', 'deactivate')
            ->name('deactivate');
    });
