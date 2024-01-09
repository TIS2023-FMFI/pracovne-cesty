<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use mikehaertl\pdftk\Pdf;
use App\Models\BusinessTrip;
use App\Enums\DocumentType;

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
    public function exportPdf(Request $request, $tripId, $documentType)
    {
        $trip = BusinessTrip::find($tripId);
        if (!$trip && $tripId != 0) {
            return response()->json(['error' => 'Business trip not found'], 404);
        }

        $templatePath = storage_path('app/pdf_templates/' . DocumentType::from($documentType)->value);

        $pdf = new Pdf($templatePath);

        $outputPath = storage_path('app/output_pdf/' . 'output_' . time() . '.pdf');

        $data = [];
        switch ($documentType) {
            case DocumentType::FOREIGN_TRIP_AFFIDAVIT:
                $data = [
                    'numberOfCommand' => '123', // Príklad hodnoty, nahraďte reálnymi údajmi
                    'totalTime' => '48 hodín', // Príklad hodnoty, nahraďte reálnymi údajmi
                    'placeOfResidence' => $trip->destination, // Predpokladáme, že destinácia cesty je miesto pobytu
                    'nameOfDeclarator' => $trip->user->name, // Meno používateľa z modelu BusinessTrip
                ];
                break;
        }

        $pdf->fillForm($data)
            ->flatten()
            ->saveAs($outputPath);

        return response()->download($outputPath)->deleteFileAfterSend(true);
    }

}
