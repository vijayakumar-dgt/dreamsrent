<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void // Specify return type as void
    {
        Schema::create('wallet_history', function (Blueprint $table) {
            $table->id(); // Primary Key
            $table->unsignedBigInteger('user_id')->index(); // User Reference
            $table->decimal('amount', 10, 2)->nullable(); // Transaction Amount
            $table->enum('payment_type', ['paypal', 'bank_transfer', 'others', 'stripe'])->nullable(); // Payment Type
            $table->enum('status', ['Completed', 'Pending', 'Failed', 'Refunded'])->default('Pending'); // Status
            $table->integer('reference_id')->nullable(); // Reference ID
            $table->tinyInteger('type')->default(1)->comment('1 -> Add Amount, 2 -> Booking, 3 -> Leads'); // Transaction Type
            $table->string('transaction_id')->nullable(); // Transaction ID
            $table->timestamp('transaction_date')->nullable(); // Transaction Date
            $table->timestamps(); // Created & Updated At
            $table->softDeletes(); // Soft Delete Support
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void // Specify return type as void
    {
        Schema::dropIfExists('wallet_history');
    }
};
