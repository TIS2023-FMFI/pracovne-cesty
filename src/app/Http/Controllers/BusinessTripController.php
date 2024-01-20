<?php

namespace App\Http\Controllers;

use App\Enums\DocumentType;
use App\Models\BusinessTrip;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use mikehaertl\pdftk\Pdf;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class BusinessTripController extends Controller
{

    /**
     * Returning view with details from all trips
     */
    public function index() {
        return view();
    }

    /**
     * Returning view with the trip details
     */
    public function show(BusinessTrip $b) {
        return view();
    }

    /**
     * Returning view with the form for addding of the new trip
     */
    public function create() {
        return view();
    }

    /**
     * Parsing data from the $request in form
     * Also managing uploaded files from the form
     * Redirecting to the homepage
     * Sending mail with mail component to admin
     */
    public function store(Request $request) {

    }

    /**
     * Returning the view with the trip editing form
     */
    public function edit(BusinessTrip $b) {
        return view();
    }

    /**
     * Validation as in create(), should check if user is admin/not
     * Redirecting to the trip editing form
     * Sending mail with mail component to admin
     */
    public function update(Request $request, BusinessTrip $b) {

    }

    /**
     * Next 3 functions could be combined to changeState(Request $request, BuisenessTrip $b)
     *
     * Updating state of the trip to cancelled
     * Adding cancellation reason
     */
    public function cancel(BusinessTrip $b) {

    }

    /**
     * Same as update(), updating state of the trip to confirmed
     */
    public function confirm() {

    }

    /**
     * Same as update, updating state to closed
     */
    public function close() {

    }

    public function exportPdf(int $tripId, DocumentType $documentType): JsonResponse | BinaryFileResponse
    {
        $trip = BusinessTrip::find($tripId);
        if (!$trip && $tripId !== 0) {
            return response()->json(['error' => 'Business trip not found'], 404);
        }

        $templateName = $documentType->value;
        $templatePath = Storage::disk('pdf-templates')
            ->path($templateName);

        if (Storage::disk('pdf-templates')->missing($templateName)) {
            Log::error("PDF template file does not exist at path: " . $templatePath);
            return response()->json(['error' => 'PDF template file not found'], 404);
        }

        $data = [];
        switch ($documentType) {
            case DocumentType::FOREIGN_TRIP_AFFIDAVIT:
                $data = [
                    'order_number' => '999',
                    'trip_duration' => '0000-00-00 00:00:00', //$trip->'?', // tu potrebujem pomôcť, ktorý stĺpec z databázy to je
                    'adress' => 'adresa',
                    'name' => 'meno',
                    /*
                    'order_number' => $trip->sofia_id,
                    'trip_duration' => '?', //$trip->'?', // tu potrebujem pomôcť, ktorý stĺpec z databázy to je
                    'adress' => $trip->place,
                    'name' => $trip->user->first_name . ' ' . $trip->user->last_name,
                    */
                ];
                break;

            case DocumentType::COMPENSATION_AGREEMENT:
                $data = [
                    'first_name' => $trip->user->first_name,
                    'last_name' => $trip->user->last_name,
                    'academic_degree' => $trip->user->academic_degrees,
                    'address' => $trip->user->address,
                    'contribution1' => '?', // v databáze neexistuje prepojenie na tabuľku s prínosmi pre fakultu
                    'contribution2' => '?', // -||-
                    'contribution3' => '?', // -||-
                    'department' => $trip->user->department,
                    'place' => $trip->country->name . ', ' . $trip->place,
                    'datetime_start' => $trip->datetime_start->format('Y-m-d'),
                    'datetime_end' => $trip->datetime_end->format('Y-m-d'),
                    'transport' => $trip->transport->name,
                    'trip_purpose' => $trip->tripPurpose->name,
                    'fund' => $trip->sppSymbol->fund,
                    'functional_region' => $trip->sppSymbol->functional_region,
                    'financial_centre' => $trip->sppSymbol->financial_centre,
                    'spp_symbol' => $trip->sppSymbol->spp_symbol,
                    'account' => $trip->sppSymbol->account,
                    'grantee' => $trip->sppSymbol->grantee,
                    'iban' => $trip->iban,
                    'bank_name' => '?',
                    'incumbent_name2' => '?', // sem sa bude dopĺnať meno a priezvisko z tabuľky staff
                    'incumbent_name1' => '?', // -||-
                    'position_name1' => '?',  // -||-
                    'position_name2' => '?',  // -||-
                    'contribution1_text' => '?', // v databáze neexistuje prepojenie na tabuľku s prínosmi pre fakultu
                    'contribution2_text' => '?', // -||-
                    'contribution3_text' => '?', // -||-
                ];
                break;

            case DocumentType::CONTROL_SHEET:
                $data = [
                    'spp_symbol' => $trip->sppSymbol->spp_symbol,
                    'expense_estimation' => $trip->expense_estimation,
                    'incumbent_name1' => '?', // to isté čo v COMPENSATION_AGREEMENT, dopĺňanie z tabuľky staff
                    'incumbent_name2' => '?', // -||-
                    'incumbent_name3' => '?', // -||-
                    'source1' => $trip->sppSymbol->fund, // niesom si istý, či zdroj je fund
                    'source2' => $trip->sppSymbol->fund,
                    'source3' => $trip->sppSymbol->fund,
                    'functional_region1' => $trip->sppSymbol->functional_region,
                    'functional_region2' => $trip->sppSymbol->functional_region,
                    'functional_region3' => $trip->sppSymbol->functional_region,
                    'spp_symbol1' => $trip->sppSymbol->spp_symbol,
                    'spp_symbol2' => $trip->sppSymbol->spp_symbol,
                    'spp_symbol3' => $trip->sppSymbol->spp_symbol,
                    'financial_centre1' => $trip->sppSymbol->financial_centre,
                    'financial_centre2' => $trip->sppSymbol->financial_centre,
                    'financial_centre3' => $trip->sppSymbol->financial_centre,
                    'incumbent_name4' => '?', // to čo vyššie
                    'incumbent_name5' => '?',
                    'purpose_details' => $trip->purpose_details,
                ];
                break;

            case DocumentType::PAYMENT_ORDER:
                $data = [
                    'advance_amount_string' => '?', // niesom si istý či máme pre toto vytvorený stĺpec
                    'advance_amount' => $trip->advanceExpense ? $trip->advanceExpense->amount_eur : null,
                    'grantee' => $trip->sppSymbol->grantee,
                    'address' => $trip->user->address,
                    'incumbent_name' => '?', // to isté čo v COMPENSATION_AGREEMENT, dopĺňanie z tabuľky staff
                    'source' => $trip->sppSymbol->fund,
                    'functional_region' => $trip->sppSymbol->functional_region,
                    'spp_symbol' => $trip->sppSymbol->spp_symbol,
                    'financial_centre' => $trip->sppSymbol->functional_region,
                    'iban' => $trip->iban,
                ];
                break;

            case DocumentType::DOMESTIC_REPORT:
                $data = [
                    'name' => $trip->user->first_name . ' ' . $trip->user->last_name,
                    'department' => $trip->user->department,
                    'date_start' => $trip->datetime_start->format('Y-m-d'),
                    'date_end' => $trip->datetime_end->format('Y-m-d'),
                    'spp_symbol' => $trip->spp_symbols->spp_symbol,
                    'time_start' => $trip->datetime_start->format('H:i'),
                    'time_end' => $trip->datetime_end->format('H:i'),
                    'transport' => $trip->transport->name,
                    'travelling_expense' => $trip->travellingExpense ? $trip->travellingExpense->amount_eur : null,
                    'accommodation_expense' => $trip->accommodationExpense ? $trip->accommodationExpense->amount_eur : null,
                    'other_expenses' => $trip->otherExpense ? $trip->otherExpense->amount_eur : null,
                    'allowance' => $trip->allowanceExpense ? $trip->allowanceExpense->amount_eur : null,
                    'expenses' => '?', // neviem ktorý stĺpec predstavuje celkové náklady na cestu
                    'conclusion' => $trip->conclusion,
                    'iban_foreign' => '?', // tu všade kde je otáznik, potrebujem pomôcť
                    'bank_address' => '?',
                    'iban' => $trip->iban,
                    'address' => $trip->user->address,
                    'swift' => '?',
                    'place' => '?',
                    'meals_reimbursement_DG42' => '?',
                    'conclusion_MESS' => '?',
                ];
                break;

            case DocumentType::FOREIGN_REPORT:
                $data = [
                    'name' => $trip->user->first_name . ' ' . $trip->user->last_name,
                    'department' => $trip->user->department,
                    'country' => $trip->country->name,
                    'datetime_end' => $trip->datetime_end->format('Y-m-d H:i'),
                    'datetime_start' => $trip->datetime_start->format('Y-m-d H:i'),
                    'datetime_border_crossing_start' => $trip->datetime_border_crossing_start->format('Y-m-d H:i'),
                    'datetime_border_crossing_end' => $trip->datetime_border_crossing_end->format('Y-m-d H:i'),
                    'place' => $trip->place,
                    'spp_symbol' => $trip->sppSymbol->spp_symbol,
                    'transport' => $trip->transport->name,
                    'travelling_expense_foreign' => $trip->travellingExpense ? $trip->travellingExpense->amount_foreign : null,
                    'travelling_expense' => $trip->travellingExpense ? $trip->travellingExpense->amount_eur : null,
                    'accommodation_expense_foreign' => $trip->accommodationExpense ? $trip->accommodationExpense->amount_foreign : null,
                    'accommodation_expense' => $trip->accommodationExpense ? $trip->accommodationExpense->amount_eur : null,
                    'meals_reimbursement_foreign' => '?', // toto máme uložené v databáze len ako 1 alebo 0
                    'allowance_foreign' => $trip->allowanceExpense ? $trip->allowanceExpense->amount_foreign : null,
                    'allowance' => $trip->allowanceExpense ? $trip->allowanceExpense->amount_eur : null,
                    'meals_reimbursement' => '?', // to isté ako pri meals_reimbursement_foreign
                    'other_expenses_foreign' => $trip->otherExpense ? $trip->otherExpense->amount_foreign : null,
                    'other_expenses' => $trip->otherExpense ? $trip->otherExpense->amount_eur : null,
                    'expenses_foreign' => '?', // opäť neviem nájsť stĺpec ktorý predstavuje celkové náklady
                    'expenses' => '?', // -||-
                    'conclusion' => $trip->conclusion,
                    'bank_code' => '?',
                    'iban' => $trip->iban,
                    'advance_expense_foreign' => $trip->advanceExpense ? $trip->advanceExpense->amount_foreign : null,
                    'invitation_case_charges' => '?',   // neviem ktorý riadok predstavuje odhadnutie preplatených
                    // výdavkov v prípade pozvania druhou stranou
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
            Log::info("Filling form with data: " . json_encode($data, JSON_THROW_ON_ERROR));
            $pdf->fillForm($data);

            Log::info("Starting PDF generation for path: " . $outputPath);

            Log::info("Flattening PDF");
            $pdf->flatten();

            Log::info("Saving PDF to path: " . $outputPath);
            $pdf->saveAs($outputPath);
        } catch (Exception $e) {
            Log::error("Error during PDF manipulation: " . $e->getMessage());
            return response()->json(['error' => 'Failed during PDF manipulation: ' . $e->getMessage()], 500);
        }

        Log::info("PDF generation completed, checking file existence...");
        if (Storage::disk('pdf-exports')->exists($outputName)) {
            Log::info("PDF generation successful, file exists at path: " . $outputPath);
            return response()->download($outputPath)->deleteFileAfterSend(true);
        }

        Log::error("PDF file does not exist after generation: " . $outputPath);
        return response()->json(['error' => 'Failed to generate PDF, file not found'], 500);
    }
}
