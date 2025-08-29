<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Create Room') }}
            </h2>
            <x-secondary-button onclick="window.location='{{ route('admin.rooms.index') }}'">
                {{ __('Back to List') }}
            </x-secondary-button>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('admin.rooms.store') }}">
                        @csrf

                        <div class="space-y-6">
                            <!-- Room Type -->
                            <div>
                                <x-input-label for="room_type_id" :value="__('Room Type')" />
                                <select id="room_type_id" name="room_type_id" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                    <option value="">{{ __('Select Room Type') }}</option>
                                    @foreach($roomTypes as $roomType)
                                        <option value="{{ $roomType->id }}" {{ old('room_type_id', request('room_type')) == $roomType->id ? 'selected' : '' }}>
                                            {{ $roomType->name }} ({{ $roomType->code }})
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('room_type_id')" class="mt-2" />
                            </div>

                            <!-- Room Number -->
                            <div>
                                <x-input-label for="room_number" :value="__('Room Number')" />
                                <x-text-input id="room_number" class="block mt-1 w-full" type="text" name="room_number" 
                                            :value="old('room_number')" required autofocus />
                                <x-input-error :messages="$errors->get('room_number')" class="mt-2" />
                                <p class="mt-1 text-sm text-gray-600">{{ __('Unique room number (e.g., 101, 201A, etc.)') }}</p>
                            </div>

                            <!-- Floor -->
                            <div>
                                <x-input-label for="floor" :value="__('Floor')" />
                                <x-text-input id="floor" class="block mt-1 w-full" type="number" min="0" name="floor" 
                                            :value="old('floor')" required />
                                <x-input-error :messages="$errors->get('floor')" class="mt-2" />
                            </div>

                            <!-- Smoking -->
                            <div class="flex items-center">
                                <input id="is_smoking" type="checkbox" name="is_smoking" value="1" 
                                       {{ old('is_smoking') ? 'checked' : '' }}
                                       class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                <x-input-label for="is_smoking" :value="__('Smoking Room')" class="ml-2" />
                                <x-input-error :messages="$errors->get('is_smoking')" class="mt-2" />
                            </div>

                            <!-- Status -->
                            <div>
                                <x-input-label for="status" :value="__('Initial Status')" />
                                <select id="status" name="status" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="available" {{ old('status', 'available') == 'available' ? 'selected' : '' }}>{{ __('Available') }}</option>
                                    <option value="closed" {{ old('status') == 'closed' ? 'selected' : '' }}>{{ __('Closed (Maintenance)') }}</option>
                                </select>
                                <x-input-error :messages="$errors->get('status')" class="mt-2" />
                                <p class="mt-1 text-sm text-gray-600">{{ __('New rooms are typically available or under maintenance') }}</p>
                            </div>

                            <!-- Notes -->
                            <div>
                                <x-input-label for="notes" :value="__('Notes (Optional)')" />
                                <textarea id="notes" 
                                        name="notes" 
                                        rows="3" 
                                        class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                        placeholder="{{ __('Any special notes about this room...') }}">{{ old('notes') }}</textarea>
                                <x-input-error :messages="$errors->get('notes')" class="mt-2" />
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="flex items-center justify-end mt-6 space-x-3">
                            <x-secondary-button type="button" onclick="window.location='{{ route('admin.rooms.index') }}'">
                                {{ __('Cancel') }}
                            </x-secondary-button>
                            <x-primary-button>
                                {{ __('Create Room') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Room Type Preview -->
            <div id="room-type-preview" class="mt-6 bg-white overflow-hidden shadow-sm sm:rounded-lg" style="display: none;">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('Room Type Preview') }}</h3>
                    <div id="room-type-details">
                        <!-- Details will be loaded via JavaScript -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const roomTypeSelect = document.getElementById('room_type_id');
            const roomTypePreview = document.getElementById('room-type-preview');
            const roomTypeDetails = document.getElementById('room-type-details');

            // Room type data (you would pass this from the controller)
            const roomTypesData = @json($roomTypesData);

            roomTypeSelect.addEventListener('change', function() {
                const selectedId = this.value;
                
                if (selectedId) {
                    const roomType = roomTypesData.find(rt => rt.id == selectedId);
                    if (roomType) {
                        let amenitiesHtml = '';
                        if (roomType.amenities && Array.isArray(roomType.amenities) && roomType.amenities.length > 0) {
                            amenitiesHtml = '<div class="mt-4">'
                                + '<dt class="text-sm font-medium text-gray-500">' + '{{ __('Amenities') }}' + '</dt>'
                                + '<dd class="mt-1">'
                                + '<div class="flex flex-wrap gap-2">'
                                + roomType.amenities.map(function(amenity) {
                                    return '<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">' + amenity + '</span>';
                                }).join('')
                                + '</div>'
                                + '</dd>'
                                + '</div>';
                        }
                        roomTypeDetails.innerHTML = `
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">{{ __('Base Price') }}</dt>
                                    <dd class="mt-1 text-sm text-gray-900">$${parseFloat(roomType.base_price).toFixed(2)}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">{{ __('Max Occupancy') }}</dt>
                                    <dd class="mt-1 text-sm text-gray-900">${roomType.max_occupancy} {{ __('guests') }}</dd>
                                </div>
                            </div>
                            ${roomType.description ? `
                                <div class="mt-4">
                                    <dt class="text-sm font-medium text-gray-500">{{ __('Description') }}</dt>
                                    <dd class="mt-1 text-sm text-gray-900">${roomType.description}</dd>
                                </div>
                            ` : ''}
                            ${amenitiesHtml}
                        `;
                        roomTypePreview.style.display = 'block';
                    }
                } else {
                    roomTypePreview.style.display = 'none';
                }
            });

            // Trigger change event if room type is pre-selected
            if (roomTypeSelect.value) {
                roomTypeSelect.dispatchEvent(new Event('change'));
            }
        });
    </script>
</x-app-layout>
