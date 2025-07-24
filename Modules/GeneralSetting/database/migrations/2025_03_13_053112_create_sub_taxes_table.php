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
        Schema::create('sub_taxes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tax_group_id')->nullable();
            $table->unsignedBigInteger('tax_rate_id')->nullable();
            $table->foreign('tax_group_id')->references('id')->on('tax_groups')->onDelete('cascade');
            $table->foreign('tax_rate_id')->references('id')->on('tax_rates')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sub_taxes');
    }
};
