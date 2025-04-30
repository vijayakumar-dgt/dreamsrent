<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('user_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->integer('category_id')->nullable()->index();
            $table->integer('subcategory_id')->nullable()->index();
            $table->integer('parent_id')->default(0);
            $table->string('profile_image')->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('mobile_number', 20)->nullable();
            $table->enum('gender', ['Male', 'Female', 'Other'])->nullable()->index();
            $table->date('dob')->nullable();
            $table->text('bio')->nullable();
            $table->string('address')->nullable();
            $table->integer('country_id')->nullable()->index();
            $table->integer('state_id')->nullable()->index();
            $table->integer('city_id')->nullable()->index();
            $table->string('postal_code', 20)->nullable();
            $table->string('currency_code', 10)->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->string('language', 10)->nullable()->index();
            $table->string('company_image')->nullable();
            $table->string('company_name')->nullable()->index();
            $table->string('company_website')->nullable();
            $table->string('company_address')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique('user_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_details');
    }
};
