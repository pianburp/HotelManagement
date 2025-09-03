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
        Schema::table('waitlists', function (Blueprint $table) {
            $table->decimal('max_price', 10, 2)->nullable()->after('status');
            $table->string('contact_name')->nullable()->after('max_price');
            $table->string('contact_email')->nullable()->after('contact_name');
            $table->string('contact_phone')->nullable()->after('contact_email');
            $table->text('special_requests')->nullable()->after('contact_phone');
            $table->text('notes')->nullable()->after('special_requests');
            $table->boolean('notify')->default(true)->after('notes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('waitlists', function (Blueprint $table) {
            $table->dropColumn([
                'max_price',
                'contact_name',
                'contact_email',
                'contact_phone',
                'special_requests',
                'notes',
                'notify'
            ]);
        });
    }
};
