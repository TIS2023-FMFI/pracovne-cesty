<?php

namespace App\Http\Controllers;

use App\Mail\SimpleMail;
use App\Models\BusinessTrip;
use App\Models\Contribution;
use App\Models\Country;
use App\Models\SppSymbol;
use App\Models\Transport;
use App\Models\TripPurpose;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class BusinessTripController extends Controller
{

    /**
     * Returning view with details from all trips
     */
    public function index() {
        $trip = BusinessTrip::all();
        return view('business-trips.show', ['trip' => $trip]);
    }

    /**
     * Returning view with the trip details
     * No need in implementation as by now
     */
    public function show(BusinessTrip $trip) {
        return view('business-trips.show', ['trip' => $trip]);
    }

    /**
     * Returning view with the form for adding of the new trip
     */
    public function create() {
        return view('business-trips.create');
    }

    /**
     * Parsing data from the $request in form
     * Also managing uploaded files from the form
     * Redirecting to the homepage
     * Sending mail with mail component to admin
     */
    public function store(Request $request) {
        return redirect()->route('components.homepage');

    }

    /**
     * Returning the view with the trip editing form
     */
    public function edit(BusinessTrip $trip) {
        return view('business-trips.edit', ['trip' => $trip]);

    }

    /**
     * Validation as in create(), should check if user is admin/not
     * Redirecting to the trip editing form
     * Sending mail with mail component to admin
     */
    public function update(Request $request, BusinessTrip $trip) {
        return redirect()->route('business-trips.edit', $trip);

    }

    /**
     * Next 3 functions could be combined to changeState(Request $request, BusinessTrip $b)
     * Check for admin needed?
     *
     * Updating state of the trip to cancelled
     * Adding cancellation reason
     */
    public function cancel(BusinessTrip $trip) {
        //Cancel the trip and add cancellation reason
        $trip->update(['state' => 'canceled', 'cancellation_reason' => request('cancellation_reason')]);

        //Send cancellation email to admin
        $message = '';
        $recipient = 'admin@example.com';
        $viewTemplate = 'emails.cancellation_request_admin';

        // Create an instance of the SimpleMail class
        $email = new SimpleMail($message, $recipient, $viewTemplate);

        // Send the email
        Mail::to($recipient)->send($email);

        return redirect()->route('business-trips.show', $trip);

    }

    /**
     * Same as update(), updating state of the trip to confirmed
     */
    public function confirm(BusinessTrip $trip) {
        //Confirm the trip
        $trip->update(['state' => 'confirmed']);

        return redirect()->route('business-trips.show', $trip);

    }

    /**
     * Same as update, updating state to closed
     */
    public function close(BusinessTrip $trip) {
        //Close the trip
        $trip->update(['state' => 'closed']);
        
        return redirect()->route('business-trips.show', $trip);

    }
}
