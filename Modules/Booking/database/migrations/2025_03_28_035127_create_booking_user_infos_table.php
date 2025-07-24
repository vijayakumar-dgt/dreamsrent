<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('booking_user_infos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('booking_id');
            // Driver Information
            $table->string('driver_first_name')->nullable();
            $table->string('driver_last_name')->nullable();
            $table->integer('driver_age')->nullable();
            $table->string('driver_mobile_number')->nullable();
            $table->string('driver_licence')->nullable();
            $table->boolean('driver_check')->default(false);
            // User Information
            $table->string('first_name');
            $table->string('last_name');
            $table->string('no_person');
            $table->string('company')->nullable();
            $table->text('address');
            $table->unsignedBigInteger('country_id');
            $table->unsignedBigInteger('state_id');
            $table->unsignedBigInteger('city_id')->nullable();
            $table->string('pincode');
            $table->string('email');
            $table->string('phone_number');
            $table->text('add_info')->nullable();
            $table->boolean('terms_check')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_user_infos');
    }
};
