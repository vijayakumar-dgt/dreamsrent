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
        Schema::create('vehicle_seasons', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vehicle_id');
            $table->string('seasonal_title');
            $table->date('seasonal_start_date');
            $table->date('seasonal_end_date');
            $table->decimal('seasonal_daily_rate', 10, 2);
            $table->decimal('seasonal_weekly_rate', 10, 2);
            $table->decimal('seasonal_monthly_rate', 10, 2);
            $table->decimal('seasonal_late_fee', 10, 2);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicle_seasons');
    }
};
