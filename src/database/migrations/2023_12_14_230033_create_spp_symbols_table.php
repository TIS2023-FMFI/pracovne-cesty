<?php

use App\Enums\SppStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('spp_symbols', static function (Blueprint $table) {
            $table->id();

            $table->string('spp_symbol', 30);
            $table->string('functional_region', 10);
            $table->string('financial_centre', 10)->default('107240');
            $table->string('grantee', 100);
            $table->unsignedSmallInteger('status')->default(SppStatus::ACTIVE->value);

            $table->timestamps();
            $table->string('agency', 100);
            $table->string('acronym', 10);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spp_symbols');
    }
};
