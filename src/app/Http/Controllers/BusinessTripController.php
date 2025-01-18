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
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use DateTime;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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

        // Get sort parameter, default to 'date_created'
        $sort = $request->query('sort', 'date_created');

        // Check if the user is an admin
        if ($user->hasRole('admin')) {
            // Get filter parameters
            $filter = $request->query('filter');
            $selectedUser = $request->query('user');

            $validator = Validator::make(
                ['filter' => $filter, 'user' => $selectedUser],
                [
                    'filter' => 'string|nullable',
                    'user' => 'integer|nullable',
                    'sort' => 'string|nullable|in:sofia_id,place,date_created,date_start'
                ]
            );

            $trips = BusinessTrip::query();

            // If the filter parameters are correct
            if (!$validator->fails()) {
                $selectedUser = User::find($selectedUser);

                // Only a single parameter can be used
                if ($filter) {
                    $trips = match ($filter) {
                        'all' => BusinessTrip::getAll(),
                        'unconfirmed' => BusinessTrip::unconfirmed(),
                        'unaccounted' => BusinessTrip::unaccounted(),
                        default => BusinessTrip::query(),
                    };
                } else if ($selectedUser) {
                    $trips = $selectedUser->businessTrips();
                }
            }

            // Retrieve filtered trips and all users for admin
            $users = User::getSortedByLastName();
        } else {
            // Retrieve only user's trips for regular users
            $trips = $user->businessTrips();
            // No need for a list of users
            $users = null;
        }

        // Apply sorting based on the 'sort' parameter
        $trips = match ($sort) {
            'date_created' => $trips->orderBy('created_at', 'desc'),
            'date_start' => $trips->orderBy('datetime_start', 'asc'),
            'place' => $trips->orderBy('place', 'asc'),
            'sofia_id' => $trips->orderByRaw('CAST(sofia_id AS UNSIGNED) DESC'),
            default => $trips,
        };

        // Paginate the results
        $trips = $trips->paginate(10)->withQueryString();

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
        $validatedTripData = array_merge($validatedTripData,
            $request->validate([
                'event_url' => 'nullable|url|max:200',
                'spp_symbol_id' => 'required'
            ]));

        if (!$request->filled('spp_symbol_id')) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['spp_symbol_id' => 'ŠPP je povinné.']);
        }

        [$isReimbursement, $validatedReimbursementData] = self::validateReimbursementData($request);
        [$isConferenceFee, $validatedConferenceFeeData] = self::validateConferenceFeeData($request);

        $areContributions = false;
        if ($targetUser->user_type->isExternal()) {
            $validatedTripContributionsData = self::validateTripContributionsData($request);
            $areContributions = true;
        }

        // Add the authenticated user's ID to the validated data
        $validatedTripData['user_id'] = $targetUser->id;

        //check for duplicity
        $date_start = date('Y-m-d', strtotime($validatedTripData['datetime_start']));
        $date_end = date('Y-m-d', strtotime($validatedTripData['datetime_end']));
        if(BusinessTrip::isDuplicate($validatedTripData['user_id'], $validatedTripData['place'], $date_start, $date_end))
        {

            return redirect()->back()->withInput()->withErrors(["duplicate" => "Cesta s rovnakými údajmi už v systéme existuje a preto táto cesta nebola uložená."]);
        }

        // Start DB transaction before writing
        DB::beginTransaction();

        try {
            // Logic to store iban
            if(isset($request->storeIban)){
                User::updateIbanOfUserWithId($targetUserId,$request->iban);
            }

            // Logic to store the trip based on the validated data
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

            // Handle file uploads
            if ($request->hasFile('upload_name')) {
                $file = $request->file('upload_name');

                // Store the file in the storage/app/trips directory
                $upload_name = Storage::disk('uploads')->putFile('', $file);

                // Save the file path in the model
                $trip->update(['upload_name' => $upload_name]);
            }

            // Update user details
            $targetUser->update($validatedUserData);

            // Increment the trips count for the selected country
            $trip->country->incrementTripsCount();

            // Save changes
            DB::commit();

        } catch (Exception $e) {
            // Rollback in case something went wrong
            DB::rollBack();
	    Log::info("add trip exception: " . $e->getMessage());
            return redirect()->route('homepage')
                ->with('message', 'Pracovná cesta nebola kvôli chybe vytvorená. Zopakujte to neskôr, prosím.');
        }

        // Sending mails
        $message ='ID pridanej cesty: ' . $trip->sofia_id
            . ' Meno a priezvisko cestujúceho: ' . $trip->user->fullName();

        foreach (User::getAdminEmails() as $recipient) {
            // Create an instance of the SimpleMail class
            $email = new SimpleMail(
                $message,
                $recipient,
                'emails.new_trip_admin',
                'Pracovné cesty - pridaná nová cesta'
            );

            // Send the email
            Mail::to($recipient)->send($email);
        }

        // Check if it was too late to add trip and inform user
        $warningMessage = null;
        $message = null;
        $currentDate = new DateTime();
        $startDate = new DateTime($trip->datetime_start);
        $days = $trip->type == TripType::DOMESTIC ? '4' : '11';

        $newDate = $currentDate->modify("+ " . $days . " weekday");

        if ($startDate < $newDate) {
            $warningMessage = 'Vaša pracovná cesta bude pridaná, ale nie je to v súlade s pravidlami.
                               Cestu vždy pridávajte minimálne 4 pracovné dni pred jej začiatkom v prípade,
                               že ide o tuzemskú cestu, a 11 pracovných dní pred začiatkom v prípade zahraničnej cesty.';
        } else {
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
        if ($trip->state === TripState::CANCELLED) {
            throw ValidationException::withMessages(['state' => 'Invalid state for updating.']);
        }
        // Check if the authenticated user is an admin
        $user = Auth::user();
        if (!$user) {
            throw new Exception();
        }

        $isAdmin = $user->hasRole('admin');
        $tripState = $trip->state;

        $oldTripData = $trip->getAttributes();

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
            }

            // Check if sofia_id is updated and check for duplicates
            $sofiaId = $request->input('sofia_id', $trip->sofia_id);
            if ($sofiaId !== $trip->sofia_id) {
                if (BusinessTrip::isDuplicateSofiaId($sofiaId, $trip->id)) {
                    return redirect()
                        ->back()
                        ->withErrors(["sofia_id" => "Tento identifikátor je už v systéme použitý."])
                        ->withInput();
                }
                $validatedTripData['sofia_id'] = $sofiaId;
            }

            // Start DB transaction before writing
            DB::beginTransaction();

            try {
                if ($tripState->hasTravellerReturned()) {
                    self::createOrUpdateExpenses($validatedExpensesData, $trip);
                }

                if ($isReimbursement) {
                    self::createOrUpdateReimbursement($validatedReimbursementData, $trip);
                } else {
                    $trip->reimbursement()->delete();
                    $trip->update(['reimbursement_id' => null]);
                }

                if ($isConferenceFee) {
                    self::createOrUpdateConferenceFee($validatedConferenceFeeData, $trip);
                } else {
                    $trip->conferenceFee()->delete();
                    $trip->update(['conference_fee_id' => null]);
                }

                self::createOrUpdateTripContributions($validatedTripContributionsData, $trip);

                $trip->user->update($validatedUserData);

                $oldCountryId = $trip->country_id;

                // Update the trip with the provided data
                $trip->update($validatedTripData);

                self::correctNotReimbursedMeals($trip);

                // If country changed, update both countries' trip counts
                if ($oldCountryId !== $trip->country_id) {
                    Country::find($oldCountryId)->decrementTripsCount();
                    $trip->country->incrementTripsCount();
                }

                DB::commit();

            } catch (Exception $e) {
                DB::rollBack();
                return redirect()
                    ->route('trip.edit', ['trip' => $trip])
                    ->with('message', 'Údaje o ceste neboli kvôli chybe aktualizované. Skúste to neskôr, prosím.');
            }

        } else { // Non-admin user updating the trip
            // Validate based on trip state
            switch ($tripState) {
                case TripState::NEW:
                case TripState::CONFIRMED:
                    $validatedTripData = self::validateUpdatableTripData($request);

                    $validatedExpensesData = self::validateExpensesData($trip, $request);

                    $days = self::getTripDurationInDays($trip);
                    $validatedMealsData = self::validateMealsData($days, $request);
                    $validatedTripData['not_reimbursed_meals'] = $validatedMealsData;

                    self::correctNotReimbursedMeals($trip);

                    $validatedTripData = array_merge($validatedTripData, $request->validate(
                        ['conclusion' => 'required|max:5000']));

                    break;

                // Updating a wrong state trip
                default:
                    throw ValidationException::withMessages(['state' => 'Invalid state for updating.']);
            }

            // Start DB transaction before writing
            DB::beginTransaction();

            try {
                // Update based on trip state
                if ($tripState === TripState::CONFIRMED) {

                    // Adding report to an CONFIRMED state trip
                    self::createOrUpdateExpenses($validatedExpensesData, $trip);

                    // Change the state to COMPLETED
                    $trip->update(['state' => TripState::COMPLETED]);
                }

                // Update the trip with the provided data
                $trip->update($validatedTripData);

                DB::commit();

            } catch (Exception $e) {
                DB::rollBack();
                return redirect()
                    ->route('trip.edit', ['trip' => $trip])
                    ->with('message', 'Údaje o ceste neboli kvôli chybe aktualizované. Skúste to neskôr, prosím.');
            }

            // Sending mails
            foreach (User::getAdminEmails() as $recipient) {
                // Create an instance of the SimpleMail class
                $email = new SimpleMail(
                    '',
                    $recipient,
                    'emails.new_trip_admin',
                    'Pracovné cesty - pridaná nová cesta'
                );

                // Send the email
                Mail::to($recipient)->send($email);
            }
        }

        if (self::isSyncRequired($oldTripData, $trip->getAttributes())) {
            if ($trip->user->pritomnostUser()->first()) {
                $status = SynchronizationController::updateSingleBusinessTrip($trip->id);

                if (!$status) {
                    return redirect()
                        ->route('trip.edit', ['trip' => $trip])
                        ->with('message', 'Doplnenú cestu sa nepodarilo zosynchronizovať s dochádzkovým systémom.');
                }
            }
        }

        return redirect()
            ->route('trip.edit', ['trip' => $trip])
            ->with('message', 'Údaje o ceste boli úspešne aktualizované.');
    }

    private static function isSyncRequired(array $oldTripData, array $tripData): bool {
        if ($oldTripData['datetime_start'] !== $tripData['datetime_start']) {
            return true;
        }
        if ($oldTripData['datetime_end'] !== $tripData['datetime_end']) {
            return true;
        }
        if ($oldTripData['type'] !== $tripData['type']) {
            return true;
        }

        return false;
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

        // Decrement the trips count for the country
        $trip->country->decrementTripsCount();

        //Send cancellation email to user

        // Retrieve user's email associated with the trip
        $recipient = $trip->user->email;
        $message = 'Chceme vás informovať, že vaša pracovná cesta s ID ' .  $trip->sofia_id
        . ' naplánovaná na ' . $trip->datetime_start
        . ' s miestom konania ' . $trip->place . ' bola stornovaná.';
        $viewTemplate = 'emails.cancellation_user';

        // Create an instance of the SimpleMail class
        $email = new SimpleMail($message, $recipient, $viewTemplate, 'Pracovné cesty - stornovaná cesta');

        // Send the email
        Mail::to($recipient)->send($email);

        if ($trip->user->pritomnostUser()->first()) {
            $status = SynchronizationController::deleteCancelledBusinessTrip($trip->id);

            if (!$status) {
                return redirect()
                    ->route('trip.edit', ['trip' => $trip])
                    ->with('message', 'Stornovanú cestu sa nepodarilo odstrániť z dochádzkového systému.');
            }
        }

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

        // Check if the sofia_id is a duplicate
        if (BusinessTrip::isDuplicateSofiaId($validatedData['sofia_id'], $trip->id)) {
            return redirect()
                ->back()
                ->withErrors(["sofia_id" => "Tento identifikátor je už v systéme použitý."])
                ->withInput();
        }

        // Confirm the trip and record sofia_id
        $trip->update([
            'state' => TripState::CONFIRMED,
            'sofia_id' => $validatedData['sofia_id']
        ]);

        $recipient = $trip->user->email;
        $message = 'Vaša pracovná cesta '
        . ' naplánovaná na ' . $trip->datetime_start
        . ' s miestom konania ' . $trip->place
        . ' bola úspešne spracovaná administrátorom a bol jej pridelený identifikátor. Prosíme Vás, aby ste sa dostavili na podpísanie cestovného príkazu.';
        $viewTemplate = 'emails.tmp';

        // Create an instance of the SimpleMail class
        $email = new SimpleMail($message, $recipient, $viewTemplate, 'Pracovné cesty - pridelenie identifikátora');

        // Send the email
        Mail::to($recipient)->send($email);

        if ($trip->user->pritomnostUser()->first()) {
            $status = SynchronizationController::createSingleBusinessTrip($trip->id);

            if (!$status) {
                return redirect()
                    ->route('trip.edit', ['trip' => $trip])
                    ->with('message', 'Cestu sa nepodarilo zosynchronizovať s dochádzkovým systémom.');
            }
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
            $message = 'ID pracovnej cesty: ' . $trip->sofia_id
                . ' Meno a priezvisko cestujúceho: ' . $trip->user->fullName();

            foreach (User::getAdminEmails() as $recipient) {
                // Create an instance of the SimpleMail class
                $email = new SimpleMail(
                    $message,
                    $recipient,
                    'emails.cancellation_request_admin',
                    'Pracovné cesty - žiadosť o storno cesty'
                );

                // Send the email
                Mail::to($recipient)->send($email);
            }
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
        $message = 'ID Cesty ku ktorej bola pridaná poznámka: ' . $trip->sofia_id
            . ' Meno a priezvisko cestujúceho: ' . $trip->user->fullName();

        foreach (User::getAdminEmails() as $recipient) {
            // Create an instance of the SimpleMail class
            $email = new SimpleMail(
                $message,
                $recipient,
                'emails.new_note_admin',
                'Pracovné cesty - pridaná nová poznámka'
            );

            // Send the email
            Mail::to($recipient)->send($email);
        }

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
        $tripUserType = $trip->user->user_type;
        $tripState = $trip->state;
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
                if ($trip->type != TripType::FOREIGN) {
                    return response()->json(['error' => 'Document not applicable for domestic trips.'], 403);
                }
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
                if (!$tripUserType->isExternal()) {
                    return response()->json(['error' => 'Unauthorized document type for user.'], 403);
                }
                $contributions = $trip->contributions;
                $dean = Staff::where('position', PositionTitle::DEAN)->first();
                $secretary = Staff::where('position', PositionTitle::SECRETARY)->first();

                $data = [
                    'first_name' => $trip->user->first_name,
                    'last_name' => $trip->user->last_name,
                    'academic_degree' => $trip->user->academic_degrees,
                    'address' => $trip->user->address,
                    'contribution1' => $contributions->contains('id', 1) ? 'yes1' : null,
                    'contribution2' => $contributions->contains('id', 2) ? 'yes2' : null,
                    'contribution3' => $contributions->contains('id', 3) ? 'yes3' : null,
                    'department' => $trip->user->department,
                    'place' => $trip->country->name . ', ' . $trip->place,
                    'datetime_start' => $trip->datetime_start->format('d.m.Y'),
                    'datetime_end' => $trip->datetime_end->format('d.m.Y'),
                    'transport' => $trip->transport->name,
                    'trip_purpose' => $trip
                            ->tripPurpose
                            ->name . (isset($trip->purpose_details) ? ' - ' . $trip->purpose_details : ''),
                    'functional_region' => $trip->sppSymbol->functional_region ?? "",
                    'financial_centre' => $trip->sppSymbol->financial_centre ?? "",
                    'spp_symbol' => $trip->sppSymbol->spp_symbol ?? "",
                    'account' => $trip->type == TripType::DOMESTIC ? '631001' : '631002',
                    'grantee' => $trip->sppSymbol->grantee ?? "",
                    'iban' => $trip->iban,
                    'incumbent_name1' => $dean->incumbent_name ?? "",
                    'incumbent_name2' => $secretary->incumbent_name ?? "",
                    'position_name1' => $dean->position_name ?? "",
                    'position_name2' => $secretary->position_name ?? "",
                    'contribution1_text' => $contributions->where('id', 1)->first()?->pivot->detail ?? "",
                    'contribution2_text' => $contributions->where('id', 2)->first()?->pivot->detail ?? "",
                    'contribution3_text' => $contributions->where('id', 3)->first()?->pivot->detail ?? "",
                ];
                break;

            case DocumentType::CONTROL_SHEET:
                if ($trip->conference_fee_id == null) {
                    return response()->json(['error' => 'Conference fee not requested.'], 403);
                }
                $data = [
                    'spp_symbol' => $trip->sppSymbol->spp_symbol ?? "",
                    // ! rename expense_estimation to amount in PDF template
                    'expense_estimation' => $trip->conferenceFee->amount ?? "",
                    'functional_region1' => $trip->sppSymbol->functional_region ?? "",
                    'spp_symbol1' => $trip->sppSymbol->spp_symbol ?? "",
                    'financial_centre1' => $trip->sppSymbol->financial_centre ?? "",
                    'purpose_details' => 'Úhrada vložného',
                ];
                break;

            case DocumentType::PAYMENT_ORDER:
                if ($trip->conference_fee_id == null) {
                    return response()->json(['error' => 'Conference fee not requested.'], 403);
                }
                $data = [
                    // ! rename fields PDF template
                    'advance_amount' => $trip->conferenceFee->amount ?? "Žiadne",
                    'grantee' => $trip->conferenceFee->organiser_name ?? "---",
                    'address' => $trip->conferenceFee->organiser_address ?? "---",
                    'functional_region' => $trip->sppSymbol->functional_region ?? "",
                    'spp_symbol' => $trip->sppSymbol->spp_symbol ?? "",
                    'financial_centre' => $trip->sppSymbol->financial_centre ?? "",
                    'iban' => $trip->conferenceFee->iban ?? "",
                ];
                break;

            case DocumentType::DOMESTIC_REPORT:
                if (!in_array($tripState, [TripState::COMPLETED, TripState::CLOSED])) {
                    return response()->json(['error' => 'Report not available for current trip state.'], 403);
                }
                if ($trip->type != TripType::DOMESTIC) {
                    return response()->json(
                        ['error' => 'FOREIGN_REPORT not applicable for domestic trips.'],
                        403
                    );
                }
                $name = ($trip->user->academic_degrees ?? '')
                    . ' ' . $trip->user->first_name
                    . ' ' . $trip->user->last_name;

                $mealsReimbursementText = $trip->meals_reimbursement
                    ? 'mám záujem'
                    : 'Nenárokujem si';

                $data = [
                    'name' => $name,
                    'department' => $trip->user->department,
                    'date_start' => $trip->datetime_start->format('d.m.Y'),
                    'date_end' => $trip->datetime_end->format('d.m.Y'),
                    'spp_symbol' => $trip->sppSymbol->spp_symbol,
                    'time_start' => $trip->datetime_start->format('H.i'),
                    'time_end' => $trip->datetime_end->format('H.i'),
                    'place' => $trip->place,
                    'transport' => $trip->transport->name,
                    'travelling_expense' => $trip->travellingExpense->amount_eur ?? "Nenárokujem si",
                    'accommodation_expense' => $trip->accommodationExpense->amount_eur ?? "Nenárokujem si",
                    'other_expenses' => $trip->otherExpense->amount_eur ?? "Nenárokujem si",
                    // ! rename allowance to advance in PDF template //vlozne...
                    'allowance' => $trip->advanceExpense->amount_eur ?? "Žiadne",
                    'conclusion' => $trip->conclusion,
                    'iban' => $trip->iban,
                    'address' => $trip->user->address,
                    'meals_reimbursement_DG42' => $mealsReimbursementText,
                ];
                break;

            case DocumentType::FOREIGN_REPORT:
                if (!in_array($tripState, [TripState::COMPLETED, TripState::CLOSED])) {
                    return response()->json(['error' => 'Report not available for current trip state.'], 403);
                }
                if ($trip->type != TripType::FOREIGN) {
                    return response()->json(
                        ['error' => 'DOMESTIC_REPORT not applicable for foreign trips.'],
                        403
                    );
                }
                $mealsReimbursementText = $trip->meals_reimbursement
                    ? 'mám záujem'
                    : 'Nenárokujem si';

		//TODO: fix after renaming
		$other_exp = $trip->otherExpense->amount_eur;
		if (is_null($other_exp))
		{
			$other_exp = $trip->advanceExpense->amount_eur;
			if (is_null($other_exp)) $other_exp = "Nenárokujem si";
		}
		else
		{
			if (!is_null($trip->advanceExpense->amount_eur))
			    $other_exp = $other_exp . " + " . $trip->advanceExpense->amount_eur;
			else $other_exp = $trip->advanceExpense->amount_eur;
		}
		$other_exp_foreign = $trip->otherExpense->amount_foreign;
		if (is_null($other_exp_foreign))
		{
			$other_exp_foreign = $trip->advanceExpense->amount_foreign;
			if (is_null($other_exp_foreign)) $other_exp_foreign = "Nenárokujem si";
		}
		else
		{
			if (!is_null($trip->advanceExpense->amount_foreign))
			    $other_exp_foreign = $other_exp_foreign . " + " . $trip->advanceExpense->amount_foreign;
			else $other_exp_foreign = $trip->advanceExpense->amount_foreign;
		}

                $data = [
                    'name' => $trip->user->first_name . ' ' . $trip->user->last_name,
                    'department' => $trip->user->department,
                    'country' => $trip->country->name,
                    'datetime_end' => $trip->datetime_end->format('d.m.Y H.i'),
                    'datetime_start' => $trip->datetime_start->format('d.m.Y H.i'),
                    'datetime_border_crossing_start' => $trip->datetime_border_crossing_start->format('d.m.Y H.i'),
                    'datetime_border_crossing_end' => $trip->datetime_border_crossing_end->format('d.m.Y H.i'),
                    'place' => $trip->place,
                    'spp_symbol' => $trip->sppSymbol->spp_symbol,
                    'transport' => $trip->transport->name,
                    'travelling_expense_foreign' => $trip->travellingExpense->amount_foreign ?? "Nenárokujem si",
                    'travelling_expense' => $trip->travellingExpense->amount_eur ?? "Nenárokujem si",
                    'accommodation_expense_foreign' => $trip->accommodationExpense->amount_foreign ?? "Nenárokujem si",
                    'accommodation_expense' => $trip->accommodationExpense->amount_eur ?? "Nenárokujem si",
                    'meals_reimbursement' => $mealsReimbursementText,
                    'meals_reimbursement_foreign' => $mealsReimbursementText,
                    'other_expenses_foreign' => $other_exp_foreign, //$trip->otherExpense->amount_foreign ?? "Nenárokujem si",
                    'other_expenses' => $other_exp, // $trip->otherExpense->amount_eur ?? "Nenárokujem si",
                    'conclusion' => $trip->conclusion,
                    'iban' => $trip->iban,
		    // allowance is expected not to be reimbursed
		    'allowance' => "Nenárokujem si",
		    'allowance_foreign' => "Nenárokujem si",
                    // ! rename advance to allowance in PDF template
                    'advance_expense_foreign' => $trip->allowanceExpense->amount_foreign ?? "Nenárokujem si",
                    'advance_expense' => $trip->allowanceExpense->amount_eur ?? "Nenárokujem si",
                    'invitation_case_charges' => $trip->expense_estimation,
                ];
                break;

            default:
                return response()->json(['error' => 'Unknown document type'], 400);
        }

        try {
            Log::info("Creating PDF object with template path: " . $templatePath);
            $pdf = new Pdf($templatePath, [ 'locale' => 'sk_SK.utf8', 'procEnv' => [ 'LANG' => 'sk_SK.utf-8', ], ]);
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
        $startDate = (new Carbon($trip->datetime_start))->midDay();
        $endDate = (new Carbon($trip->datetime_end))->midDay();

        return CarbonPeriod::create($startDate, '1 day', $endDate)->count();
    }

    /**
     * @param Request $request
     * @return array
     * @throws Exception
     */
    private static function validateUserData(Request $request): array
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
    private static function validateUpdatableTripData(Request $request): array
    {
        $user = Auth::user();

        if (!$user) {
            throw new Exception();
        }

        $rules = [
            'transport_id' => 'required|exists:transports,id',
            'place_start' => 'required|string|max:200',
            'place_end' => 'required|string|max:200',
            'datetime_start' => 'required|date',
            'datetime_end' => 'required|date|after:datetime_start',
            'datetime_border_crossing_start' => 'sometimes|required|date',
            'datetime_border_crossing_end' => 'sometimes|required|date',
            'conclusion' => 'sometimes|required|string|max:5000',
            'sofia_id' => 'string|max:40'
        ];

        return $request->validate($rules);
    }

    /**
     * @param Request $request
     * @return array
     */
    private static function validateFixedTripData(Request $request): array
    {
        $validatedData = $request->validate([
            'iban' => 'required|string|max:34',
            'country_id' => 'required|exists:countries,id',
            'transport_id' => 'required|exists:transports,id',
            'place' => 'required|string|max:200',
            'trip_purpose_id' => 'required|integer|min:0',
            'purpose_details' => 'nullable|string|max:200',
            'spp_symbol_id' => 'required|exists:spp_symbols,id',
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
    private static function validateReimbursementData(Request $request): array
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
    private static function validateConferenceFeeData(Request $request): array
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
    private static function validateExpensesData(BusinessTrip $trip, Request $request): array
    {
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
                $validatedExpenseData = array_merge(
                    $validatedExpenseData,
                    $request->validate([$expenseName . '_expense_foreign' => 'nullable|string:max:20'])
                );
            }

            $validatedExpensesData[$expenseName] = $validatedExpenseData;
        }

        $validatedExpensesData = array_merge(
            $validatedExpensesData,
            $request->validate(['expense_estimation' => 'nullable|string|max:20'])
        );

        $validatedExpensesData['no_meals_reimbursed'] = $request->has('no_meals_reimbursed');
        return $validatedExpensesData;
    }

    /**
     * @param array $validatedExpensesData
     * @param BusinessTrip $trip
     * @return void
     */
    private static function createOrUpdateExpenses(array $validatedExpensesData, BusinessTrip $trip): void
    {
        $trip->update([
            'expense_estimation' => $validatedExpensesData['expense_estimation'],
            'meals_reimbursement' => !$validatedExpensesData['no_meals_reimbursed']
        ]);
        unset($validatedExpensesData['expense_estimation'], $validatedExpensesData['no_meals_reimbursed']);


        foreach ($validatedExpensesData as $name => $expenseData) {
            $data = [
                'amount_eur' => $expenseData[$name . '_expense_eur'],
                'amount_foreign' => $trip->type === TripType::FOREIGN ? $expenseData[$name . '_expense_foreign'] : null,
                'reimburse' => !array_key_exists($name . '_expense_not_reimburse', $expenseData),
            ];
            $expense = $trip->{$name . 'Expense'};
            if ($expense == null) {
                $expense = Expense::create($data);

                $trip->update([$name . '_expense_id' => $expense->id]);
                $trip->save();
            } else {
                $expense->update($data);
            }
        }
    }

    /**
     * @param Request $request
     * @return array
     */
    private static function validateTripContributionsData(Request $request): array
    {
        // Contributions validation
        $checkedContributions = [];
        $contributionIds = Contribution::all()->pluck( 'id');

        // Check for checked contributions checkboxes
        foreach ($contributionIds as $id) {
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
                $checkedContributions[$id] = $updContributionData;
            }
        }
        return $checkedContributions;
    }

    /**
     * @param array $validatedReimbursementData
     * @param $trip
     * @return void
     */
    private static function createOrUpdateReimbursement(array $validatedReimbursementData, $trip): void
    {
        $reimbursement = $trip->reimbursement;
        if ($reimbursement == null) {
            $reimbursement = Reimbursement::create($validatedReimbursementData);

            $trip->update(['reimbursement_id' => $reimbursement->id]);

        } else {
            $reimbursement->update($validatedReimbursementData);
        }
    }

    /**
     * @param array $validatedConferenceFeeData
     * @param $trip
     * @return void
     */
    private static function createOrUpdateConferenceFee(array $validatedConferenceFeeData, $trip): void
    {
        $conferenceFee = $trip->conferenceFee;
        if ($conferenceFee == null) {
            $conferenceFee = ConferenceFee::create($validatedConferenceFeeData);

            $trip->update(['conference_fee_id' => $conferenceFee->id]);

        } else {
            $conferenceFee->update($validatedConferenceFeeData);
        }
    }

    /**
     * @param array $validatedTripContributionsData
     * @param $trip
     * @return void
     */
    private static function createOrUpdateTripContributions(array $validatedTripContributionsData, $trip): void
    {
        $contributionIds = Contribution::all()->pluck( 'id');

        foreach ($contributionIds as $id) {
            if (array_key_exists($id, $validatedTripContributionsData)) {
                $tripContributionData = $validatedTripContributionsData[$id];
                $tripContributionData['business_trip_id'] = $trip->id;

                $foundContribution = $trip->contributions
                    ->where('id', $id)
                    ->first();

                if ($foundContribution == null) {
                    TripContribution::create($tripContributionData);

                } else {
                    $trip->contributions()
                        ->updateExistingPivot($tripContributionData['contribution_id'], $tripContributionData);
                }
            } else {
                $trip->contributions()->detach($id);
            }
        }
    }

    /**
     * @param Request $request
     * @param int $days
     * @return string
     */
    private static function validateMealsData(int $days, Request $request): string
    {
        $notReimbursedMeals = '';
        $checkboxNames = ['b', 'l', 'd']; // Checkbox names prefix
        for ($i = 0; $i < $days; $i++) {
            foreach ($checkboxNames as $prefix) {
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
    private static function getTripType(int $selectedCountry): TripType
    {
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
            if ($mealsLen < $days * 3) {
                $notReimbursedMeals .= str_repeat('0', ($days * 3 - $mealsLen));
            } elseif ($mealsLen > $days * 3) {
                $notReimbursedMeals = substr($notReimbursedMeals, 0, $days * 3);
            }

            $trip->update(['not_reimbursed_meals' => $notReimbursedMeals]);
        }
    }

}
