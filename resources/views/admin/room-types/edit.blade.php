<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Edit Room Type') }}: {{ $roomType->name }}
            </h2>
            <div class="flex space-x-2">
                <x-secondary-button onclick="window.location='{{ route('admin.room-types.show', $roomType) }}'">
                    {{ __('View Details') }}
                </x-secondary-button>
                <x-secondary-button onclick="window.location='{{ route('admin.room-types.index') }}'">
                    {{ __('Back to List') }}
                </x-secondary-button>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('admin.room-types.update', $roomType) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Left Column -->
                            <div class="space-y-6">
                                <!-- Room Type Code -->
                                <div>
                                    <x-input-label for="code" :value="__('Room Type Code')" />
                                    <x-text-input id="code" class="block mt-1 w-full" type="text" name="code" 
                                                :value="old('code', $roomType->code)" required autofocus />
                                    <x-input-error :messages="$errors->get('code')" class="mt-2" />
                                    <p class="mt-1 text-sm text-gray-600">{{ __('Unique identifier for this room type (e.g., STD, DLX, STE)') }}</p>
                                </div>

                                <!-- Base Price -->
                                <div>
                                    <x-input-label for="base_price" :value="__('Base Price')" />
                                    <x-text-input id="base_price" class="block mt-1 w-full" type="number" step="0.01" 
                                                name="base_price" :value="old('base_price', $roomType->base_price)" required />
                                    <x-input-error :messages="$errors->get('base_price')" class="mt-2" />
                                </div>

                                <!-- Maximum Occupancy -->
                                <div>
                                    <x-input-label for="max_occupancy" :value="__('Maximum Occupancy')" />
                                    <x-text-input id="max_occupancy" class="block mt-1 w-full" type="number" min="1" 
                                                name="max_occupancy" :value="old('max_occupancy', $roomType->max_occupancy)" required />
                                    <x-input-error :messages="$errors->get('max_occupancy')" class="mt-2" />
                                </div>

                                <!-- Status -->
                                <div>
                                    <x-input-label for="is_active" :value="__('Status')" />
                                    <select id="is_active" name="is_active" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                        <option value="1" {{ old('is_active', $roomType->is_active) == '1' ? 'selected' : '' }}>{{ __('Active') }}</option>
                                        <option value="0" {{ old('is_active', $roomType->is_active) == '0' ? 'selected' : '' }}>{{ __('Inactive') }}</option>
                                    </select>
                                    <x-input-error :messages="$errors->get('is_active')" class="mt-2" />
                                </div>

                                <!-- Images -->
                                <div>
                                    <x-input-label for="images" :value="__('Add New Images')" />
                                    <input id="images" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" 
                                           type="file" name="images[]" accept="image/*" multiple />
                                    <x-input-error :messages="$errors->get('images')" class="mt-2" />
                                    <p class="mt-1 text-sm text-gray-600">{{ __('Select new images to add (existing images will be preserved)') }}</p>
                                </div>

                                <!-- Existing Images -->
                                @if($roomType->getMedia('images')->count() > 0)
                                    <div>
                                        <x-input-label :value="__('Current Images')" />
                                        <div class="mt-2 grid grid-cols-2 gap-4">
                                            @foreach($roomType->getMedia('images') as $image)
                                                <div class="relative">
                                                    <img src="{{ $image->getUrl('thumb') }}" 
                                                         alt="{{ $roomType->name }}" 
                                                         class="w-full h-24 object-cover rounded-lg">
                                                    <label class="absolute top-2 right-2 bg-red-500 text-white p-1 rounded cursor-pointer hover:bg-red-600">
                                                        <input type="checkbox" name="remove_images[]" value="{{ $image->id }}" class="sr-only">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                        </svg>
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                        <p class="mt-1 text-sm text-gray-600">{{ __('Check the X to remove images') }}</p>
                                    </div>
                                @endif
                            </div>

                            <!-- Right Column -->
                            <div class="space-y-6">
                                <!-- Amenities -->
                                <div>
                                    <x-input-label for="amenities" :value="__('Amenities')" />
                                    <div class="mt-2 space-y-2" id="amenities-container">
                                        @if(old('amenities', $roomType->amenities))
                                            @foreach(old('amenities', $roomType->amenities) as $index => $amenity)
                                                <div class="flex items-center space-x-2 amenity-item">
                                                    <x-text-input class="flex-1" type="text" name="amenities[]" value="{{ $amenity }}" placeholder="{{ __('Enter amenity') }}" />
                                                    <button type="button" class="text-red-600 hover:text-red-800 remove-amenity">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                        </svg>
                                                    </button>
                                                </div>
                                            @endforeach
                                        @else
                                            <div class="flex items-center space-x-2 amenity-item">
                                                <x-text-input class="flex-1" type="text" name="amenities[]" placeholder="{{ __('Enter amenity') }}" />
                                                <button type="button" class="text-red-600 hover:text-red-800 remove-amenity">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                    </svg>
                                                </button>
                                            </div>
                                        @endif
                                    </div>
                                    <button type="button" id="add-amenity" class="mt-2 text-indigo-600 hover:text-indigo-800 text-sm">
                                        {{ __('+ Add Another Amenity') }}
                                    </button>
                                    <x-input-error :messages="$errors->get('amenities')" class="mt-2" />
                                </div>
                            </div>
                        </div>

                        <!-- Translations Section -->
                        <div class="mt-8 border-t pt-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Translations') }}</h3>
                            
                            @foreach(config('app.available_locales', ['en']) as $locale)
                                @php
                                    $translation = $roomType->translations->where('locale', $locale)->first();
                                @endphp
                                <div class="mb-6 p-4 border border-gray-200 rounded-lg">
                                    <h4 class="font-medium text-gray-700 mb-3">
                                        {{ __(':language Translation', ['language' => strtoupper($locale)]) }}
                                    </h4>
                                    
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <!-- Name -->
                                        <div>
                                            <x-input-label for="translations_{{ $locale }}_name" :value="__('Name')" />
                                            <x-text-input id="translations_{{ $locale }}_name" class="block mt-1 w-full" 
                                                        type="text" name="translations[{{ $locale }}][name]" 
                                                        :value="old('translations.'.$locale.'.name', $translation?->name)" />
                                            <x-input-error :messages="$errors->get('translations.'.$locale.'.name')" class="mt-2" />
                                        </div>

                                        <!-- Size -->
                                        <div>
                                            <x-input-label for="translations_{{ $locale }}_size" :value="__('Size')" />
                                            <x-text-input id="translations_{{ $locale }}_size" class="block mt-1 w-full" 
                                                        type="text" name="translations[{{ $locale }}][size]" 
                                                        :value="old('translations.'.$locale.'.size', $translation?->size)" 
                                                        placeholder="{{ __('e.g., 25 sqm') }}" />
                                            <x-input-error :messages="$errors->get('translations.'.$locale.'.size')" class="mt-2" />
                                        </div>
                                    </div>

                                    <!-- Description -->
                                    <div class="mt-4">
                                        <x-input-label for="translations_{{ $locale }}_description" :value="__('Description')" />
                                        <textarea id="translations_{{ $locale }}_description" 
                                                name="translations[{{ $locale }}][description]" 
                                                rows="4" 
                                                class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('translations.'.$locale.'.description', $translation?->description) }}</textarea>
                                        <x-input-error :messages="$errors->get('translations.'.$locale.'.description')" class="mt-2" />
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Submit Button -->
                        <div class="flex items-center justify-end mt-6">
                            <x-secondary-button type="button" onclick="window.location='{{ route('admin.room-types.show', $roomType) }}'" class="mr-3">
                                {{ __('Cancel') }}
                            </x-secondary-button>
                            <x-primary-button>
                                {{ __('Update Room Type') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const amenitiesContainer = document.getElementById('amenities-container');
            const addAmenityBtn = document.getElementById('add-amenity');

            addAmenityBtn.addEventListener('click', function() {
                const newAmenityDiv = document.createElement('div');
                newAmenityDiv.className = 'flex items-center space-x-2 amenity-item';
                newAmenityDiv.innerHTML = `
                    <input class="flex-1 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" 
                           type="text" name="amenities[]" placeholder="{{ __('Enter amenity') }}" />
                    <button type="button" class="text-red-600 hover:text-red-800 remove-amenity">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                `;
                amenitiesContainer.appendChild(newAmenityDiv);
            });

            amenitiesContainer.addEventListener('click', function(e) {
                if (e.target.closest('.remove-amenity')) {
                    const amenityItem = e.target.closest('.amenity-item');
                    if (amenitiesContainer.children.length > 1) {
                        amenityItem.remove();
                    }
                }
            });

            // Handle image removal checkbox styling
            document.querySelectorAll('input[name="remove_images[]"]').forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    const imageContainer = this.closest('.relative');
                    const img = imageContainer.querySelector('img');
                    if (this.checked) {
                        img.style.opacity = '0.5';
                        imageContainer.style.backgroundColor = '#fee2e2';
                    } else {
                        img.style.opacity = '1';
                        imageContainer.style.backgroundColor = 'transparent';
                    }
                });
            });
        });
    </script>
</x-app-layout>
