<?php

namespace App\Http\Controllers;

use App\Enums\DocumentType;
use App\Enums\PositionTitle;
use App\Enums\TripState;
use App\Enums\TripType;
use App\Mail\SimpleMail;
use App\Models\BusinessTrip;
use App\Models\Staff;
use App\Models\User;
use DateInterval;
use DatePeriod;
use DateTime;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use mikehaertl\pdftk\Pdf;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class BusinessTripController extends Controller
{

    /**
     * Returning view with details from all trips
     */
    public function index() {
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
            $upload_name = uniqid('', true) . '.' . $file->extension();
            Storage::disk('uploads')->put($upload_name, $file);

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
        $filePath = Storage::disk('uploads')->path($trip->upload_name);

        // Check if the file exists
        if (Storage::disk('uploads')->missing($trip->upload_name)) {
            abort(404, 'File not found'); // Or other error handling
        }

        // Download the file
        return Storage::disk('uploads')->download($trip->upload_name);
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
        $expenses = ['travelling', 'accommodation', 'allowance', 'advance', 'other'];
        $expenseRules = [];

        foreach ($expenses as $expenseName) {
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
        $isAdmin = Auth::user()->hasRole('admin');

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

        return redirect()->route('business-trips.edit', $trip);

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

        return redirect()->route('business-trips.edit', $trip);

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

        return redirect()->route('business-trips.edit', $trip);

    }

    /**
     * Updating state of the trip to cancellation request
     * @throws ValidationException
     */
    public function requestCancellation(BusinessTrip $trip): \Illuminate\Http\RedirectResponse
    {
        // Check if the current state of the trip allows cancellation request
        if (in_array($trip->state,
            [TripState::NEW, TripState::CONFIRMED, TripState::UPDATED])) {
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

    /**
     * Exports a PDF document based on a specific business trip and document type.
     *
     * Generates various types of PDF documents for business trips. The method
     * verifies the trip's existence and the PDF template, prepares data based on
     * the document type, and creates the PDF, which is returned as a binary file.
     *
     * @param int $tripId The ID of the business trip.
     * @param int $documentType ID of the document type to be exported.
     * @return JsonResponse|BinaryFileResponse Returns a JsonResponse in case of
     *         errors or a BinaryFileResponse with the generated PDF.
     * @throws Exception If there is an error during PDF creation or manipulation.
     * @example
     * $response = $object->exportPdf(123, 0);
     */
    public function exportPdf(int $tripId, int $documentType): JsonResponse | BinaryFileResponse
    {
        $trip = BusinessTrip::find($tripId);
        if (!$trip) {
            return response()->json(['error' => 'Business trip not found'], 404);
        }

        $docType = DocumentType::from($documentType);

        $templateName = $docType->fileName();
        $templatePath = Storage::disk('pdf-templates')
            ->path($templateName);

        if (Storage::disk('pdf-templates')->missing($templateName)) {
            Log::error("PDF template file does not exist at path: " . $templatePath);
            return response()->json(['error' => 'PDF template file not found'], 404);
        }

        $data = [];
        switch ($docType) {
            case DocumentType::FOREIGN_TRIP_AFFIDAVIT:
                $tripDuration = $trip->datetime_start->diff($trip->datetime_end);
                $tripDurationFormatted = $tripDuration->format('%d dni %h hodin %i minut');
                $name = $trip->user->academic_degrees
                    ? $trip->user->academic_degrees . ' ' . $trip->user->first_name . ' ' . $trip->user->last_name
                    : $trip->user->first_name . ' ' . $trip->user->last_name;
                $data = [
                    'order_number' => $trip->sofia_id,
                    'trip_duration' => $tripDurationFormatted,
                    'adress' => $trip->place,
                    'name' => $name,
                ];
                break;

            case DocumentType::COMPENSATION_AGREEMENT:
                $contributions = $trip->contributions;
                $dean = Staff::where('position', PositionTitle::DEAN)->first();
                $secretary = Staff::where('position', PositionTitle::SECRETARY)->first();
                $data = [
                    'first_name' => $trip->user->first_name,
                    'last_name' => $trip->user->last_name,
                    'academic_degree' => $trip->user->academic_degrees,
                    'address' => $trip->user->address,
                    'contribution1' => $contributions->get(0) ? 'true' : null,
                    'contribution2' => $contributions->get(1) ? 'true' : null,
                    'contribution3' => $contributions->get(2) ? 'true' : null,
                    'department' => $trip->user->department,
                    'place' => $trip->country->name . ', ' . $trip->place,
                    'datetime_start' => $trip->datetime_start->format('d-m-Y'),
                    'datetime_end' => $trip->datetime_end->format('d-m-Y'),
                    'transport' => $trip->transport->name,
                    'trip_purpose' => $trip->tripPurpose->name . (isset($trip->purpose_details) ? ' - ' . $trip->purpose_details : ''),
                    'fund' => $trip->sppSymbol->fund,
                    'functional_region' => $trip->sppSymbol->functional_region,
                    'financial_centre' => $trip->sppSymbol->financial_centre,
                    'spp_symbol' => $trip->sppSymbol->spp_symbol,
                    'account' => $trip->sppSymbol->account,
                    'grantee' => $trip->sppSymbol->grantee,
                    'iban' => $trip->iban,
                    'incumbent_name1' => $dean ? $dean->incumbent_name : 'N/A',
                    'incumbent_name2' => $secretary ? $secretary->incumbent_name : 'N/A',
                    'contribution1_text' => $contributions->get(0) ? $contributions->get(0)->pivot->detail : null,
                    'contribution2_text' => $contributions->get(1) ? $contributions->get(1)->pivot->detail : null,
                    'contribution3_text' => $contributions->get(2) ? $contributions->get(2)->pivot->detail : null,
                ];
                break;

            case DocumentType::CONTROL_SHEET:
                $data = [
                    'spp_symbol' => $trip->sppSymbol->spp_symbol,
                    'expense_estimation' => $trip->conference_fee->amount,
                    'source1' => $trip->sppSymbol->fund,
                    'functional_region1' => $trip->sppSymbol->functional_region,
                    'spp_symbol1' => $trip->sppSymbol->spp_symbol,
                    'financial_centre1' => $trip->sppSymbol->financial_centre,
                    'purpose_details' => 'Úhrada vložného',
                ];
                break;

            case DocumentType::PAYMENT_ORDER:
                $data = [
                    'advance_amount' => $trip->conference_fee->amount,
                    'grantee' => $trip->sppSymbol->grantee,
                    'address' => $trip->conference_fee->organiser_address,
                    'source' => $trip->sppSymbol->fund,
                    'functional_region' => $trip->sppSymbol->functional_region,
                    'spp_symbol' => $trip->sppSymbol->spp_symbol,
                    'financial_centre' => $trip->sppSymbol->functional_region,
                    'iban' => $trip->iban,
                ];
                break;

            case DocumentType::DOMESTIC_REPORT:
                $name = $trip->user->academic_degrees
                    ? $trip->user->academic_degrees . ' ' . $trip->user->first_name . ' ' . $trip->user->last_name
                    : $trip->user->first_name . ' ' . $trip->user->last_name;
                $mealsReimbursementText = $trip->meals_reimbursement == 1
                    ? 'mám záujem o preplatenie'
                    : 'nemám záujem o preplatenie';
                $data = [
                    'name' => $name,
                    'department' => $trip->user->department,
                    'date_start' => $trip->datetime_start->format('d-m-Y'),
                    'date_end' => $trip->datetime_end->format('d-m-Y'),
                    'spp_symbol' => $trip->spp_symbols->spp_symbol,
                    'time_start' => $trip->datetime_start->format('H:i'),
                    'time_end' => $trip->datetime_end->format('H:i'),
                    'transport' => $trip->transport->name,
                    'travelling_expense' => $trip->travellingExpense ? $trip->travellingExpense->amount_eur : null,
                    'accommodation_expense' => $trip->accommodationExpense ? $trip->accommodationExpense->amount_eur : null,
                    'other_expenses' => $trip->otherExpense ? $trip->otherExpense->amount_eur : null,
                    'allowance' => $trip->allowanceExpense ? $trip->allowanceExpense->amount_eur : null,
                    'conclusion' => $trip->conclusion,
                    'iban' => $trip->iban,
                    'address' => $trip->user->address,
                    'meals_reimbursement_DG42' => $mealsReimbursementText,
                ];
                break;

            case DocumentType::FOREIGN_REPORT:
                $mealsReimbursementText = $trip->meals_reimbursement == 1
                    ? 'mám záujem o preplatenie'
                    : 'nemám záujem o preplatenie';
                $data = [
                    'name' => $trip->user->first_name . ' ' . $trip->user->last_name,
                    'department' => $trip->user->department,
                    'country' => $trip->country->name,
                    'datetime_end' => $trip->datetime_end->format('d-m-Y H:i'),
                    'datetime_start' => $trip->datetime_start->format('d-m-Y H:i'),
                    'datetime_border_crossing_start' => $trip->datetime_border_crossing_start->format('d-m-Y H:i'),
                    'datetime_border_crossing_end' => $trip->datetime_border_crossing_end->format('d-m-Y H:i'),
                    'place' => $trip->place,
                    'spp_symbol' => $trip->sppSymbol->spp_symbol,
                    'transport' => $trip->transport->name,
                    'travelling_expense_foreign' => $trip->travellingExpense && isset($trip->travellingExpense->amount_foreign) ? $trip->travellingExpense->amount_foreign : null,
                    'travelling_expense' => $trip->travellingExpense ? $trip->travellingExpense->amount_eur : null,
                    'accommodation_expense_foreign' => $trip->accommodationExpense ? $trip->accommodationExpense->amount_foreign : null,
                    'accommodation_expense' => $trip->accommodationExpense ? $trip->accommodationExpense->amount_eur : null,
                    'allowance_foreign' => $trip->allowanceExpense ? $trip->allowanceExpense->amount_foreign : null,
                    'allowance' => $trip->allowanceExpense ? $trip->allowanceExpense->amount_eur : null,
                    'meals_reimbursement' => $mealsReimbursementText,
                    'other_expenses_foreign' => $trip->otherExpense ? $trip->otherExpense->amount_foreign : null,
                    'other_expenses' => $trip->otherExpense ? $trip->otherExpense->amount_eur : null,
                    'conclusion' => $trip->conclusion,
                    'iban' => $trip->iban,
                    'advance_expense_foreign' => $trip->advanceExpense ? $trip->advanceExpense->amount_foreign : null,
                    'invitation_case_charges' => $trip->expense_estimation,
                ];
                break;

            default:
                return response()->json(['error' => 'Unknown document type'], 400);
        }

        try {
            Log::info("Creating PDF object with template path: " . $templatePath);
            $pdf = new Pdf($templatePath);
        } catch (Exception $e) {
            Log::error("Error creating PDF object: " . $e->getMessage());
            return response()->json(['error' => 'Failed to create PDF object: ' . $e->getMessage()], 500);
        }

        $outputName = uniqid('', true) . '.pdf';
        $outputPath = Storage::disk('pdf-exports')
            ->path($outputName);

        try {
            $pdf->fillForm($data);
            $pdf->flatten();
            $pdf->saveAs($outputPath);
        } catch (Exception $e) {
            Log::error("Error during PDF manipulation: " . $e->getMessage());
            return response()->json(['error' => 'Failed during PDF manipulation: ' . $e->getMessage()], 500);
        }

        if (Storage::disk('pdf-exports')->exists($outputName)) {
            return response()->download($outputPath)->deleteFileAfterSend(true);
        }

        Log::error("PDF file does not exist after generation: " . $outputPath);
        return response()->json(['error' => 'Failed to generate PDF, file not found'], 500);
    }
}
