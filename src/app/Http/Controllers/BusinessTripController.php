<?php

namespace App\Http\Controllers;

use App\Mail\SimpleMail;
use App\Models\BusinessTrip;
use App\Models\Contribution;
use App\Models\Country;
use App\Models\SppSymbol;
use App\Models\Transport;
use App\Models\TripPurpose;
use DateTime;
use DateInterval;
use DatePeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use App\Enums\TripState;
use App\Enums\TripType;

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
        $validatedData = $request->validate([
            //Add more fields and validation rules
            'place' => 'required|varchar|max:200',
            'datetime_start' => 'required|datetime',
        ]);

        // Set the type of trip based on the selected country
        $selectedCountry = $request->input('country');
        $validatedData['type'] = $selectedCountry === 'Slovensko' ? TripType::DOMESTIC : TripType::FOREIGN;

        //Logic to store the trip based on the validated data
        $trip = new BusinessTrip($validatedData);

        //Handle file uploads
        if ($request->hasFile('file-upload-id')) {
            $file = $request->file('file-upload-id');

            //Store the file in the storage/app/trips directory
            $upload_name = $file->storeAs('trips', 'trip_' . $trip->id . '.' . $file->extension());

            //Save the file path in the model
            $trip->upload_name = $upload_name;

            //Save the model to the DB
            $trip->save();
        }

        //Sending mails
        $message = '';
        $recipient = 'admin@example.com';
        $viewTemplate = 'emails.new_trip_admin';

        // Create an instance of the SimpleMail class
        $email = new SimpleMail($message, $recipient, $viewTemplate);

        // Send the email
        Mail::to($recipient)->send($email);

        //Redirecting to the homepage
        return redirect()->route('components.homepage');

    }

    /**
     * Get the attachment from a business trip.
     */
    public function getAttachment(BusinessTrip $trip) {
        // Check if the trip has an attachment
        if (!$trip->upload_name) {
            abort(404, 'File not found'); // Or other error handling
        }

        // Build the file path
        $filePath = storage_path('app/' . $trip->upload_name);

        // Check if the file exists
        if (!file_exists($filePath)) {
            abort(404, 'File not found'); // Or other error handling
        }

        // Download the file
        return response()->download($filePath, $trip->upload_name);
    }

    /**
     * Returning the view with the trip editing form
     * @throws \Exception
     */
    public function edit(BusinessTrip $trip) {
        $startDate = new DateTime($trip->datetime_start);
        $endDate = new DateTime($trip->datetime_end);
        $interval = new DateInterval('P1D');
        $dateRange = new DatePeriod($startDate, $interval, $endDate);

        $days = iterator_count($dateRange);

        // Get the collection of contributions associated with the trip
        $tripContributions = $trip->contributions()->pluck('contributions.id');

        return view('business-trips.edit',  [
            'trip' => $trip,
            'days' => $days,
            'tripContributions' => $tripContributions,
        ]);

    }

    /**
     * Validation as in create(), should check if user is admin/not
     * Redirecting to the trip editing form
     * Sending mail with mail component to admin
     */
    public function update(Request $request, BusinessTrip $trip) {
        //Validate data
        $validatedData = $request->validate([
            //Add more fields and validation rules
            'place' => 'required|varchar|max:200',
            'datetime_start' => 'required|datetime',
        ]);

        //Update the trip based on the validated data
        $trip->update($validatedData);

        //Sending mails
        $message = '';
        $recipient = 'admin@example.com';
        $viewTemplate = 'emails.new_trip_admin';

        // Create an instance of the SimpleMail class
        $email = new SimpleMail($message, $recipient, $viewTemplate);

        // Send the email
        Mail::to($recipient)->send($email);

        return redirect()->route('business-trips.edit', $trip);

    }

    /**
     * Next 3 functions could be combined to changeState(Request $request, BusinessTrip $b)
     * Check for admin needed?
     *
     * Updating state of the trip to cancelled
     * Adding cancellation reason
     * @throws ValidationException
     */
    public function cancel(BusinessTrip $trip) {
        // Check if the trip is in a valid state for cancellation
        if (!in_array($trip->state, [TripState::NEW, TripState::CONFIRMED])) {
            throw ValidationException::withMessages(['state' => 'Invalid state for cancellation.']);
        }
        //Cancel the trip and add cancellation reason
        $trip->update(['state' => TripState::CANCELLATION_REQUEST, 'cancellation_reason' => request('cancellation_reason')]);

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
     * @throws ValidationException
     */
    public function confirm(BusinessTrip $trip) {
        // Check if the trip is in a valid state for confirmation
        if ($trip->state !== TripState::NEW) {
            throw ValidationException::withMessages(['state' => 'Invalid state for confirmation.']);
        }
        //Confirm the trip
        $trip->update(['state' => TripState::CONFIRMED]);

        return redirect()->route('business-trips.show', $trip);

    }

    /**
     * Same as update, updating state to closed
     * @throws ValidationException
     */
    public function close(BusinessTrip $trip) {
        // Check if the trip is in a valid state for closing
        if ($trip->state !== TripState::CONFIRMED) {
            throw ValidationException::withMessages(['state' => 'Invalid state for closing.']);
        }

        //Close the trip
        $trip->update(['state' => TripState::CLOSED]);

        return redirect()->route('business-trips.show', $trip);

    }
}
