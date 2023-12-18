<?php

use App\Models\ConferenceFee;
use App\Models\Country;
use App\Models\Expense;
use App\Models\Reimbursement;
use App\Models\SppSymbol;
use App\Models\Transport;
use App\Models\TripPurpose;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('business_trips', static function (Blueprint $table) {
            $table->id();

            // General details
            $table->foreignIdFor(User::class);
            $table->foreignIdFor(Country::class);
            $table->foreignIdFor(Transport::class);

            $table->string('place', 200);

            $table->string('event_url', 200)->nullable();
            $table->string('upload_name', 200)->nullable();

            $table->string('sofia_id', 40)->nullable();
            $table->unsignedSmallInteger('state');


            // Start and end of the trip
            $table->dateTime('datetime_start')->nullable();
            $table->dateTime('datetime_end')->nullable();

            $table->string('place_start', 200)->nullable();
            $table->string('place_end', 200)->nullable();

            $table->dateTime('datetime_border_crossing_start')->nullable();
            $table->dateTime('datetime_border_crossing_end')->nullable();


            // Purpose
            $table->foreignIdFor(TripPurpose::class);
            $table->string('purpose_details', 50)->nullable();


            // Expenses and reimbursement
            $table->string('iban', 34);

            $table->foreignIdFor(ConferenceFee::class)->nullable();
            $table->foreignIdFor(Reimbursement::class)->nullable()->unique();
            $table->foreignIdFor(SppSymbol::class)->nullable();

            // TODO: Do we want these to be unique?
            $table->foreignIdFor(Expense::class, 'accommodation_expense_id')->nullable();
            $table->foreignIdFor(Expense::class, 'travelling_expense_id')->nullable();
            $table->foreignIdFor(Expense::class, 'other_expense_id')->nullable();
            $table->foreignIdFor(Expense::class, 'allowance_id')->nullable();

            $table->string('not_reimbursed_meals')->nullable();
            $table->boolean('meals_reimbursement')->default(true);

            $table->string('advance_amount', '20')->nullable();
            $table->string('expense_estimation', '20')->nullable();


            // Misc
            $table->string('cancellation_reason', 1000)->nullable();
            $table->string('note', 5000)->nullable();
            $table->string('conclusion', 5000)->nullable();


            // Timestamp
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('business_trips');
    }
};
