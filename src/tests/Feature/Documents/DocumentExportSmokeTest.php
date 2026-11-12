<?php

namespace Tests\Feature\Documents;

use App\Enums\DocumentType;
use App\Models\BusinessTrip;
use App\Models\ConferenceFee;
use App\Models\User;
use Database\Factories\BusinessTripFactory;
use Illuminate\Support\Facades\File;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\DatabaseTestCase;

/**
 * Every document the application prints really comes out of pdflatex as a PDF.
 * Needs a TeX installation; skipped where there is none.
 */
class DocumentExportSmokeTest extends DatabaseTestCase
{
    private const PDF_SIGNATURE = '%PDF-';

    protected function setUp(): void
    {
        parent::setUp();

        if (! is_executable(config('laratex.binPath'))) {
            $this->markTestSkipped('pdflatex is not installed at '.config('laratex.binPath'));
        }
    }

    public static function documentsWithATripThatAllowsThem(): array
    {
        return [
            'affidavit for a foreign trip' => [
                DocumentType::FOREIGN_TRIP_AFFIDAVIT,
                static fn () => BusinessTrip::factory()->foreign()->confirmed(),
            ],
            'compensation agreement for an external traveller' => [
                DocumentType::COMPENSATION_AGREEMENT,
                static fn () => BusinessTrip::factory()->for(User::factory()->extern())->confirmed(),
            ],
            'control sheet for a conference fee' => [
                DocumentType::CONTROL_SHEET,
                static fn () => self::tripWithConferenceFee(),
            ],
            'payment order for a conference fee' => [
                DocumentType::PAYMENT_ORDER,
                static fn () => self::tripWithConferenceFee(),
            ],
            'report from a domestic trip' => [
                DocumentType::DOMESTIC_REPORT,
                static fn () => BusinessTrip::factory()->completed(),
            ],
            'report from a foreign trip' => [
                DocumentType::FOREIGN_REPORT,
                static fn () => BusinessTrip::factory()->foreign()->completed(),
            ],
        ];
    }

    #[DataProvider('documentsWithATripThatAllowsThem')]
    public function test_document_is_exported_as_a_pdf(DocumentType $document, callable $tripFactory): void
    {
        $trip = $tripFactory()->create();

        $response = $this->actingAs($trip->user)
            ->get(route('trip.export', ['trip' => $trip, 'fileType' => $document->value]));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
        $pdfPath = $response->baseResponse->getFile()->getPathname();
        $this->assertStringStartsWith(self::PDF_SIGNATURE, file_get_contents($pdfPath));

        // The application deletes the file once it has been sent, which never happens in a test.
        File::delete($pdfPath);
    }

    private static function tripWithConferenceFee(): BusinessTripFactory
    {
        return BusinessTrip::factory()->foreign()->confirmed()->state([
            'conference_fee_id' => ConferenceFee::factory()->state([
                'organiser_name' => 'ACM',
                'organiser_address' => '1601 Broadway, New York',
                'ico' => null,
                'amount' => 'EUR 350.00',
            ]),
        ]);
    }
}
