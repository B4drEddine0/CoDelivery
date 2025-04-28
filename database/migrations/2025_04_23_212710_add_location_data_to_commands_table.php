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
            // Removed all tracking and real-time location fields from commands table
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('commands', function (Blueprint $table) {
            $table->decimal('pickup_latitude', 10, 7)->nullable();
            $table->decimal('pickup_longitude', 10, 7)->nullable();
            $table->decimal('delivery_latitude', 10, 7)->nullable();
            $table->decimal('delivery_longitude', 10, 7)->nullable();
            $table->decimal('livreur_latitude', 10, 7)->nullable();
            $table->decimal('livreur_longitude', 10, 7)->nullable();
            $table->timestamp('location_updated_at')->nullable();
            $table->json('delivery_route')->nullable();
            $table->integer('estimated_delivery_time')->nullable(); // in minutes
        });
    }
};
