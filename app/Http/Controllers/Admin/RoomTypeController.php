<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RoomType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class RoomTypeController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $roomTypes = RoomType::with(['media', 'translations'])
            ->withCount(['rooms'])
            ->paginate(10);

        return view('admin.room-types.index', compact('roomTypes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.room-types.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'code' => 'required|string|max:20|unique:room_types',
            'base_price' => 'required|numeric|min:0',
            'max_occupancy' => 'required|integer|min:1',
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'size' => 'nullable|string|max:100',
            'amenities_description' => 'nullable|string',
            'amenities' => 'nullable|array',
            'amenities.*' => 'nullable|string|max:255',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120', // 5MB max
        ]);

        DB::beginTransaction();
        try {
            $roomType = RoomType::create([
                'code' => $request->code,
                'base_price' => $request->base_price,
                'max_occupancy' => $request->max_occupancy,
                'amenities' => array_filter($request->amenities ?? []), // Remove empty values
                'is_active' => true,
            ]);
            
            // Create translation
            $roomType->translations()->create([
                'locale' => 'en',
                'name' => $request->name,
                'description' => $request->description,
                'size' => $request->size,
                'amenities_description' => $request->amenities_description,
            ]);

            // Handle image uploads
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $roomType->addMedia($image)
                            ->toMediaCollection('images');
                }
            }

            DB::commit();

            return redirect()->route('admin.room-types.index')
                           ->with('success', 'Room type created successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Error creating room type: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(RoomType $roomType)
    {
        $roomType->load(['media', 'rooms.bookings']);
        
        // Get room statistics
        $roomType->rooms_count = $roomType->rooms()->count();
        $roomType->available_rooms_count = $roomType->rooms()->where('status', 'available')->count();
        $roomType->occupied_rooms_count = $roomType->rooms()->where('status', 'onboard')->count();
        $roomType->maintenance_rooms_count = $roomType->rooms()->where('status', 'closed')->count();
        $roomType->waitlist_count = $roomType->waitlist()->where('status', 'pending')->count();

        return view('admin.room-types.show', compact('roomType'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(RoomType $roomType)
    {
        // Load relationships using fresh query to ensure they're loaded properly
        $roomType = RoomType::with(['media', 'translations'])->findOrFail($roomType->id);
        
        // Ensure there's at least an English translation
        $englishTranslation = $roomType->translations()->where('locale', 'en')->first();
        if (!$englishTranslation) {
            // Create a default translation if none exists
            $roomType->translations()->create([
                'locale' => 'en',
                'name' => $roomType->code,
                'description' => 'Room type ' . $roomType->code,
                'size' => null,
                'amenities_description' => null,
            ]);
            // Reload to include the new translation
            $roomType = RoomType::with(['media', 'translations'])->findOrFail($roomType->id);
        }
        
        // Debug: Check if translations are loaded properly
        if (config('app.debug')) {
            \Log::info('Room Type Edit Debug', [
                'room_type_id' => $roomType->id,
                'translations_count' => $roomType->translations->count(),
                'english_translation' => $roomType->translations->where('locale', 'en')->first()?->toArray(),
                'getName_result' => $roomType->getName(),
                'getTranslatedField_name' => $roomType->getTranslatedField('name'),
            ]);
        }
        
        return view('admin.room-types.edit', compact('roomType'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, RoomType $roomType)
    {
        $this->validate($request, [
            'code' => 'required|string|max:20|unique:room_types,code,' . $roomType->id,
            'base_price' => 'required|numeric|min:0',
            'max_occupancy' => 'required|integer|min:1',
            'is_active' => 'required|boolean',
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'size' => 'nullable|string|max:100',
            'amenities_description' => 'nullable|string',
            'amenities' => 'nullable|array',
            'amenities.*' => 'nullable|string|max:255',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120', // 5MB max
            'remove_images' => 'nullable|array',
            'remove_images.*' => 'integer|exists:media,id',
        ]);

        DB::beginTransaction();
        try {
            $roomType->update([
                'code' => $request->code,
                'base_price' => $request->base_price,
                'max_occupancy' => $request->max_occupancy,
                'amenities' => array_filter($request->amenities ?? []), // Remove empty values
                'is_active' => (bool)$request->is_active,
            ]);
            
            // Update translation
            $translation = $roomType->translations()->where('locale', 'en')->first();
            if ($translation) {
                $translation->update([
                    'name' => $request->name,
                    'description' => $request->description,
                    'size' => $request->size,
                    'amenities_description' => $request->amenities_description,
                ]);
            } else {
                $roomType->translations()->create([
                    'locale' => 'en',
                    'name' => $request->name,
                    'description' => $request->description,
                    'size' => $request->size,
                    'amenities_description' => $request->amenities_description,
                ]);
            }

            // Remove selected images
            if ($request->has('remove_images') && is_array($request->remove_images)) {
                $mediaToRemove = Media::whereIn('id', $request->remove_images)
                     ->where('model_type', RoomType::class)
                     ->where('model_id', $roomType->id)
                     ->where('collection_name', 'images')
                     ->get();
                
                foreach ($mediaToRemove as $media) {
                    $media->delete();
                }
            }

            // Add new images
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $roomType->addMedia($image)
                            ->toMediaCollection('images');
                }
            }

            DB::commit();

            return redirect()->route('admin.room-types.index')
                           ->with('success', 'Room type updated successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Error updating room type: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(RoomType $roomType)
    {
        if ($roomType->rooms()->exists()) {
            return back()->with('error', 'Cannot delete room type with associated rooms.');
        }

        try {
            $roomType->delete();
            Cache::tags(['room_types'])->flush();
            
            return redirect()->route('admin.room-types.index')
                           ->with('success', 'Room type deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error deleting room type: ' . $e->getMessage());
        }
    }

    /**
     * Toggle the active status of the room type.
     */
    public function toggleStatus(RoomType $roomType)
    {
        $roomType->update(['is_active' => !$roomType->is_active]);
        Cache::tags(['room_types'])->flush();
        
        return back()->with('success', 'Room type status updated successfully.');
    }
}
