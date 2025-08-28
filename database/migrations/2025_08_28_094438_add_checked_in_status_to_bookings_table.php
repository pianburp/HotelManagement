<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First drop the existing enum column
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        // Recreate with the new enum values including 'checked_in'
        Schema::table('bookings', function (Blueprint $table) {
            $table->enum('status', ['pending', 'confirmed', 'checked_in', 'cancelled', 'completed', 'no_show'])->default('pending');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to original enum without 'checked_in'
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->enum('status', ['pending', 'confirmed', 'cancelled', 'completed', 'no_show'])->default('pending');
        });
    }
};
