<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up() {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone_number')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->nullable();
            $table->tinyInteger('user_type')->nullable()->comment('1=admin, 2=providers, 3=users');
            $table->rememberToken();
            $table->text('fcm_token')->nullable();
            $table->tinyInteger('status')->default(1)->comment('0=Inactive, 1=Active');
            $table->string('auth_provider_id')->nullable();
            $table->string('auth_provider')->nullable();
            $table->integer('user_language_id')->default(1);
            $table->integer('role_id')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('user_type');
            $table->index('status');
            $table->index('role_id');
            $table->index('user_language_id');
            $table->index('auth_provider');
            $table->index('created_at');
            $table->fullText(['name', 'email']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
