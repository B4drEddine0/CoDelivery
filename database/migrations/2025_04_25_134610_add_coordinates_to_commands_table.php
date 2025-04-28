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
        Schema::table('commands', function (Blueprint $table) {
            // Only add columns that are missing
            if (!Schema::hasColumn('commands', 'pickup_latitude')) {
                $table->decimal('pickup_latitude', 10, 7)->nullable();
            }
            if (!Schema::hasColumn('commands', 'pickup_longitude')) {
                $table->decimal('pickup_longitude', 10, 7)->nullable();
            }
            if (!Schema::hasColumn('commands', 'delivery_latitude')) {
                $table->decimal('delivery_latitude', 10, 7)->nullable();
            }
            if (!Schema::hasColumn('commands', 'delivery_longitude')) {
                $table->decimal('delivery_longitude', 10, 7)->nullable();
            }
            if (!Schema::hasColumn('commands', 'livreur_latitude')) {
                $table->decimal('livreur_latitude', 10, 7)->nullable();
            }
            if (!Schema::hasColumn('commands', 'livreur_longitude')) {
                $table->decimal('livreur_longitude', 10, 7)->nullable();
            }
            // Skip location_updated_at as it already exists
            
            if (!Schema::hasColumn('commands', 'delivery_route')) {
                $table->json('delivery_route')->nullable();
            }
            if (!Schema::hasColumn('commands', 'estimated_delivery_time')) {
                $table->integer('estimated_delivery_time')->nullable(); // in minutes
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('commands', function (Blueprint $table) {
            $table->dropColumn([
                'pickup_latitude',
                'pickup_longitude',
                'delivery_latitude',
                'delivery_longitude',
                'livreur_latitude',
                'livreur_longitude',
                // Don't drop location_updated_at as it was already there
                'delivery_route',
                'estimated_delivery_time'
            ]);
        });
    }
};
