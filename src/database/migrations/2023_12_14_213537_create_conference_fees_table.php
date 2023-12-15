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
        Schema::create('conference_fees', static function (Blueprint $table) {
            $table->id();
            $table->string('organiser_name', 100);
            $table->string('organiser_address', 200);
            $table->string('ico', 8)->nullable();
            $table->string('iban', 34);
            $table->string('amount', 20);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conference_fees');
    }
};
