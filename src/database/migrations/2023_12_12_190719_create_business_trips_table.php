<?php

use App\Enums\TripState;
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

            $table->unsignedSmallInteger('type');
            $table->foreignIdFor(Country::class);

            $table->foreignIdFor(Transport::class);

            $table->string('place', 200);

            $table->string('event_url', 200)->nullable();
            $table->string('upload_name', 200)->nullable();

            $table->string('sofia_id', 40)->default('0000');
            $table->unsignedSmallInteger('state')->default(TripState::NEW->value);


            // Start and end of the trip
            $table->dateTime('datetime_start')->nullable();
            $table->dateTime('datetime_end')->nullable();

            $table->string('place_start', 200)->nullable();
            $table->string('place_end', 200)->nullable();

            $table->dateTime('datetime_border_crossing_start')->nullable();
            $table->dateTime('datetime_border_crossing_end')->nullable();


            // Purpose
            $table->foreignIdFor(TripPurpose::class);
            $table->string('purpose_details', 200)->nullable();


            // Expenses and reimbursement
            $table->string('iban', 34);

            $table->foreignIdFor(ConferenceFee::class)->nullable()->unique();
            $table->foreignIdFor(Reimbursement::class)->nullable()->unique();
            $table->foreignIdFor(SppSymbol::class, 'spp_symbol_id')->nullable();
            $table->foreignIdFor(SppSymbol::class, 'spp_symbol_id_2')->nullable();
            $table->foreignIdFor(SppSymbol::class, 'spp_symbol_id_3')->nullable();

            $table->smallInteger('amount_eur')->nullable();
            $table->smallInteger('amount_eur_2')->nullable();
            $table->smallInteger('amount_eur_3')->nullable();

            // Cestovne
            $table->foreignIdFor(Expense::class, 'travelling_expense_id')
                ->nullable()
                ->unique();

            // Ubytovanie
            $table->foreignIdFor(Expense::class, 'accommodation_expense_id')
                ->nullable()
                ->unique();

            // Vlozne
            $table->foreignIdFor(Expense::class, 'participation_expense_id')
                ->nullable()
                ->unique();

            // Poistenie
            $table->foreignIdFor(Expense::class, 'insurance_expense_id')
                ->nullable()
                ->unique();

            // Ine vydavky
            $table->foreignIdFor(Expense::class, 'other_expense_id')
                ->nullable()
                ->unique();

            // Vreckove
            $table->foreignIdFor(Expense::class, 'allowance_expense_id')
                ->nullable()
                ->unique();

            // Zaloha
            $table->foreignIdFor(Expense::class, 'advance_expense_id')
                ->nullable()
                ->unique();

            $table->string('not_reimbursed_meals')->nullable();
            $table->boolean('meals_reimbursement')->default(true);

            $table->string('expense_estimation', '20')->nullable();


            // Misc
            $table->string('cancellation_reason', 1000)->nullable();
            $table->string('note', 5000)->nullable();
            $table->string('conclusion', 5000)->nullable();

            // Template
            $table->boolean('is_template')->default(false);

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
