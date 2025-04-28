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
            $table->decimal('pickup_latitude', 10, 7)->nullable();
            $table->decimal('pickup_longitude', 10, 7)->nullable();
            $table->decimal('delivery_latitude', 10, 7)->nullable();
            $table->decimal('delivery_longitude', 10, 7)->nullable();
            $table->decimal('livreur_latitude', 10, 7)->nullable();
            $table->decimal('livreur_longitude', 10, 7)->nullable();
            $table->decimal('client_latitude', 10, 7)->nullable();
            $table->decimal('client_longitude', 10, 7)->nullable();
            $table->timestamp('livreur_location_updated_at')->nullable();
            $table->timestamp('client_location_updated_at')->nullable();
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
                'client_latitude',
                'client_longitude',
                'livreur_location_updated_at',
                'client_location_updated_at'
            ]);
        });
    }
};