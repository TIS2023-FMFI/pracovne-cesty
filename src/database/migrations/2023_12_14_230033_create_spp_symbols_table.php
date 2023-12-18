<?php

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

            $table->string('spp_symbol', 20);
            $table->string('fund', 10);
            $table->string('functional_region', 10);
            $table->string('financial_centre', 10)->default('107240');
            $table->string('account', 50);
            $table->string('grantee', 100);
            $table->unsignedSmallInteger('status');

            $table->timestamps();
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
