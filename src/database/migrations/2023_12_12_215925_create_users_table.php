<?php

use App\Enums\UserStatus;
use App\Enums\UserType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', static function (Blueprint $table) {
            $table->id();
            $table->string('first_name', 50);
            $table->string('last_name', 50);
            $table->string('academic_degrees', 30)->nullable();
            $table->string('personal_id', 10)->nullable();
            $table->string('department', 10)->default('KAI');
            $table->string('email', 127)->unique();
            $table->string('address', 200)->nullable();

            $table->unsignedSmallInteger('user_type')
                ->default(UserType::EMPLOYEE->value);

            $table->string('username', 255)->unique();
            $table->string('password', 255);

            $table->unsignedSmallInteger('status')->default(UserStatus::ACTIVE->value);
            $table->dateTime('last_login')->nullable();

            $table->rememberToken();

            $table->string('iban', 34)->nullable();
            $table->unsignedSmallInteger('spp_user_type')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
