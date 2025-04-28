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
        // Drop the location_tracking table
        Schema::dropIfExists('location_tracking');
        // Remove location fields from commands table
        Schema::table('commands', function (Blueprint $table) {
            $table->dropColumn([
                'pickup_latitude',
                'pickup_longitude',
                'delivery_latitude',
                'delivery_longitude',
                'livreur_latitude',
                'livreur_longitude'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recreate location_tracking table
        Schema::create('location_tracking', function (Blueprint $table) {
            $table->id();
            $table->foreignId('command_id')->constrained()->onDelete('cascade');
            $table->decimal('pickup_latitude', 10, 7)->nullable();
            $table->decimal('pickup_longitude', 10, 7)->nullable();
            $table->decimal('delivery_latitude', 10, 7)->nullable();
            $table->decimal('delivery_longitude', 10, 7)->nullable();
            $table->decimal('livreur_latitude', 10, 7)->nullable();
            $table->decimal('livreur_longitude', 10, 7)->nullable();
            $table->decimal('client_latitude', 10, 7)->nullable();
            $table->decimal('client_longitude', 10, 7)->nullable();
            $table->json('route')->nullable();
            $table->timestamp('estimated_delivery_time')->nullable();
            $table->timestamp('location_updated_at')->nullable();
            $table->timestamp('client_location_updated_at')->nullable();
            $table->timestamps();
        });
        
        // Add location fields back to commands table
        Schema::table('commands', function (Blueprint $table) {
            $table->decimal('pickup_latitude', 10, 7)->nullable();
            $table->decimal('pickup_longitude', 10, 7)->nullable();
            $table->decimal('delivery_latitude', 10, 7)->nullable();
            $table->decimal('delivery_longitude', 10, 7)->nullable();
            $table->decimal('livreur_latitude', 10, 7)->nullable();
            $table->decimal('livreur_longitude', 10, 7)->nullable();
        });
    }
};
