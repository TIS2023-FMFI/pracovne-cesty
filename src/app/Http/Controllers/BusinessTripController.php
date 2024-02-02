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
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use App\Enums\TripState;
use App\Enums\TripType;
use App\Enums\UserType;
use App\Models\User;

class BusinessTripController extends Controller
{

    /**
     * Returning view with details from all trips
     */
    public function index() {
        // Check if the user is logged in
        if (Auth::check()) {
            // Check if the user is an admin
            if (Auth::user()->hasRole('admin')) {
                // Retrieve all trips and users for admin
                $trips = BusinessTrip::paginate(15);
                $users = User::all();
            } else {
                // Retrieve only user's trips for regular users
                $trips = Auth::user()->businessTrips()->paginate(15);
                // No need for a list of users
                $users = null;
            }

            // Return the dashboard view with trips and users
            return view('dashboard', [
                'trips' => $trips,
                'users' => $users,
            ]);
        } else {
            // User is not logged in, redirect to login page
            return redirect()->route('login');
        }
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
        //Validate data
        $validatedData = $request->validate([
            'user_id' => 'required|integer|min:0',
            'country_id' => 'required|exists:countries,id',
            'transport_id' => 'required|exists:transports,id',
            'place' => 'required|string|max:200',
            'datetime_start' => 'required|date',
            'datetime_end' => 'required|date|after:datetime_start',
            'trip_purpose_id' => 'required|integer|min:0',
            'purpose_details' => 'nullable|string|max:50',
            'iban' => 'required|string|max:34',
            'meals_reimbursement' => 'nullable|boolean',
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
        $endDate->modify('+1 day'); // Include the end day
        $interval = new DateInterval('P1D');
        $dateRange = new DatePeriod($startDate, $interval, $endDate);

        $days = iterator_count($dateRange);

        return view('business-trips.edit',  [
            'trip' => $trip,
            'days' => $days,
        ]);

    }

    /**
     * Redirecting to the trip editing form
     * Sending mail with mail component to admin
     * @throws ValidationException
     * @throws Exception
     */
    public function update(Request $request, BusinessTrip $trip) {
        // Validation rules for expense-related fields
        $expenses = ['travelling' => 'Cestovné', 'accommodation' => 'Ubytovanie', 'allowance' => 'Záloha za cestu', 'advance' => 'Vložné', 'other' => 'Iné'];
        $expenseRules = [];

        foreach ($expenses as $expenseName => $label) {
            $eurKey = $expenseName . '_expense_eur';
            $foreignKey = $expenseName . '_expense_foreign';
            $reimburseKey = $expenseName . '_reimburse';

            // Rules for each expense-related field
            $expenseRules[$eurKey] = ['nullable', 'numeric'];
            $expenseRules[$foreignKey] = ['nullable', 'numeric'];
            $expenseRules[$reimburseKey] = ['nullable', 'boolean'];
        }

        //Validate data
        $validatedData = $request->validate([
            'user_id' => 'required|integer|min:0',
            'country_id' => 'required|exists:countries,id',
            'transport_id' => 'required|exists:transports,id',
            'place' => 'required|string|max:200',
            'event_url' => 'nullable|string|max:200',
            'upload_name' => 'nullable|string|max:200',
            'sofia_id' => 'nullable|string|max:40',
            'state' => 'required|integer',
            'datetime_start' => 'required|date',
            'datetime_end' => 'required|date|after:datetime_start',
            'place_start' => 'nullable|string|max:200',
            'place_end' => 'nullable|string|max:200',
            'datetime_border_crossing_start' => 'nullable|date',
            'datetime_border_crossing_end' => 'nullable|date',
            'trip_purpose_id' => 'required|integer|min:0', //possibly |exists:trip_purposes,id
            'purpose_details' => 'nullable|string|max:50',
            'iban' => 'required|string|max:34',
            'conference_fee_id' => 'nullable|integer|min:0',
            'reimbursement_id' => 'nullable|integer|min:0',
            'spp_symbol_id' => 'nullable|exists:spp_symbols,id',
            'accommodation_expense_id' => 'nullable|integer|min:0',
            'travelling_expense_id' => 'nullable|integer|min:0',
            'other_expense_id' => 'nullable|integer|min:0',
            'advance_expense_id' => 'nullable|integer|min:0',
            'meals_reimbursement' => 'boolean',
            'expense_estimation' => $expenseRules,
            'cancellation_reason' => 'nullable|string|max:1000',
            'note' => 'nullable|string|max:5000',
            'conclusion' => 'nullable|string|max:5000',
        ]);

        // Calculate the number of days based on start and end datetimes
        $startDate = new DateTime($validatedData['datetime_start']);
        $endDate = new DateTime($validatedData['datetime_end']);
        $endDate->modify('+1 day'); // Include the end day
        $interval = new DateInterval('P1D'); // 1 day interval
        $dateRange = new DatePeriod($startDate, $interval, $endDate);
        $days = iterator_count($dateRange);

        // Set the value for 'not_reimbursed_meals'
        $notReimbursedMeals = '';
        $checkboxNames = ['b', 'l', 'd']; // Checkbox names prefix
        foreach ($checkboxNames as $prefix) {
            for ($i = 0; $i < $days; $i++) {
                $checkboxName = $prefix . $i;
                if (!$request->has($checkboxName)) {
                    $notReimbursedMeals .= '0'; // Checkbox not present, mark as not reimbursed
                } else {
                    $notReimbursedMeals .= '1'; // Checkbox present, mark as reimbursed
                }
            }
        }
        $validatedData['not_reimbursed_meals'] = $notReimbursedMeals;

        // Check if the authenticated user is an admin
        $isAdmin = Auth::check() && Auth::user()->hasRole('admin');

        // Check if the trip is in a valid state for updating by non-admin user
        if (!$isAdmin && !in_array($trip->state, [TripState::NEW, TripState::CONFIRMED])) {
            throw ValidationException::withMessages(['state' => 'Invalid state for updating.']);
        }

        // Update the trip with the calculated 'not_reimbursed_meals' value
        $trip->update($validatedData);

        // Change the state to 'UPDATED' if not admin
        if (!$isAdmin) {
            $trip->update(['state' => TripState::UPDATED]);
        }

        // Check if the trip is in a valid state for updating by non-admin user
        if (!$isAdmin && !in_array($trip->state, [TripState::NEW, TripState::CONFIRMED])) {
            throw ValidationException::withMessages(['state' => 'Invalid state for updating.']);
        }

        // Update the trip with the calculated 'not_reimbursed_meals' value
        $trip->update($validatedData);

        // Change the state to 'UPDATED' if not admin
        if (!$isAdmin) {
            $trip->update(['state' => TripState::UPDATED]);
        }

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
     * Updating state of the trip to cancelled
     * Adding cancellation reason
     * @throws ValidationException
     */
    public function cancel(Request $request, BusinessTrip $trip) {
        // Check if the trip is in a valid state for cancellation
        if (!in_array($trip->state, [TripState::NEW, TripState::CONFIRMED, TripState::CANCELLATION_REQUEST])) {
            throw ValidationException::withMessages(['state' => 'Invalid state for cancellation.']);
        }
        // Cancel the trip and add cancellation reason if provided
        $data = ['state' => TripState::CANCELLED];
        if ($request->has('cancellation_reason')) {
            $data['cancellation_reason'] = $request->input('cancellation_reason');
        }
        $trip->update($data);

        //Send cancellation email to user

        // Retrieve user's email associated with the trip
        $recipient = $trip->user->email;

        $message = '';
        $viewTemplate = 'emails.cancellation_user';

        // Create an instance of the SimpleMail class
        $email = new SimpleMail($message, $recipient, $viewTemplate);

        // Send the email
        Mail::to($recipient)->send($email);

        return redirect()->route('business-trips.show', $trip);

    }

    /**
     * Updating state of the trip to confirmed
     * @throws ValidationException
     */
    public function confirm(Request $request, BusinessTrip $trip) {
        // Check if the trip is in a valid state for confirmation
        if ($trip->state !== TripState::NEW) {
            throw ValidationException::withMessages(['state' => 'Invalid state for confirmation.']);
        }

        // Validate the sofia_id
        $validatedData = $request->validate([
            'sofia_id' => 'required|string|max:40',
        ]);

        // Confirm the trip and record sofia_id
        $trip->update(['state' => TripState::CONFIRMED, 'sofia_id' => $validatedData['sofia_id']]);

        return redirect()->route('business-trips.show', $trip);

    }

    /**
     * Updating state to closed
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

    /**
     * Updating state of the trip to cancellation request
     * @throws ValidationException
     */
    public function requestCancellation(BusinessTrip $trip): \Illuminate\Http\RedirectResponse
    {
        // Check if the current state of the trip allows cancellation request
        if ($trip->state === TripState::NEW
            || $trip->state === TripState::CONFIRMED
            || $trip->state === TripState::UPDATED) {
            // Change the state to CANCELLATION_REQUEST
            $trip->update(['state' => TripState::CANCELLATION_REQUEST]);

            // Send email notification to the admin
            $message = '';
            $recipient = 'admin@example.com';
            $viewTemplate = 'emails.cancellation_request_admin';

            // Create an instance of the SimpleMail class
            $email = new SimpleMail($message, $recipient, $viewTemplate);

            // Send the email
            Mail::to($recipient)->send($email);
        } else {
            throw ValidationException::withMessages(['state' => 'Invalid state for cancellation request.']);
        }

        return redirect()->route('request-cancel', $trip->id)->with('success', 'Cancellation request submitted successfully.');
    }

    /**
     * Adding comment to trip
     */
    public function addComment(Request $request, BusinessTrip $trip): \Illuminate\Http\RedirectResponse
    {
        // Validate the incoming request
        $request->validate([
            'comment' => 'required|string|max:5000',
        ]);

        // Update the trip's note with the new comment
        $trip->update(['note' => $request->input('comment')]);

        // Send email notification to the admin
        $message = '';
        $recipient = 'admin@example.com';
        $viewTemplate = 'emails.new_note_admin';

        // Create an instance of the SimpleMail class
        $email = new SimpleMail($message, $recipient, $viewTemplate);

        // Send the email
        Mail::to($recipient)->send($email);

        // Redirect or respond with a success message
        return redirect()->route('add-report', $trip->id)->with('success', 'Comment added successfully.');
    }
}
