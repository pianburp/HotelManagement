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
        Schema::table('room_type_translations', function (Blueprint $table) {
            $table->string('size', 100)->nullable()->after('description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('room_type_translations')) {
            try {
                Schema::table('room_type_translations', function (Blueprint $table) {
                    $table->dropColumn('size');
                });
            } catch (\Exception $e) {
                // If the column doesn't exist or drop fails during rollback, ignore to allow other rollbacks to continue.
            }
        }
    }
};
