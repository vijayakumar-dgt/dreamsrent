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
        Schema::create('faqs', function (Blueprint $table) {
            $table->id();
            $table->integer('order_by')->default(0);
            $table->string('question');
            $table->text('answer');
            $table->boolean('status')->default(1); // 1 = Active, 0 = Inactive
            $table->foreignId('language_id')->constrained('languages')->onDelete('cascade'); // Assuming a languages table exists
            $table->foreignId('parent_id')->nullable()->constrained('faqs')->onDelete('cascade'); // For sub-FAQs
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('faqs');
    }
};
