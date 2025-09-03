<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Room Type Details') }}: {{ $roomType->name }}
            </h2>
            <div class="flex space-x-2">
                <x-secondary-button onclick="window.location='{{ route('admin.room-types.edit', $roomType) }}'">
                    {{ __('Edit') }}
                </x-secondary-button>
                <x-secondary-button onclick="window.location='{{ route('admin.room-types.index') }}'">
                    {{ __('Back to List') }}
                </x-secondary-button>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Information -->
                <div class="lg:col-span-2">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-6">
                                <h3 class="text-lg font-semibold text-gray-900">{{ __('Basic Information') }}</h3>
                                <span class="px-3 py-1 text-sm rounded-full {{ $roomType->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $roomType->is_active ? __('Active') : __('Inactive') }}
                                </span>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">{{ __('Room Type Code') }}</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $roomType->code }}</dd>
                                </div>

                                <div>
                                    <dt class="text-sm font-medium text-gray-500">{{ __('Base Price') }}</dt>
                                    <dd class="mt-1 text-lg font-semibold text-gray-900">{{ money($roomType->base_price) }}</dd>
                                </div>

                                <div>
                                    <dt class="text-sm font-medium text-gray-500">{{ __('Maximum Occupancy') }}</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $roomType->max_occupancy }} {{ __('guests') }}</dd>
                                </div>

                                <div>
                                    <dt class="text-sm font-medium text-gray-500">{{ __('Total Rooms') }}</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $roomType->rooms_count }}</dd>
                                </div>

                                <div>
                                    <dt class="text-sm font-medium text-gray-500">{{ __('Room Size') }}</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $roomType->size ?? __('Not specified') }}</dd>
                                </div>

                                <div>
                                    <dt class="text-sm font-medium text-gray-500">{{ __('Created') }}</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $roomType->created_at->format('M d, Y') }}</dd>
                                </div>
                            </div>

                            <!-- Description -->
                            @if($roomType->description)
                                <div class="mt-6">
                                    <dt class="text-sm font-medium text-gray-500 mb-2">{{ __('Description') }}</dt>
                                    <dd class="text-sm text-gray-900 leading-relaxed">{{ $roomType->description }}</dd>
                                </div>
                            @endif

                            <!-- Amenities -->
                            @if($roomType->amenities && count($roomType->amenities) > 0)
                                <div class="mt-6">
                                    <dt class="text-sm font-medium text-gray-500 mb-3">{{ __('Amenities') }}</dt>
                                    <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
                                        @foreach($roomType->amenities as $amenity)
                                            <div class="flex items-center text-sm text-gray-700">
                                                <svg class="w-4 h-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                </svg>
                                                {{ $amenity }}
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <!-- Images Gallery -->
                            @if($roomType->getMedia('images')->count() > 0)
                                <div class="mt-6">
                                    <dt class="text-sm font-medium text-gray-500 mb-3">{{ __('Images') }}</dt>
                                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                                        @foreach($roomType->getMedia('images') as $image)
                                            <div class="aspect-w-4 aspect-h-3">
                                                <img src="{{ $image->getUrl() }}" 
                                                     alt="{{ $roomType->name }}" 
                                                     class="object-cover rounded-lg cursor-pointer hover:opacity-90 transition-opacity"
                                                     onclick="openImageModal('{{ $image->getUrl() }}')">
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Room Statistics -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('Room Statistics') }}</h3>
                            
                            <div class="space-y-4">
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">{{ __('Total Rooms') }}</span>
                                    <span class="text-sm font-medium">{{ $roomType->rooms_count }}</span>
                                </div>
                                
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">{{ __('Available') }}</span>
                                    <span class="text-sm font-medium text-green-600">{{ $roomType->available_rooms_count }}</span>
                                </div>
                                
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">{{ __('Occupied') }}</span>
                                    <span class="text-sm font-medium text-blue-600">{{ $roomType->occupied_rooms_count }}</span>
                                </div>
                                
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">{{ __('Maintenance') }}</span>
                                    <span class="text-sm font-medium text-yellow-600">{{ $roomType->maintenance_rooms_count }}</span>
                                </div>
                            </div>
                            
                            @if($roomType->rooms_count > 0)
                                <div class="mt-4 pt-4 border-t">
                                    <div class="text-sm text-gray-600 mb-2">{{ __('Occupancy Rate') }}</div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="bg-blue-600 h-2 rounded-full" style="width: {{ ($roomType->occupied_rooms_count / $roomType->rooms_count) * 100 }}%"></div>
                                    </div>
                                    <div class="text-xs text-gray-500 mt-1">
                                        {{ number_format(($roomType->occupied_rooms_count / $roomType->rooms_count) * 100, 1) }}%
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('Quick Actions') }}</h3>
                            
                            <div class="space-y-3">
                                <x-secondary-button onclick="window.location='{{ route('admin.rooms.create', ['room_type' => $roomType->id]) }}'" class="w-full">
                                    {{ __('Add Room') }}
                                </x-secondary-button>
                                
                                <x-secondary-button onclick="window.location='{{ route('admin.rooms.index', ['room_type' => $roomType->id]) }}'" class="w-full">
                                    {{ __('View Rooms') }}
                                </x-secondary-button>
                                
                                <x-secondary-button onclick="window.location='{{ route('admin.bookings.index', ['room_type' => $roomType->id]) }}'" class="w-full">
                                    {{ __('View Bookings') }}
                                </x-secondary-button>
                            </div>
                        </div>
                    </div>

                    <!-- Waitlist -->
                    @if($roomType->waitlist_count > 0)
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('Waitlist') }}</h3>
                                
                                <div class="text-center">
                                    <div class="text-2xl font-bold text-orange-600">{{ $roomType->waitlist_count }}</div>
                                    <div class="text-sm text-gray-600">{{ __('Active Requests') }}</div>
                                </div>
                                
                                <x-secondary-button onclick="window.location='{{ route('admin.waitlist.index', ['room_type' => $roomType->id]) }}'" class="w-full mt-3">
                                    {{ __('View Waitlist') }}
                                </x-secondary-button>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Image Modal -->
    <div id="imageModal" class="fixed inset-0 bg-black bg-opacity-75 hidden z-50 flex items-center justify-center" onclick="closeImageModal()">
        <div class="max-w-4xl max-h-screen p-4">
            <img id="modalImage" src="" alt="" class="max-w-full max-h-full object-contain">
        </div>
    </div>

    <script>
        function openImageModal(imageSrc) {
            document.getElementById('modalImage').src = imageSrc;
            document.getElementById('imageModal').classList.remove('hidden');
        }

        function closeImageModal() {
            document.getElementById('imageModal').classList.add('hidden');
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeImageModal();
            }
        });
    </script>
</x-app-layout>
