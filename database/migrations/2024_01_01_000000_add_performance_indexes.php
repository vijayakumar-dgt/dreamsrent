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
        // Add indexes for bookings table
        Schema::table('bookings', function (Blueprint $table) {
            $table->index(['customer_id', 'booking_status'], 'idx_bookings_customer_status');
            $table->index(['vehicle_id', 'start_datetime', 'end_datetime'], 'idx_bookings_vehicle_dates');
            $table->index(['booking_status', 'created_at'], 'idx_bookings_status_created');
            $table->index('pickup_location', 'idx_bookings_pickup_location');
            $table->index('return_location', 'idx_bookings_return_location');
            $table->index('driving_type', 'idx_bookings_driving_type');
        });

        // Add indexes for vehicle_info table
        Schema::table('vehicle_info', function (Blueprint $table) {
            $table->index(['status', 'language_id'], 'idx_vehicle_status_language');
            $table->index(['type', 'status'], 'idx_vehicle_type_status');
            $table->index(['brand_id', 'status'], 'idx_vehicle_brand_status');
            $table->index(['model_id', 'status'], 'idx_vehicle_model_status');
            $table->index(['location_id', 'status'], 'idx_vehicle_location_status');
            $table->index('popular', 'idx_vehicle_popular');
            $table->index('recommended', 'idx_vehicle_recommended');
        });

        // Add indexes for users table
        Schema::table('users', function (Blueprint $table) {
            $table->index(['user_type', 'status'], 'idx_users_type_status');
            $table->index(['language_id', 'status'], 'idx_users_language_status');
            $table->index('email', 'idx_users_email');
            $table->index('phone_number', 'idx_users_phone');
        });

        // Add indexes for locations table
        Schema::table('locations', function (Blueprint $table) {
            $table->index(['status', 'language_id'], 'idx_locations_status_language');
            $table->index('name', 'idx_locations_name');
        });

        // Add indexes for vehicle_tarrifs table
        if (Schema::hasTable('vehicle_tarrifs')) {
            Schema::table('vehicle_tarrifs', function (Blueprint $table) {
                $table->index(['vehicle_id', 'tariff_from_days', 'tariff_to_days'], 'idx_tarrifs_vehicle_days');
            });
        }

        // Add indexes for vehicle_seasons table
        if (Schema::hasTable('vehicle_seasons')) {
            Schema::table('vehicle_seasons', function (Blueprint $table) {
                $table->index(['vehicle_id', 'seasonal_start_date', 'seasonal_end_date'], 'idx_seasons_vehicle_dates');
            });
        }

        // Add indexes for reviews table
        if (Schema::hasTable('reviews')) {
            Schema::table('reviews', function (Blueprint $table) {
                $table->index(['reviewable_type', 'reviewable_id'], 'idx_reviews_reviewable');
                $table->index(['user_id', 'created_at'], 'idx_reviews_user_created');
            });
        }

        // Add indexes for wishlists table
        if (Schema::hasTable('wishlists')) {
            Schema::table('wishlists', function (Blueprint $table) {
                $table->index(['user_id', 'vehicle_id'], 'idx_wishlists_user_vehicle');
            });
        }

        // Add indexes for extra_services table
        if (Schema::hasTable('extra_services')) {
            Schema::table('extra_services', function (Blueprint $table) {
                $table->index(['status', 'language_id'], 'idx_extra_services_status_language');
            });
        }

        // Add indexes for insurances table
        if (Schema::hasTable('insurances')) {
            Schema::table('insurances', function (Blueprint $table) {
                $table->index(['status', 'language_id'], 'idx_insurances_status_language');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop indexes for bookings table
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropIndex('idx_bookings_customer_status');
            $table->dropIndex('idx_bookings_vehicle_dates');
            $table->dropIndex('idx_bookings_status_created');
            $table->dropIndex('idx_bookings_pickup_location');
            $table->dropIndex('idx_bookings_return_location');
            $table->dropIndex('idx_bookings_driving_type');
        });

        // Drop indexes for vehicle_info table
        Schema::table('vehicle_info', function (Blueprint $table) {
            $table->dropIndex('idx_vehicle_status_language');
            $table->dropIndex('idx_vehicle_type_status');
            $table->dropIndex('idx_vehicle_brand_status');
            $table->dropIndex('idx_vehicle_model_status');
            $table->dropIndex('idx_vehicle_location_status');
            $table->dropIndex('idx_vehicle_popular');
            $table->dropIndex('idx_vehicle_recommended');
        });

        // Drop indexes for users table
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('idx_users_type_status');
            $table->dropIndex('idx_users_language_status');
            $table->dropIndex('idx_users_email');
            $table->dropIndex('idx_users_phone');
        });

        // Drop indexes for locations table
        Schema::table('locations', function (Blueprint $table) {
            $table->dropIndex('idx_locations_status_language');
            $table->dropIndex('idx_locations_name');
        });

        // Drop indexes for other tables
        if (Schema::hasTable('vehicle_tarrifs')) {
            Schema::table('vehicle_tarrifs', function (Blueprint $table) {
                $table->dropIndex('idx_tarrifs_vehicle_days');
            });
        }

        if (Schema::hasTable('vehicle_seasons')) {
            Schema::table('vehicle_seasons', function (Blueprint $table) {
                $table->dropIndex('idx_seasons_vehicle_dates');
            });
        }

        if (Schema::hasTable('reviews')) {
            Schema::table('reviews', function (Blueprint $table) {
                $table->dropIndex('idx_reviews_reviewable');
                $table->dropIndex('idx_reviews_user_created');
            });
        }

        if (Schema::hasTable('wishlists')) {
            Schema::table('wishlists', function (Blueprint $table) {
                $table->dropIndex('idx_wishlists_user_vehicle');
            });
        }

        if (Schema::hasTable('extra_services')) {
            Schema::table('extra_services', function (Blueprint $table) {
                $table->dropIndex('idx_extra_services_status_language');
            });
        }

        if (Schema::hasTable('insurances')) {
            Schema::table('insurances', function (Blueprint $table) {
                $table->dropIndex('idx_insurances_status_language');
            });
        }
    }
};