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
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_id');
            $table->string('priority', 200);
            $table->unsignedBigInteger('user_id');
            $table->string('subject', 255)->nullable();
            $table->text('description')->nullable();
            $table->string('user_type', 200);
            $table->integer('status')->default(1); // 1=Open, 2=Assigned, 3=InProgress, 4=Closed
            $table->text('reply_description')->nullable();
            $table->text('attachment')->nullable();
            $table->unsignedBigInteger('assignee_id')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->softDeletes();

            // Foreign Keys
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('assignee_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
