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
        Schema::table('locations', function (Blueprint $table) {
            $table->longText('image')->nullable()->after('name');
            $table->string('email')->nullable()->after('image');
            $table->string('phone')->nullable()->after('email');
            $table->longText('address')->nullable()->after('phone');
            $table->unsignedBigInteger('country')->nullable()->after('address');
            $table->unsignedBigInteger('state')->nullable()->after('country');
            $table->unsignedBigInteger('city')->nullable()->after('state');
            $table->string('pincode')->nullable()->after('city');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('locations', function (Blueprint $table) {
            $table->dropColumn('image');
            $table->dropColumn('email');
            $table->dropColumn('phone');
            $table->dropColumn('address');
            $table->dropColumn('country');
            $table->dropColumn('state');
            $table->dropColumn('city');
            $table->dropColumn('pincode');
        });
    }
};
