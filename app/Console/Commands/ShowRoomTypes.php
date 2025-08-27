<?php

namespace App\Console\Commands;

use App\Models\RoomType;
use Illuminate\Console\Command;

class ShowRoomTypes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:show-room-types';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Display all room types with their pricing';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $roomTypes = RoomType::with('translations')->get();
        
        $this->info('Room Types:');
        $this->table(
            ['ID', 'Code', 'Name (EN)', 'Price', 'Max Occupancy', 'Active'],
            $roomTypes->map(function ($type) {
                return [
                    $type->id,
                    $type->code,
                    $type->name ?? 'N/A',
                    'RM' . number_format($type->base_price, 2),
                    $type->max_occupancy,
                    $type->is_active ? 'Yes' : 'No'
                ];
            })
        );
    }
}
