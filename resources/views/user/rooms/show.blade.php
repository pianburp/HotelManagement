<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $room->roomType->name }} - {{ __('Room') }} {{ $room->room_number }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <!-- Room Images -->
                    <div class="mb-8">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @forelse($room->roomType->media as $media)
                                <div class="relative aspect-w-16 aspect-h-9">
                                    <img src="{{ $media->getUrl() }}" 
                                         alt="{{ $room->roomType->name }}"
                                         class="object-cover rounded-lg">
                                </div>
                            @empty
                                <div class="col-span-2 bg-gray-100 rounded-lg p-4 text-center">
                                    {{ __('No images available') }}
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <!-- Room Details -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                        <div class="md:col-span-2">
                            <h3 class="text-2xl font-bold mb-4">{{ __('Room Details') }}</h3>
                            
                            <!-- Room Specific Information -->
                            <div class="grid grid-cols-2 gap-4 mb-6">
                                <div>
                                    <p class="text-gray-600">{{ __('Floor Number') }}</p>
                                    <p class="font-semibold">{{ $room->floor_number }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-600">{{ __('Room Size') }}</p>
                                    <p class="font-semibold">{{ $room->size }} m²</p>
                                </div>
                                <div>
                                    <p class="text-gray-600">{{ __('Room Status') }}</p>
                                    <p class="font-semibold">{{ __(ucfirst($room->status)) }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-600">{{ __('Last Maintenance') }}</p>
                                    <p class="font-semibold">{{ $room->last_maintenance ? $room->last_maintenance->format('Y-m-d') : __('Not available') }}</p>
                                </div>
                            </div>

                            @if($room->notes)
                            <div class="mb-6">
                                <p class="text-gray-600">{{ __('Additional Notes') }}</p>
                                <p class="mt-1">{{ $room->notes }}</p>
                            </div>
                            @endif

                            <div class="prose max-w-none">
                                {!! $room->roomType->description !!}
                            </div>

                            <!-- Amenities -->
                            <div class="mt-8">
                                <h4 class="text-lg font-semibold mb-4">{{ __('Amenities') }}</h4>
                                <div class="grid grid-cols-2 gap-4">
                                    @foreach($room->roomType->amenities as $amenity)
                                        <div class="flex items-center">
                                            <svg class="h-5 w-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                            <span class="ml-2">{{ __($amenity) }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Room Policies -->
                            <div class="mt-8">
                                <h4 class="text-lg font-semibold mb-4">{{ __('Room Policies') }}</h4>
                                <ul class="list-disc list-inside space-y-2 text-gray-600">
                                    <li>{{ __('Check-in time: 2:00 PM') }}</li>
                                    <li>{{ __('Check-out time: 12:00 PM') }}</li>
                                    <li>{{ __('Maximum occupancy:') }} {{ $room->roomType->max_occupancy }} {{ __('guests') }}</li>
                                    <li>{{ $room->smoking_allowed ? __('Smoking allowed') : __('No smoking') }}</li>
                                </ul>
                            </div>
                        </div>

                        <!-- Booking Panel -->
                        <div class="md:col-span-1">
                            <div class="bg-gray-50 rounded-lg p-6 sticky top-6">
                                <div class="text-center mb-6">
                                    <span class="text-3xl font-bold text-gray-900">
                                        {{ money($room->roomType->base_price) }}
                                    </span>
                                    <span class="text-gray-500">/ {{ __('night') }}</span>
                                </div>

                                <form action="{{ route('user.bookings.create') }}" method="GET" class="space-y-4">
                                    <input type="hidden" name="room_id" value="{{ $room->id }}">
                                    
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

                                    <div>
                                        <x-input-label for="guests" :value="__('Number of Guests')" />
                                        <select id="guests" name="guests" required 
                                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                            @for ($i = 1; $i <= $room->roomType->max_occupancy; $i++)
                                                <option value="{{ $i }}" {{ request('guests') == $i ? 'selected' : '' }}>
                                                    {{ $i }} {{ __('Guest(s)') }}
                                                </option>
                                            @endfor
                                        </select>
                                    </div>

                                    <div class="pt-4">
                                        <x-primary-button class="w-full justify-center">
                                            {{ __('Book Now') }}
                                        </x-primary-button>
                                    </div>
                                </form>

                                @if(!$room->isAvailable())
                                    <div class="mt-4 text-center">
                                        <p class="text-gray-600 mb-2">{{ __('Room not available for selected dates?') }}</p>
                                        <x-secondary-button onclick="window.location='{{ route('user.waitlist.create', ['room_type_id' => $room->roomType->id]) }}'">
                                            {{ __('Join Waitlist') }}
                                        </x-secondary-button>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
