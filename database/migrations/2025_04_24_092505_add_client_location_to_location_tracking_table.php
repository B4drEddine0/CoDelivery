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
        Schema::table('location_tracking', function (Blueprint $table) {
            $table->decimal('client_latitude', 10, 7)->nullable();
            $table->decimal('client_longitude', 10, 7)->nullable();
            $table->timestamp('client_location_updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('location_tracking', function (Blueprint $table) {
            $table->dropColumn([
                'client_latitude',
                'client_longitude',
                'client_location_updated_at'
            ]);
        });
    }
};
