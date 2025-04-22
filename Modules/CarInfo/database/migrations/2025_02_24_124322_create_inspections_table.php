<?php

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
        Schema::create('inspections', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vehicle_info_id');
            $table->date('inspection_date')->nullable();
            $table->unsignedBigInteger('inspector_id')->nullable();
            $table->double('odometer')->nullable()->default(0);
            $table->double('fuel')->nullable()->default(0);
            $table->text('check_list')->nullable();
            $table->longText('notes')->nullable();
            $table->string('inspection_status')->nullable();
            $table->string('repair_status')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inspections');
    }
};
