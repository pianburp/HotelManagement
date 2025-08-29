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
        // Update existing payment records to use MYR currency
        DB::table('payments')
            ->where('currency', 'USD')
            ->update(['currency' => 'MYR']);

        // Update the default value for future records
        Schema::table('payments', function (Blueprint $table) {
            $table->string('currency', 3)->default('MYR')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert payment records back to USD currency
        DB::table('payments')
            ->where('currency', 'MYR')
            ->update(['currency' => 'USD']);

        // Revert the default value
        Schema::table('payments', function (Blueprint $table) {
            $table->string('currency', 3)->default('USD')->change();
        });
    }
};
