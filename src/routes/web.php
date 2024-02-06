<?php

use App\Http\Controllers\BusinessTripController;
use App\Http\Controllers\SPPController;
use App\Http\Controllers\UserController;
use App\Models\InvitationLink;
use Illuminate\Http\Request;
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

// Home
Route::get('/', static function () {
    if (Auth::check()) {
        // Show trip index for logged-in user
        return BusinessTripController::index();
    }

    // General homepage
    return view('homepage');
})
    ->name('homepage');


// Business trip management
Route::controller(BusinessTripController::class)
    ->prefix('trips')
    ->name('trip.')
    ->group(static function () {
        Route::middleware('role:traveller|admin')
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
                Route::put('/{trip}/add-comment', 'addComment')
                    ->name('add-comment');

                // Export the trip details as the selected document
                Route::get('/{trip}/export', 'exportPdf')
                    ->name('export');

                // Download attachment in the trip
                Route::get('/{trip}/attachment', 'getAttachment')
                    ->name('attachment');
            });

        Route::middleware('role:traveller')
            ->group(static function () {
                Route::put('/{trip}/request-cancel', 'requestCancellation')
                    ->name('request-cancel');
            });

        Route::middleware('role:admin')
            ->group(static function () {
                // Trip modifiers
                Route::put('/{trip}/cancel', 'cancel')
                    ->name('cancel');

                Route::put('/{trip}/confirm', 'confirm')
                    ->name('confirm');

                Route::put('/{trip}/close', 'close')
                    ->name('close');
            });
    });


// User management
Route::controller(UserController::class)
    ->prefix('user')
    ->name('user.')
    ->group(static function () {
        // Invite a new user
        // Intended for the invitation submit button
        Route::post('/invite', 'invite')
            ->middleware('role:admin')
            ->name('invite');

        // Log user out
        Route::post('/logout', 'logout')
            ->middleware('role:traveller|admin')
            ->name('logout');

        Route::middleware('guest')
            ->group(static function () {
                // Log user in
                // Intended for the login button
                Route::post('/', 'authenticate')
                    ->name('login');

                // Show the registration form based on a given token
                Route::get('/register', static function (Request $request) {
                    $token = $request->query('token');

                    if (!InvitationLink::isValid($token)) {
                        abort(403, 'Invalid token');
                    }

                    $link = InvitationLink::where('token', $token)->first();
                    return UserController::create($link->email);
                })
                    ->name('register');

                // Submit the registration form
                Route::post('/register/store', 'store')
                    ->name('register-submit');
            });
    });


// SPP management
Route::controller(SPPController::class)
    ->prefix('spp')
    ->name('spp.')
    ->middleware('role:admin')
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
