<?php

namespace App\Http\Controllers;

use App\Enums\DocumentType;
use App\Enums\PositionTitle;
use App\Enums\TripState;
use App\Enums\TripType;
use App\Mail\SimpleMail;
use App\Models\BusinessTrip;
use App\Models\ConferenceFee;
use App\Models\Contribution;
use App\Models\Expense;
use App\Models\Reimbursement;
use App\Models\Staff;
use App\Models\TripContribution;
use App\Models\User;
use DateInterval;
use DatePeriod;
use DateTime;
use Exception;
use Illuminate\Contracts\Auth\Authenticatable;
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
    public static function index() {
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
    public static function create() {
        return view('business-trips.create');
    }

    /**
     * Parsing data from the $request in form
     * Also managing uploaded files from the form
     * Redirecting to the homepage
     * Sending mail with mail component to admin
     */
    public static function store(Request $request) {
        // Get the authenticated user's ID
        $user = Auth::user();

        // Validate all necessary data
        $validatedUserData = self::validateUserData($request);
        $validatedTripData = self::validateUpdatableTripData($request) + self::validateFixedTripData($request);

        if ($user->user_type->isExternal()) {
            $validatedTripContributionsData = self::validateTripContributionsData($request);
        }

        list($isReimbursement, $validatedReimbursementData) = self::validateReimbursementData($request);
        list($isConferenceFee, $validatedConferenceFeeData) = self::validateConferenceFeeData($request);

        // Add the authenticated user's ID to the validated data
        $validatedTripData['user_id'] = $user->id;

        // Set the type of trip based on the selected country
        $selectedCountry = $request->input('country');
        $validatedTripData['type'] = $selectedCountry === 152 ? TripType::DOMESTIC : TripType::FOREIGN;

        //Logic to store the trip based on the validated data
        $trip = BusinessTrip::create($validatedTripData);
        if ($isReimbursement){
            self::createOrUpdateReimbursement($validatedReimbursementData, $trip);
        }

        if ($isConferenceFee) {
            self::createOrUpdateConferenceFee($validatedConferenceFeeData, $trip);
        }

        if ($user->user_type->isExternal()) {
            self::createOrUpdateTripContributions($validatedTripContributionsData, $trip);
        }

        //Handle file uploads
        if ($request->hasFile('upload_name')) {
            $file = $request->file('upload_name');

            //Store the file in the storage/app/trips directory
            $upload_name = Storage::disk('uploads')->putFile('', $file);

            //Save the file path in the model
            $trip->update(['upload_name' => $upload_name]);
        }

        // Update user details
        $user->update($validatedUserData);

        //Sending mails
        $message = '';
        $recipient = 'admin@example.com';
        $viewTemplate = 'emails.new_trip_admin';

        // Create an instance of the SimpleMail class
        $email = new SimpleMail($message, $recipient, $viewTemplate);

        // Send the email
        Mail::to($recipient)->send($email);

        // Check if it was too late to add trip and inform user
        $warningMessage = null;
        $currentDate = new DateTime();
        $startDate = new DateTime($trip->datetime_start);
        $days = $trip->type == TripType::DOMESTIC ? '4' : '11';
        $newDate = $currentDate->modify("+ ".$days." weekday");
        if ($startDate < $newDate) {
            $warningMessage = 'Vaša pracovná cesta bude pridaná, ale nie je to v súlade s pravidlami. Cestu vždy pridávajte minimálne 4 pracovné dni pred jej začiatkom v prípade, že ide o tuzemskú cestu, a 11 pracovných dní pred začiatkom v prípade zahraničnej cesty.';
        }

        //Redirecting to the homepage
        return redirect()->route('homepage')->with('warning', $warningMessage);
    }

    /**
     * Get the attachment from a business trip.
     */
    public static function getAttachment(BusinessTrip $trip) {
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
     * @throws Exception
     */
    public static function edit(BusinessTrip $trip) {
        $days = self::getTripDurationInDays($trip);

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
    public static function update(Request $request, BusinessTrip $trip) {
        if ($trip->state->isFinal()) {
            throw ValidationException::withMessages(['state' => 'Invalid state for updating.']);
        }
        // Check if the authenticated user is an admin
        $user = Auth::user();
        $isAdmin = $user->hasRole('admin');

        $tripState = $trip->state;

        // Admin updating the trip
        if ($isAdmin) {
            $validatedUserData = self::validateUserData($request);

            $validatedTripData = self::validateUpdatableTripData($request) + self::validateFixedTripData($request);
            $validatedTripContributionsData = self::validateTripContributionsData($request);
            list($isReimbursement, $validatedReimbursementData) = self::validateReimbursementData($request);
            list($isConferenceFee, $validatedConferenceFeeData) = self::validateConferenceFeeData($request);

            if (in_array($tripState, [TripState::UPDATED, TripState::COMPLETED])) {
                $validatedExpensesData = self::validateExpensesData($trip, $request);
                array_merge($validatedTripData, $request->validate(['conclusion' => 'required|max:5000']));
                // all validations complete
                self::createOrUpdateExpenses($validatedExpensesData, $trip);
            }

            if ($isReimbursement) {
                self::createOrUpdateReimbursement($validatedReimbursementData, $trip);
            }

            if ($isConferenceFee) {
                self::createOrUpdateConferenceFee($validatedConferenceFeeData, $trip);
            }

            self::createOrUpdateTripContributions($validatedTripContributionsData, $trip);

            $trip->user->update($validatedUserData);

            // Update the trip with the provided data
            $trip->update($validatedTripData);

        } else { // Non-admin user updating the trip

            // Validate and update based on trip state
            switch ($trip->state) {
                case TripState::CONFIRMED:
                    $validatedTripData = self::validateUpdatableTripData($request);

                    // Change the state to UPDATED
                    $trip->update(['state' => TripState::UPDATED]);
                    break;

                // Adding report to an UPDATED state trip
                case TripState::UPDATED:
                    // Validation rules for expense-related fields
                    $validatedExpensesData = self::validateExpensesData($trip, $request);
                    //meals table
                    $validatedTripData = $request->validate(['conclusion' => 'required|max:5000']);

                    self::createOrUpdateExpenses($validatedExpensesData, $trip);

                    // Change the state to COMPLETED
                    $trip->update(['state' => TripState::COMPLETED]);
                    break;

                // Updating a wrong state trip
                default:
                    throw ValidationException::withMessages(['state' => 'Invalid state for updating.']);
            }

            // Update the trip with the provided data
            $trip->update($validatedTripData);

            //Sending mails
            $message = '';
            $recipient = 'admin@example.com';
            $viewTemplate = 'emails.new_trip_admin';

            // Create an instance of the SimpleMail class
            $email = new SimpleMail($message, $recipient, $viewTemplate);

            // Send the email
            Mail::to($recipient)->send($email);
        }

        return redirect()->route('trip.edit', ['trip' => $trip]);
    }

    /**
     * Updating state of the trip to cancelled
     * Adding cancellation reason
     * @throws ValidationException
     */
    public static function cancel(Request $request, BusinessTrip $trip) {
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
    public static function confirm(Request $request, BusinessTrip $trip) {
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

        return redirect()->route('trip.edit', $trip);

    }

    /**
     * Updating state to closed
     * @throws ValidationException
     */
    public static function close(BusinessTrip $trip) {
        // Check if the trip is in a valid state for closing
        if ($trip->state !== TripState::COMPLETED) {
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
    public static function requestCancellation(Request $request, BusinessTrip $trip): \Illuminate\Http\RedirectResponse
    {
        // Validate the cancellation reason
        $validator = Validator::make($request->all(), [
            'cancellation_reason' => 'required|string|max:1000',
        ]);

        // Check if the validation fails
        if ($validator->fails()) {
            throw ValidationException::withMessages(['cancellation_reason' => 'Cancellation reason is required.']);
        }

        // Check if the current state of the trip allows cancellation request
        if (in_array($trip->state,
            [TripState::NEW, TripState::CONFIRMED])) {
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
    public static function addComment(Request $request, BusinessTrip $trip): \Illuminate\Http\RedirectResponse
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
        return redirect()->route('business-trips.edit', $trip->id)->with('message', 'Comment added successfully.');
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
    public static function exportPdf(int $tripId, int $documentType): JsonResponse | BinaryFileResponse
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
                $tripDurationFormatted = $trip->datetime_start->format('d.m.Y')
                    . ' - '
                    . $trip->datetime_end->format('d.m.Y');

                $name = ($trip->user->academic_degrees ?? '')
                    . ' ' . $trip->user->first_name
                    . ' ' . $trip->user->last_name;

                $data = [
                    'order_number' => $trip->sofia_id,
                    'trip_duration' => $tripDurationFormatted,
                    'address' => $trip->place . ', ' . $trip->country->name,
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
                    'contribution1' => $contributions->get(0) ? 'yes1' : null,
                    'contribution2' => $contributions->get(1) ? 'yes2' : null,
                    'contribution3' => $contributions->get(2) ? 'yes3' : null,
                    'department' => $trip->user->department,
                    'place' => $trip->country->name . ', ' . $trip->place,
                    'datetime_start' => $trip->datetime_start->format('d-m-Y'),
                    'datetime_end' => $trip->datetime_end->format('d-m-Y'),
                    'transport' => $trip->transport->name,
                    'trip_purpose' => $trip
                            ->tripPurpose
                            ->name . (isset($trip->purpose_details) ? ' - ' . $trip->purpose_details : ''),
                    'fund' => $trip->sppSymbol->fund,
                    'functional_region' => $trip->sppSymbol->functional_region,
                    'financial_centre' => $trip->sppSymbol->financial_centre,
                    'spp_symbol' => $trip->sppSymbol->spp_symbol,
                    'account' => $trip->sppSymbol->account,
                    'grantee' => $trip->sppSymbol->grantee,
                    'iban' => $trip->iban,
                    'incumbent_name1' => $dean->incumbent_name ?? null,
                    'incumbent_name2' => $secretary->incumbent_name ?? null,
                    'position_name1' => $dean->position_name ?? null,
                    'position_name2' => $secretary->position_name ?? null,
                    'contribution1_text' => $contributions->get(0)->pivot->detail ?? null,
                    'contribution2_text' => $contributions->get(1)->pivot->detail ?? null,
                    'contribution3_text' => $contributions->get(2)->pivot->detail ?? null,
                ];
                break;

            case DocumentType::CONTROL_SHEET:
                $data = [
                    'spp_symbol' => $trip->sppSymbol->spp_symbol,
                    'expense_estimation' => $trip->expense_estimation,
                    'source1' => $trip->sppSymbol->fund,
                    'functional_region1' => $trip->sppSymbol->functional_region,
                    'spp_symbol1' => $trip->sppSymbol->spp_symbol,
                    'financial_centre1' => $trip->sppSymbol->financial_centre,
                    'purpose_details' => 'Úhrada vložného',
                ];
                break;

            case DocumentType::PAYMENT_ORDER:
                $data = [
                    'advance_amount' => $trip->advance_amount,
                    'grantee' => $trip->sppSymbol->grantee,
                    'address' => $trip->conference_fee->organiser_address,
                    'source' => $trip->sppSymbol->fund,
                    'functional_region' => $trip->sppSymbol->functional_region,
                    'spp_symbol' => $trip->sppSymbol->spp_symbol,
                    'financial_centre' => $trip->sppSymbol->financial_centre,
                    'iban' => $trip->iban,
                ];
                break;

            case DocumentType::DOMESTIC_REPORT:
                $name = ($trip->user->academic_degrees ?? '')
                    . ' ' . $trip->user->first_name
                    . ' ' . $trip->user->last_name;

                $mealsReimbursementText = $trip->meals_reimbursement
                    ? 'mám záujem o preplatenie'
                    : 'nemám záujem o preplatenie';

                $data = [
                    'name' => $name,
                    'department' => $trip->user->department,
                    'date_start' => $trip->datetime_start->format('d.m.Y'),
                    'date_end' => $trip->datetime_end->format('d.m.Y'),
                    'spp_symbol' => $trip->sppSymbol->spp_symbol,
                    'time_start' => $trip->datetime_start->format('H:i'),
                    'time_end' => $trip->datetime_end->format('H:i'),
                    'transport' => $trip->transport->name,
                    'travelling_expense' => $trip->travellingExpense->amount_eur ?? null,
                    'accommodation_expense' => $trip->accommodationExpense->amount_eur ?? null,
                    'other_expenses' => $trip->otherExpense->amount_eur ?? null,
                    'allowance' => $trip->allowanceExpense->amount_eur ?? null,
                    'conclusion' => $trip->conclusion,
                    'iban' => $trip->iban,
                    'address' => $trip->user->address,
                    'meals_reimbursement_DG42' => $mealsReimbursementText,
                ];
                break;

            case DocumentType::FOREIGN_REPORT:
                $mealsReimbursementText = $trip->meals_reimbursement
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
                    'travelling_expense_foreign' => $trip->travellingExpense->amount_foreign ?? null,
                    'travelling_expense' => $trip->travellingExpense->amount_eur ?? null,
                    'accommodation_expense_foreign' => $trip->accommodationExpense->amount_foreign ?? null,
                    'accommodation_expense' => $trip->accommodationExpense->amount_eur ?? null,
                    'allowance_foreign' => $trip->allowanceExpense->amount_foreign ?? null,
                    'allowance' => $trip->allowanceExpense->amount_eur ?? null,
                    'meals_reimbursement' => $mealsReimbursementText,
                    'other_expenses_foreign' => $trip->otherExpense->amount_foreign ?? null,
                    'other_expenses' => $trip->otherExpense->amount_eur ?? null,
                    'conclusion' => $trip->conclusion,
                    'iban' => $trip->iban,
                    'advance_expense_foreign' => $trip->advanceExpense->amount_foreign ?? null,
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
            $pdf->replacementFont(public_path('DejaVuSans.ttf'));
            $pdf->needAppearances();
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

    /**
     * @param BusinessTrip $trip
     * @return int
     * @throws Exception
     */
    private static function getTripDurationInDays(BusinessTrip $trip): int
    {
        $startDate = new DateTime($trip->datetime_start);
        $endDate = new DateTime($trip->datetime_end);
        $interval = new DateInterval('P1D');
        $dateRange = new DatePeriod($startDate, $interval, $endDate);

        return iterator_count($dateRange) + 1;
    }

    /**
     * @param Request $request
     * @return array
     */
    public static function validateUserData(Request $request): array
    {
        $user = Auth::user();
        $rule = 'nullable';
        if ($user->user_type->isExternal()) {
            $rule = 'required';
        }

        // Validate user data
        $validatedUserData = $request->validate([
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'academic_degrees' => 'nullable|string|max:30',
            'department' => 'required|string|max:10',
            'address' => $rule . '|string|max:200',
        ]);
        return $validatedUserData;
    }

    /**
     * @param Request $request
     * @return array
     */
    public static function validateUpdatableTripData(Request $request): array
    {
        $user = Auth::user();
        $rule = 'nullable';
        if ($user->user_type->isExternal()) {
            $rule = 'required';
        }

        $rules = [
            'iban' => $rule . '|string|max:34',
            'transport_id' => 'required|exists:transports,id',
            'spp_symbol_id' => 'required|exists:spp_symbols,id',
            'place_start' => 'required|string|max:200',
            'place_end' => 'required|string|max:200',
            'datetime_start' => 'required|date|after:today',
            'datetime_end' => 'required|date|after:datetime_start',
            'datetime_border_crossing_start' => 'sometimes|required|date',
            'datetime_border_crossing_end' => 'sometimes|required|date'
        ];

//        // Border crossing validation rules for foreign trips
//        if ($trip->type === TripType::FOREIGN && in_array($trip->state, [TripState::UPDATED, TripState::COMPLETED])) {
//            $rules = array_merge($rules, [
//                'datetime_border_crossing_start' =>  'required|date',
//                'datetime_border_crossing_end' => 'required|date'
//            ]);
//        }
        //Validate trip data
        return $request->validate($rules);
    }

    /**
     * @param Request $request
     * @return array
     */
    public static function validateFixedTripData(Request $request): array
    {
        $validatedData = $request->validate([
            'country_id' => 'required|exists:countries,id',
            'transport_id' => 'required|exists:transports,id',
            'place' => 'required|string|max:200',
            'trip_purpose_id' => 'required|integer|min:0',
            'purpose_details' => 'nullable|string|max:50'
        ]);
        return $validatedData;
    }

    /**
     * @param Request $request
     * @return array
     */
    public static function validateReimbursementData(Request $request): array
    {
        if ($request->has('reimbursement')) {
            $validatedReimbursementData = $request->validate([
                'reimbursement_spp_symbol_id' => 'required|exists:spp_symbols,id',
                'reimbursement_date' => 'required|date',
            ]);

            return array(true, self::array_key_replace(
                'reimbursement_spp_symbol_id',
                'spp_symbol_id',
                $validatedReimbursementData
            ));
        }

        return array(false, array());
    }

    /**
     * @param Request $request
     * @return array
     */
    public static function validateConferenceFeeData(Request $request): array
    {
        if ($request->has('conference_fee')) {
            $validatedConferenceFee = $request->validate([
                'organiser_name' => 'required|string|max:100',
                'ico' => 'nullable|string|max:8',
                'organiser_address' => 'required|string|max:200',
                'organiser_iban' => 'required|string|max:34',
                'amount' => 'required|string|max:20',
            ]);

            return array(true, self::array_key_replace(
                'organiser_iban',
                'iban',
                $validatedConferenceFee
            ));
        }

        return array(false, array());
    }

    /**
     * @param BusinessTrip $trip
     * @param Request $request
     * @return array
     */
    public static function validateExpensesData(BusinessTrip $trip, Request $request): array
    {
        $expenses = ['travelling', 'accommodation', 'allowance', 'advance', 'other'];
        $expenseRules = [];
        $expenseValidatedData = [];

        foreach ($expenses as $expenseName) {
            $eurKey = $expenseName . '_expense_eur';
            if ($trip->type === TripType::FOREIGN) {
                $foreignKey = $expenseName . '_expense_foreign';
                $expenseRules[$foreignKey] = ['nullable', 'string'];
            }
            $reimburseKey = $expenseName . '_reimburse';

            // Rules for each expense-related field
            $expenseRules[$eurKey] = 'nullable|string';
            $expenseRules[$reimburseKey] = 'nullable|boolean';

            $validatedTripData = $request->validate([
                $eurKey => $expenseRules[$eurKey],
                $reimburseKey => $expenseRules[$reimburseKey],
            ]);
            if ($trip->type === TripType::FOREIGN) {
                $validatedTripData = array_merge($validatedTripData, $request->validate([
                    $foreignKey => $expenseRules[$foreignKey]]));
            }
            $expenseValidatedData[$expenseName] = $validatedTripData;
        }
        return $expenseValidatedData;
    }

    /**
     * @param array $validatedExpensesData
     * @param BusinessTrip $trip
     * @return void
     */
    public static function createOrUpdateExpenses(array $validatedExpensesData, BusinessTrip $trip): void
    {
        foreach ($validatedExpensesData as $name => $expenseData) {
            $data = [
                'amount_eur' => $expenseData[$name . '_expense_eur'],
                'amount_foreign' => $trip->type === TripType::FOREIGN ? $expenseData[$name . '_expense_foreign'] : null,
                'reimburse' => !array_key_exists($name . '_expense_reimburse', $expenseData),
            ];
            $expense = $trip->{$name . 'Expense'};
            if ($expense == null) {
                $expense = Expense::create($data);
                $trip->update([$name . '_expense_id' => $expense->id]);
            } else {
                $expense->update($data);
            }
        }
    }

    /**
     * @param Request $request
     * @return array
     */
    public static function validateTripContributionsData(Request $request): array
    {
        // Contributions validation
        $checkedContributions = [];
        $contributions = Contribution::all()->pluck('name', 'id');

        // Check for checked contributions checkboxes
        foreach ($contributions as $id => $name) {
            if ($request->has('contribution_' . $id)) {
                $validatedContributionData = $request->validate([
                    'contribution_' . $id . '_detail' => 'nullable|string|max:200',
                ]);
                $updContributionData = self::array_key_replace(
                    'contribution_' . $id . '_detail',
                    'detail',
                    $validatedContributionData
                );
                $updContributionData['contribution_id'] = $id;
                $checkedContributions[] = $updContributionData;
            }
        }
        return $checkedContributions;
    }

    /**
     * @param mixed $validatedReimbursementData
     * @param $trip
     * @return void
     */
    public static function createOrUpdateReimbursement(mixed $validatedReimbursementData, $trip): void
    {
        $reimbursement = Reimbursement::create($validatedReimbursementData);
        $trip->update(['reimbursement_id' => $reimbursement->id]);
    }

    /**
     * @param mixed $validatedConferenceFeeData
     * @param $trip
     * @return void
     */
    public static function createOrUpdateConferenceFee(mixed $validatedConferenceFeeData, $trip): void
    {
        $ConferenceFee = ConferenceFee::create($validatedConferenceFeeData);
        $trip->update(['conference_fee_id' => $ConferenceFee->id]);
    }

    /**
     * @param array $validatedTripContributionsData
     * @param $trip
     * @return void
     */
    public static function createOrUpdateTripContributions(array $validatedTripContributionsData, $trip): void
    {
        foreach ($validatedTripContributionsData as $contribution) {
            $contribution['business_trip_id'] = $trip->id;
            TripContribution::create($contribution);
        }
    }
}
