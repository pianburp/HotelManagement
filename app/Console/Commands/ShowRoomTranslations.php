<?php

namespace App\Console\Commands;

use App\Models\RoomType;
use App\Models\RoomTypeTranslation;
use Illuminate\Console\Command;

class ShowRoomTranslations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:show-room-translations';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Display room type translations';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Room Type Translations:');
        
        $translations = RoomTypeTranslation::with('roomType')->get();
        
        if ($translations->isEmpty()) {
            $this->warn('No translations found!');
            return;
        }
        
        $this->table(
            ['Room Type ID', 'Code', 'Locale', 'Name', 'Description'],
            $translations->map(function ($translation) {
                return [
                    $translation->room_type_id,
                    $translation->roomType->code ?? 'N/A',
                    $translation->locale,
                    $translation->name,
                    substr($translation->description, 0, 50) . '...'
                ];
            })
        );
    }
}
