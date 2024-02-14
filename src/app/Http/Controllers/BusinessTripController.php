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
use App\Models\Country;
use App\Models\Expense;
use App\Models\Reimbursement;
use App\Models\Staff;
use App\Models\TripContribution;
use App\Models\User;
use DateTime;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use mikehaertl\pdftk\Pdf;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;


class BusinessTripController extends Controller
{

    /**
     * Returning view with details from all trips
     * @throws Exception
     */
    public static function index(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            throw new Exception();
        }

        // Check if the user is an admin
        if ($user->hasRole('admin')) {
            // Get filter parameters
            $filter = $request->query('filter');
            $selectedUser = $request->query('user');

            $validator = Validator::make(
                ['filter' => $filter, 'user' => $selectedUser],
                [
                    'filter' => 'string|nullable',
                    'user' => 'integer|nullable'
                ]
            );

            $trips = new BusinessTrip();

            // If the filter parameters are correct
            if (!$validator->fails()) {
                $selectedUser = User::find($selectedUser);

                // Only a single parameter can be used
                if ($filter) {
                    $trips = match ($filter) {
                        'newest' => BusinessTrip::newest(),
                        'unconfirmed' => BusinessTrip::unconfirmed(),
                        'unaccounted' => BusinessTrip::unaccounted(),
                        default => new BusinessTrip(),
                    };
                } else if ($selectedUser) {
                    $trips = $selectedUser->businessTrips();
                }
            }

            // Retrieve filtered trips and all users for admin
            $trips = $trips->paginate(10)->withQueryString();
            $users = User::all();
        } else {
            // Retrieve only user's trips for regular users
            $trips = $user->businessTrips()->paginate(10);
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
    public static function create(Request $request)
    {
        $selectedUser = null;
        if ($request->has('user') && Auth::user()->hasRole('admin')) {
            $selectedUser = User::find($request->query('user'));
            if (!$selectedUser) {
                return redirect()->route('homepage')->withErrors('Používateľ nebol nájdený.');
            }
        }

        return view('business-trips.create', [
            'selectedUser' => $selectedUser,
        ]);
    }



    /**
     * Parsing data from the $request in form
     * Also managing uploaded files from the form
     * Redirecting to the homepage
     * Sending mail with mail component to admin
     *
     * @throws Exception
     */
    public static function store(Request $request): RedirectResponse
    {
        // Get the authenticated user's ID
        $authUser = Auth::user();

        if (!$authUser) {
            throw new Exception();
        }
        $targetUserId = $request->input('target_user');
        $targetUser = $targetUserId ? User::find($targetUserId) : $authUser;

        if (!$targetUser) {
            Log::error("User not found for ID: " . $request->input('target_user'));
            return redirect()->back()->withErrors("Používateľ nebol nájdený.");
        }

        $isForDifferentUser = $targetUser->id !== $authUser->id && $authUser->hasRole('admin');

        if ($isForDifferentUser && !$authUser->hasRole('admin')) {
            return redirect()->back()->withErrors("Nemáte oprávnenie pridávať cesty za iných používateľov.");
        }

        // Validate all necessary data
        $validatedUserData = self::validateUserData($request);
        $validatedTripData = self::validateUpdatableTripData($request) + self::validateFixedTripData($request);

        [$isReimbursement, $validatedReimbursementData] = self::validateReimbursementData($request);
        [$isConferenceFee, $validatedConferenceFeeData] = self::validateConferenceFeeData($request);

        $areContributions = false;
        if ($targetUser->user_type->isExternal()) {
            $validatedTripContributionsData = self::validateTripContributionsData($request);
            $areContributions = true;
        }

        // Add the authenticated user's ID to the validated data
        $validatedTripData['user_id'] = $targetUser->id;

        //Logic to store the trip based on the validated data
        $trip = BusinessTrip::create($validatedTripData);
        if ($isReimbursement) {
            self::createOrUpdateReimbursement($validatedReimbursementData, $trip);
        }

        if ($isConferenceFee) {
            self::createOrUpdateConferenceFee($validatedConferenceFeeData, $trip);
        }

        if ($areContributions) {
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
        $targetUser->update($validatedUserData);

        //Sending mails
        $message = 'ID pridanej cesty: ' . $trip->id . ' Meno a priezvisko cestujúceho: ' . $trip->user->first_name . ' ' . $trip->user->last_name;
        $recipient = 'admin@example.com';
        $viewTemplate = 'emails.new_trip_admin';

        // Create an instance of the SimpleMail class
        $email = new SimpleMail($message, $recipient, $viewTemplate);

        // Send the email
        Mail::to($recipient)->send($email);

        // Check if it was too late to add trip and inform user
        $warningMessage = null;
        $message = null;
        $currentDate = new DateTime();
        $startDate = new DateTime($trip->datetime_start);
        $days = $trip->type == TripType::DOMESTIC ? '4' : '11';

        $newDate = $currentDate->modify("+ ".$days." weekday");

        if ($startDate < $newDate) {
            $warningMessage = 'Vaša pracovná cesta bude pridaná, ale nie je to v súlade s pravidlami.
                               Cestu vždy pridávajte minimálne 4 pracovné dni pred jej začiatkom v prípade,
                               že ide o tuzemskú cestu, a 11 pracovných dní pred začiatkom v prípade zahraničnej cesty.';
        }
        else {
            $message = 'Pracovná cesta bola úspešne vytvorená.';
        }

        //Redirecting to the homepage
        return redirect()->route('homepage')
            ->with('warning', $warningMessage)
            ->with('message', $message);
    }

    /**
     * Get the attachment from a business trip.
     */
    public static function getAttachment(BusinessTrip $trip): StreamedResponse
    {
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
    public static function edit(BusinessTrip $trip)
    {
        $days = self::getTripDurationInDays($trip);

        return view('business-trips.edit', [
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
    public static function update(Request $request, BusinessTrip $trip): RedirectResponse
    {
        if ($trip->state->isFinal()) {
            throw ValidationException::withMessages(['state' => 'Invalid state for updating.']);
        }
        // Check if the authenticated user is an admin
        $user = Auth::user();

        if (!$user) {
            throw new Exception();
        }

        $isAdmin = $user->hasRole('admin');

        $tripState = $trip->state;

        // Admin updating the trip
        if ($isAdmin) {
            $validatedUserData = self::validateUserData($request);

            $validatedTripData = self::validateUpdatableTripData($request) + self::validateFixedTripData($request);
            $validatedTripContributionsData = self::validateTripContributionsData($request);
            [$isReimbursement, $validatedReimbursementData] = self::validateReimbursementData($request);
            [$isConferenceFee, $validatedConferenceFeeData] = self::validateConferenceFeeData($request);

            if ($tripState->hasTravellerReturned()) {
                $validatedExpensesData = self::validateExpensesData($trip, $request);

                $days = self::getTripDurationInDays($trip);
                $validatedMealsData = self::validateMealsData($days, $request);
                $validatedTripData['not_reimbursed_meals'] = $validatedMealsData;

                array_merge($validatedTripData, $request->validate(
                    ['conclusion' => 'required|max:5000']));

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
            self::correctNotReimbursedMeals($trip);


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

                    $days = self::getTripDurationInDays($trip);
                    $validatedMealsData = self::validateMealsData($days, $request);
                    $validatedTripData['not_reimbursed_meals'] = $validatedMealsData;

                    $validatedTripData = array_merge($validatedTripData, $request->validate(
                        ['conclusion' => 'required|max:5000']));

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

        return redirect()->route('trip.edit', ['trip' => $trip])->with('message', 'Údaje o ceste boli úspešne aktualizované.');
    }

    /**
     * Updating state of the trip to cancelled
     * Adding cancellation reason
     * @throws ValidationException
     */
    public static function cancel(Request $request, BusinessTrip $trip): RedirectResponse
    {
        // Check if the trip is in a valid state for cancellation
        if (!in_array($trip->state, [TripState::NEW, TripState::CONFIRMED, TripState::CANCELLATION_REQUEST], true)) {
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
        $message = 'ID Stornovanej cesty: ' . $trip->id;
        $viewTemplate = 'emails.cancellation_user';

        // Create an instance of the SimpleMail class
        $email = new SimpleMail($message, $recipient, $viewTemplate);

        // Send the email
        Mail::to($recipient)->send($email);

        return redirect()->route('trip.edit', $trip)->with('message', 'Cesta bola úspešne stornovaná.');
    }

    /**
     * Updating state of the trip to confirmed
     * @throws ValidationException|Exception
     */
    public static function confirm(Request $request, BusinessTrip $trip): RedirectResponse
    {
        // Check if the trip is in a valid state for confirmation
        if ($trip->state !== TripState::NEW) {
            throw ValidationException::withMessages(['state' => 'Invalid state for confirmation.']);
        }

        // Validate the sofia_id
        $validatedData = $request->validate([
            'sofia_id' => 'required|string|max:40',
        ]);

        // Confirm the trip and record sofia_id
        $trip->update([
            'state' => TripState::CONFIRMED,
            'sofia_id' => $validatedData['sofia_id']
        ]);

        if ($trip->user->pritomnostUser()->first()) {
            SynchronizationController::syncSingleBusinessTrip($trip->id);
        }

        return redirect()
            ->route('trip.edit', $trip)
            ->with('message', 'Cesta bola potvrdená, identifikátor zo systému SOFIA bol priradený.');
    }

    /**
     * Updating state to closed
     * @throws ValidationException
     */
    public static function close(BusinessTrip $trip): RedirectResponse
    {
        // Check if the trip is in a valid state for closing
        if ($trip->state !== TripState::COMPLETED) {
            throw ValidationException::withMessages(['state' => 'Invalid state for closing.']);
        }

        //Close the trip
        $trip->update(['state' => TripState::CLOSED]);

        return redirect()->route('trip.edit', $trip)
            ->with('message', 'Stav cesty bol zmenený na Spracovaná. Na ceste už nie je možné vykonať žiadne zmeny.');
    }

    /**
     * Updating state of the trip to cancellation request
     * @throws ValidationException
     */
    public static function requestCancellation(Request $request, BusinessTrip $trip): RedirectResponse
    {
        // Validate the cancellation reason
        $validatedData = $request->validate([
            'cancellation_reason' => 'required|string|max:1000',
        ]);

        // Check if the current state of the trip allows cancellation request
        if (in_array($trip->state, [TripState::NEW, TripState::CONFIRMED], true)) {
            // Change the state to CANCELLATION_REQUEST
            $trip->update(['state' => TripState::CANCELLATION_REQUEST]);
            $trip->update($validatedData);

            // Send email notification to the admin
            $message = 'ID Cesty: ' . $trip->id . ' Meno a priezvisko cestujúceho: ' . $trip->user->first_name . ' ' . $trip->user->last_name;
            $recipient = 'admin@example.com';
            $viewTemplate = 'emails.cancellation_request_admin';

            // Create an instance of the SimpleMail class
            $email = new SimpleMail($message, $recipient, $viewTemplate);

            // Send the email
            Mail::to($recipient)->send($email);
        } else {
            throw ValidationException::withMessages(['state' => 'Invalid state for cancellation request.']);
        }

        return redirect()->route('trip.edit', $trip->id)
            ->with('message', 'Žiadosť o storno bola úspešne odoslaná.');
    }

    /**
     * Adding comment to trip
     */
    public static function addComment(Request $request, BusinessTrip $trip): RedirectResponse
    {
        // Validate the incoming request
        $validatedData = $request->validate([
            'note' => 'required|string|max:5000',
        ]);

        // Update the trip's note with the new comment
        $trip->update($validatedData);

        // Send email notification to the
        $message = 'ID Cesty ku ktorej bola pridaná poznámka: ' . $trip->id . ' Meno a priezvisko cestujúceho: ' . $trip->user->first_name . ' ' . $trip->user->last_name;
        $recipient = 'admin@example.com';
        $viewTemplate = 'emails.new_note_admin';

        // Create an instance of the SimpleMail class
        $email = new SimpleMail($message, $recipient, $viewTemplate);

        // Send the email
        Mail::to($recipient)->send($email);

        // Redirect or respond with a success message
        return redirect()->route('trip.edit', $trip->id)
            ->with('message', 'Poznámka pre administrátora bola pridaná.');
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
    public static function exportPdf(int $tripId, int $documentType): JsonResponse|BinaryFileResponse
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
                    'address' => $trip->conferenceFee->organiser_address,
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

        $interval = $endDate->diff($startDate);
        $daysDifference = $interval->days;

        return $daysDifference + 1;
    }

    /**
     * @param Request $request
     * @return array
     * @throws Exception
     */
    public static function validateUserData(Request $request): array
    {
        $user = Auth::user();

        if (!$user) {
            throw new Exception();
        }

        $rule = 'nullable';
        if ($user->user_type->isExternal()) {
            $rule = 'required';
        }

        // Validate user data
        $validatedUserData = $request->validate([
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'academic_degrees' => 'nullable|string|max:30',
            'personal_id' => 'required|string|max:10',
            'department' => 'required|string|max:10',
            'address' => $rule . '|string|max:200',
        ]);
        return $validatedUserData;
    }

    /**
     * @param Request $request
     * @return array
     * @throws Exception
     */
    public static function validateUpdatableTripData(Request $request): array
    {
        $user = Auth::user();

        if (!$user) {
            throw new Exception();
        }

        $rules = [
            'iban' => 'required' . '|string|max:34',
            'transport_id' => 'required|exists:transports,id',
            'spp_symbol_id' => 'required|exists:spp_symbols,id',
            'place_start' => 'required|string|max:200',
            'place_end' => 'required|string|max:200',
            'datetime_start' => 'required|date',
            'datetime_end' => 'required|date|after:datetime_start',
            'datetime_border_crossing_start' => 'sometimes|required|date',
            'datetime_border_crossing_end' => 'sometimes|required|date'
        ];

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

        // Set the type of trip based on the selected country
        $selectedCountry = $validatedData['country_id'];
        $validatedData['type'] = self::getTripType($selectedCountry);

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
//        dd($request);
        $expenses = ['travelling', 'accommodation', 'advance', 'other'];
        if ($trip->type === TripType::FOREIGN) {
            $expenses[] = 'allowance';
        }
        $validatedExpensesData = [];

        foreach ($expenses as $expenseName) {
            $validatedExpenseData = $request->validate([
                $expenseName . '_expense_eur' => 'nullable|string|max:20',
                $expenseName . '_expense_not_reimburse' => 'nullable'
            ]);
            if ($trip->type === TripType::FOREIGN) {
                $validatedExpenseData = array_merge($validatedExpenseData, $request->validate([
                    $expenseName . '_expense_foreign' => 'nullable|string:max:20']));
            }

//            $validatedExpenseData[$expenseName . '_expense_reimburse'] = $request->has($expenseName . '_expense_reimburse');
            $validatedExpensesData[$expenseName] = $validatedExpenseData;
        }

        $validatedExpensesData = array_merge($validatedExpensesData, $request->validate([
            'expense_estimation' => 'nullable|string|max:20'
        ]));

        $validatedExpensesData['no_meals_reimbursed'] = $request->has('no_meals_reimbursed');
//        dd($validatedExpensesData);
        return $validatedExpensesData;
    }

    /**
     * @param array $validatedExpensesData
     * @param BusinessTrip $trip
     * @return void
     */
    public static function createOrUpdateExpenses(array $validatedExpensesData, BusinessTrip $trip): void
    {
        $trip->update([
            'expense_estimation' => $validatedExpensesData['expense_estimation'],
            'meals_reimbursement' => !$validatedExpensesData['no_meals_reimbursed']
        ]);
        unset($validatedExpensesData['expense_estimation']);
        unset($validatedExpensesData['no_meals_reimbursed']);


        foreach ($validatedExpensesData as $name => $expenseData) {
            $data = [
                'amount_eur' => $expenseData[$name . '_expense_eur'],
                'amount_foreign' => $trip->type === TripType::FOREIGN ? $expenseData[$name . '_expense_foreign'] : null,
                'reimburse' => !array_key_exists($name . '_expense_not_reimburse', $expenseData),
            ];
            $expense = $trip->{$name . 'Expense'};
            if ($expense == null) {
//                dd($validatedExpensesData, $name, $data);
                $expense = Expense::create($data);

                $trip->update([$name . '_expense_id' => $expense->id]);
                $trip->save();
//                dd($name . '_expense_id', $trip, $expense);
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

    /**
     * @param Request $request
     * @param int $days
     * @return string
     */
    public static function validateMealsData(int $days, Request $request): string
    {
        $notReimbursedMeals = '';
        $checkboxNames = ['b', 'l', 'd']; // Checkbox names prefix
        for ($i = 0; $i < $days; $i++) {
            foreach ($checkboxNames as $prefix){
                $checkboxName = $prefix . $i;
                if (!$request->has($checkboxName)) {
                    $notReimbursedMeals .= '0'; // Checkbox not present, mark as reimbursed
                } else {
                    $notReimbursedMeals .= '1'; // Checkbox present, mark as not reimbursed
                }
            }
        }
        return $notReimbursedMeals;
    }

    /**
     * @param int $selectedCountry
     * @return TripType
     */
    public static function getTripType(int $selectedCountry): TripType
    {
//        dd($selectedCountry, Country::getIdOf('Slovensko'));
        return $selectedCountry === Country::getIdOf('Slovensko')
            ? TripType::DOMESTIC : TripType::FOREIGN;
    }

    /**
     * @param BusinessTrip $trip
     * @return void
     * @throws Exception
     */
    public static function correctNotReimbursedMeals(BusinessTrip $trip): void
    {
        $days = self::getTripDurationInDays($trip);
        $notReimbursedMeals = $trip->not_reimbursed_meals;
        if ($notReimbursedMeals) {
            $mealsLen = strlen($notReimbursedMeals);
            if ($mealsLen < $days*3) {
                $notReimbursedMeals .= str_repeat('0', ($days*3 - $mealsLen));
            } elseif ($mealsLen > $days*3) {
                $notReimbursedMeals = substr($notReimbursedMeals, 0, $days*3);
            }

//
            $trip->update(['not_reimbursed_meals' => $notReimbursedMeals]);
        }
    }

}
