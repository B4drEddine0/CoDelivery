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
        Schema::create('location_tracking', function (Blueprint $table) {
            $table->id();
            $table->foreignId('command_id')->constrained()->onDelete('cascade');
            $table->decimal('pickup_latitude', 10, 7)->nullable();
            $table->decimal('pickup_longitude', 10, 7)->nullable();
            $table->decimal('delivery_latitude', 10, 7)->nullable();
            $table->decimal('delivery_longitude', 10, 7)->nullable();
            $table->decimal('livreur_latitude', 10, 7)->nullable();
            $table->decimal('livreur_longitude', 10, 7)->nullable();
            $table->json('delivery_route')->nullable();
            $table->integer('estimated_delivery_time')->nullable(); // in minutes
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('location_tracking');
    }
};
