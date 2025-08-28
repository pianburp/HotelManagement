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
        // First, update any existing 'pending' payments to 'failed' as a safe fallback
        DB::table('payments')
            ->where('payment_status', 'pending')
            ->update(['payment_status' => 'failed']);

        Schema::table('payments', function (Blueprint $table) {
            // Drop the existing enum column and recreate without 'pending'
            $table->dropColumn('payment_status');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->enum('payment_status', ['completed', 'failed', 'refunded'])->default('failed');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            // Drop the modified column
            $table->dropColumn('payment_status');
        });

        Schema::table('payments', function (Blueprint $table) {
            // Restore the original enum with 'pending'
            $table->enum('payment_status', ['pending', 'completed', 'failed', 'refunded'])->default('pending');
        });
    }
};
