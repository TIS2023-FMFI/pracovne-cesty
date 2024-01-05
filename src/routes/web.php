<?php

use App\Models\BusinessTrip;
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

Route::get('/', function () {
    return view('homepage');
});

Route::get('/trips/create', function () {
    return view('business-trips.create');
});

Route::get('/trips/{trip}', function ($tripId) {
    // Fetch the BusinessTrip object using the provided trip ID
    $trip = BusinessTrip::find($tripId);

    // Check if the trip exists
    if (!$trip) {
        abort(404); // Or handle the case when the trip doesn't exist
    }

    // Pass the fetched trip object to the view
    return view('business-trips.edit', ['trip' => $trip]);
});

//Route::get('/trips/{trip}', function () {
//    return view('business-trips.show', ['trip' => (object) ['start_location' => 'Bratislava', 'end_location' => 'Zohor', 'id' => 1]]);
//});

