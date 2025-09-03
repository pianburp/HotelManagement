<?php

use App\Models\RoomType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Migrate existing translations from room_type_translations table to Spatie Translatable JSON format
     */
    public function up(): void
    {
        // First add JSON columns to the room_types table if they don't exist
        if (!Schema::hasColumn('room_types', 'translations')) {
            Schema::table('room_types', function (Blueprint $table) {
                $table->json('translations')->nullable()->after('is_active');
            });
        }

        // Get all room types
        $roomTypes = DB::table('room_types')->get();

        // For each room type, get its translations and migrate them to the JSON format
        foreach ($roomTypes as $roomType) {
            $translations = DB::table('room_type_translations')
                ->where('room_type_id', $roomType->id)
                ->get();

            $translationsArray = [];

            // Process each translation field
            foreach ($translations as $translation) {
                // Add name translations
                if (isset($translation->name)) {
                    $translationsArray['name'][$translation->locale] = $translation->name;
                }

                // Add description translations
                if (isset($translation->description)) {
                    $translationsArray['description'][$translation->locale] = $translation->description;
                }

                // Add size translations
                if (isset($translation->size)) {
                    $translationsArray['size'][$translation->locale] = $translation->size;
                }

                // Add amenities_description translations
                if (isset($translation->amenities_description)) {
                    $translationsArray['amenities_description'][$translation->locale] = $translation->amenities_description;
                }
            }

            // Update the room type with the new translations
            if (!empty($translationsArray)) {
                DB::table('room_types')
                    ->where('id', $roomType->id)
                    ->update([
                        'translations' => json_encode($translationsArray)
                    ]);
            }
        }

        // We're keeping the old translations table for now as a backup
        // It can be removed in a later migration if needed
    }

    /**
     * Reverse the migrations.
     * This is a data migration, so we don't need to do anything in reverse
     * The translations table will still be there as a backup
     */
    public function down(): void
    {
        // Nothing to do here
    }
};
