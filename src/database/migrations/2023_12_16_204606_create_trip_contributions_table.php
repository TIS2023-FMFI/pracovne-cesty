<?php

use App\Models\BusinessTrip;
use App\Models\Contribution;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('trip_contributions', function (Blueprint $table) {
            $table->id();

            $table->foreignIdFor(BusinessTrip::class);
            $table->foreignIdFor(Contribution::class);
            $table->string('detail', 200)->nullable();

            $table->timestamps();

            $table->unique(['business_trip_id', 'contribution_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trip_contributions');
    }
};
