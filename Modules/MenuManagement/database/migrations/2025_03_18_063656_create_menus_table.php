<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void // Added the return type void here
    {
        Schema::create('menus', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('permenantlink')->unique(); // Consider renaming to 'permalink' if typo
            $table->json('menus')->nullable();
            $table->unsignedBigInteger('language_id')->nullable();
            $table->boolean('status')->default(1);
            $table->timestamps();
            $table->softDeletes(); // Adds deleted_at column for SoftDeletes
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menus');
    }
};
