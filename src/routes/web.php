<?php

use App\Http\Controllers\BusinessTripController;
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
});

// Business trip management
Route::controller(BusinessTripController::class)
    // Requests must be authenticated
    ->middleware('auth')

    // All the routes bellow have URIs `/trips/*`
    ->prefix('trips')
    ->group(static function () {
        // Show the create form
        Route::get('/create', 'create');

        // Show trip details
        Route::get('/{trip}', 'show');

        // Show the edit form
        Route::get('/{trip}/edit', 'edit');

        // Save a newly created business trip
        // Intended for the submit button in the create form
        Route::post('/', 'store');

        // Update an existing business trip
        // Intended for the submit button in the edit form
        Route::put('/{trip}', 'update');

        // TODO: How to show index?
        // TODO: Cancel, confirm, close under a single route?
    });

// User management
// TODO: Decide which middleware to use where
Route::controller(UserController::class)
    ->group(static function () {
        // Log user in
        // Intended for the login button
        Route::post('/', 'authenticate');

        // Log user out
        Route::post('/logout', 'logout')
            ->middleware('auth');

        // Show the register form
        Route::get('/register', 'create')
            ->middleware('auth');

        // TODO: Invite?
    });
