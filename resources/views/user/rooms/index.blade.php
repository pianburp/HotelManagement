<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Available Rooms') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Search Filters -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form action="{{ route('user.rooms.search') }}" method="GET" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <!-- Date Range -->
                            <div>
                                <x-input-label for="check_in" :value="__('Check In')" />
                                <x-text-input id="check_in" type="date" name="check_in" 
                                    :value="request('check_in')" required class="mt-1 block w-full" />
                                <x-input-error :messages="$errors->get('check_in')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="check_out" :value="__('Check Out')" />
                                <x-text-input id="check_out" type="date" name="check_out" 
                                    :value="request('check_out')" required class="mt-1 block w-full" />
                                <x-input-error :messages="$errors->get('check_out')" class="mt-2" />
                            </div>
                            <!-- Occupancy -->
                            <div>
                                <x-input-label for="occupancy" :value="__('Guests')" />
                                <select id="occupancy" name="occupancy" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                    @for ($i = 1; $i <= 6; $i++)
                                        <option value="{{ $i }}" {{ request('occupancy') == $i ? 'selected' : '' }}>
                                            {{ $i }} {{ __('Guest(s)') }}
                                        </option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <!-- Price Range -->
                            <div>
                                <x-input-label for="price_min" :value="__('Min Price')" />
                                <x-text-input id="price_min" type="number" name="price_min" 
                                    :value="request('price_min')" class="mt-1 block w-full" />
                            </div>
                            <div>
                                <x-input-label for="price_max" :value="__('Max Price')" />
                                <x-text-input id="price_max" type="number" name="price_max" 
                                    :value="request('price_max')" class="mt-1 block w-full" />
                            </div>
                            <!-- Room Type -->
                            <div>
                                <x-input-label for="room_type" :value="__('Room Type')" />
                                <select id="room_type" name="room_type" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                    <option value="">{{ __('All Types') }}</option>
                                    @foreach($roomTypes as $type)
                                        <option value="{{ $type->id }}" {{ request('room_type') == $type->id ? 'selected' : '' }}>
                                            {{ $type->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Amenities -->
                        <div class="border-t pt-4">
                            <h3 class="text-lg font-medium mb-2">{{ __('Amenities') }}</h3>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                @foreach($amenities as $amenity)
                                    <label class="inline-flex items-center">
                                        <input type="checkbox" name="amenities[]" value="{{ $amenity }}"
                                            {{ in_array($amenity, request('amenities', [])) ? 'checked' : '' }}
                                            class="rounded border-gray-300 text-indigo-600 shadow-sm">
                                        <span class="ml-2">{{ __($amenity) }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div class="flex justify-end">
                            <x-primary-button>
                                {{ __('Search Rooms') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Results -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse ($rooms as $room)
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="relative">
                            @if($room->roomType->getFirstMediaUrl('images'))
                                <img src="{{ $room->roomType->getFirstMediaUrl('images') }}" 
                                     alt="{{ $room->roomType->name }}"
                                     class="w-full h-48 object-cover">
                            @else
                                <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
                                    <span class="text-gray-400">{{ __('No image available') }}</span>
                                </div>
                            @endif
                            <div class="absolute top-0 right-0 p-2">
                                @php
                                    $statusColors = [
                                        'available' => 'bg-green-500',
                                        'reserved' => 'bg-blue-500',
                                        'occupied' => 'bg-red-500',
                                        'maintenance' => 'bg-yellow-500',
                                    ];
                                    $statusColor = $statusColors[$room->status] ?? 'bg-gray-500';
                                @endphp
                                <span class="{{ $statusColor }} text-white px-2 py-1 rounded text-sm">
                                    {{ __(ucfirst($room->status)) }}
                                </span>
                            </div>
                        </div>
                        <div class="p-6">
                            <h3 class="text-xl font-semibold mb-2">{{ $room->roomType->name }} - {{ __('Room') }} {{ $room->room_number }}</h3>
                            
                            <!-- Room Details -->
                            <div class="grid grid-cols-2 gap-2 mb-4 text-sm">
                                <div>
                                    <span class="text-gray-600">{{ __('Floor') }}:</span>
                                    <span class="ml-1 font-medium">{{ $room->floor_number }}</span>
                                </div>
                                <div>
                                    <span class="text-gray-600">{{ __('Size') }}:</span>
                                    <span class="ml-1 font-medium">{{ $room->size }} m²</span>
                                </div>
                                <div>
                                    <span class="text-gray-600">{{ __('Status') }}:</span>
                                    <span class="ml-1 font-medium">{{ __(ucfirst($room->status)) }}</span>
                                </div>
                                <div>
                                    <span class="text-gray-600">{{ __('Type') }}:</span>
                                    <span class="ml-1 font-medium">{{ $room->smoking_allowed ? __('Smoking') : __('Non-Smoking') }}</span>
                                </div>
                            </div>

                            <p class="text-gray-600 mb-4 line-clamp-2">{{ Str::limit($room->roomType->description, 100) }}</p>
                            <div class="flex justify-between items-center mb-4">
                                <span class="text-lg font-bold text-gray-900">
                                    {{ money($room->roomType->base_price) }} / {{ __('night') }}
                                </span>
                                <span class="text-sm text-gray-500">
                                    {{ __('Max') }} {{ $room->roomType->max_occupancy }} {{ __('guests') }}
                                </span>
                            </div>
                            <div class="flex justify-between items-center">
                                <a href="{{ route('user.rooms.show', $room) }}" 
                                   class="text-indigo-600 hover:text-indigo-900">
                                    {{ __('View Details') }}
                                </a>
                                <x-primary-button onclick="window.location='{{ route('user.bookings.create', ['room' => $room->id]) }}'">
                                    {{ __('Book Now') }}
                                </x-primary-button>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <p class="text-center text-gray-500">
                            {{ __('No rooms available for your search criteria.') }}
                        </p>
                        @if(request()->hasAny(['check_in', 'check_out', 'occupancy', 'room_type']))
                            <div class="mt-4 text-center">
                                <p class="mb-4">{{ __('Would you like to join our waitlist?') }}</p>
                                <x-secondary-button onclick="window.location='{{ route('user.waitlist.create', request()->all()) }}'">
                                    {{ __('Join Waitlist') }}
                                </x-secondary-button>
                            </div>
                        @endif
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if ($rooms->hasPages())
                <div class="mt-6">
                    {{ $rooms->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
