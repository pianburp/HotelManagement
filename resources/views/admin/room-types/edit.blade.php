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
                                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md hover:border-gray-400 transition-colors duration-300" 
                                         id="image-dropzone">
                                        <div class="space-y-1 text-center">
                                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                            <div class="flex text-sm text-gray-600">
                                                <label for="images" class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                                                    <span>{{ __('Upload files') }}</span>
                                                    <input id="images" name="images[]" type="file" class="sr-only" accept="image/*" multiple>
                                                </label>
                                                <p class="pl-1">{{ __('or drag and drop') }}</p>
                                            </div>
                                            <p class="text-xs text-gray-500">{{ __('PNG, JPG, GIF up to 5MB each') }}</p>
                                        </div>
                                    </div>
                                    <x-input-error :messages="$errors->get('images')" class="mt-2" />
                                    
                                    <!-- Image Preview Container -->
                                    <div id="image-preview" class="mt-4 grid grid-cols-2 gap-4 hidden"></div>
                                </div>

                                <!-- Existing Images -->
                                @if($roomType->getMedia('images')->count() > 0)
                                    <div>
                                        <x-input-label :value="__('Current Images')" />
                                        <div class="mt-2 grid grid-cols-2 gap-4">
                                            @foreach($roomType->getMedia('images') as $image)
                                                <div class="relative group transition-all duration-200">
                                                    <img src="{{ $image->getUrl('thumb') }}" 
                                                         alt="{{ $roomType->name }}" 
                                                         class="w-full h-32 object-cover rounded-lg border border-gray-200 shadow-sm group-hover:shadow-md transition-shadow duration-200">
                                                    
                                                    <!-- Image info overlay -->
                                                    <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black to-transparent p-2 rounded-b-lg opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                                                        <p class="text-white text-xs truncate">{{ $image->name }}</p>
                                                        <p class="text-white text-xs">{{ $image->human_readable_size }}</p>
                                                    </div>
                                                    
                                                    <!-- Remove checkbox -->
                                                    <label class="absolute top-2 right-2 bg-red-500 hover:bg-red-600 text-white p-1.5 rounded-full cursor-pointer transition-colors duration-200 shadow-lg">
                                                        <input type="checkbox" name="remove_images[]" value="{{ $image->id }}" class="sr-only">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                        </svg>
                                                    </label>
                                                    
                                                    <!-- View full size link -->
                                                    <a href="{{ $image->getUrl() }}" target="_blank" 
                                                       class="absolute top-2 left-2 bg-blue-500 hover:bg-blue-600 text-white p-1.5 rounded-full opacity-0 group-hover:opacity-100 transition-all duration-200 shadow-lg">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                        </svg>
                                                    </a>
                                                </div>
                                            @endforeach
                                        </div>
                                        <p class="mt-2 text-sm text-gray-600">
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800 mr-2">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                </svg>
                                                {{ __('Click to remove') }}
                                            </span>
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                </svg>
                                                {{ __('Click to view full size') }}
                                            </span>
                                        </p>
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
            const imageInput = document.getElementById('images');
            const imageDropzone = document.getElementById('image-dropzone');
            const imagePreview = document.getElementById('image-preview');

            // Amenities management
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

            // Image drag and drop functionality
            let dragCounter = 0;

            imageDropzone.addEventListener('dragenter', function(e) {
                e.preventDefault();
                dragCounter++;
                this.classList.add('border-indigo-500', 'bg-indigo-50');
            });

            imageDropzone.addEventListener('dragleave', function(e) {
                e.preventDefault();
                dragCounter--;
                if (dragCounter === 0) {
                    this.classList.remove('border-indigo-500', 'bg-indigo-50');
                }
            });

            imageDropzone.addEventListener('dragover', function(e) {
                e.preventDefault();
            });

            imageDropzone.addEventListener('drop', function(e) {
                e.preventDefault();
                dragCounter = 0;
                this.classList.remove('border-indigo-500', 'bg-indigo-50');
                
                const files = Array.from(e.dataTransfer.files).filter(file => file.type.startsWith('image/'));
                handleImageFiles(files);
            });

            imageInput.addEventListener('change', function(e) {
                const files = Array.from(e.target.files);
                handleImageFiles(files);
            });

            function handleImageFiles(files) {
                if (files.length === 0) {
                    imagePreview.classList.add('hidden');
                    return;
                }

                imagePreview.classList.remove('hidden');
                imagePreview.innerHTML = '';

                files.forEach((file, index) => {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const previewDiv = document.createElement('div');
                        previewDiv.className = 'relative';
                        previewDiv.innerHTML = `
                            <img src="${e.target.result}" alt="Preview" class="w-full h-24 object-cover rounded-lg">
                            <div class="absolute top-2 left-2 bg-black bg-opacity-50 text-white text-xs px-2 py-1 rounded">
                                ${file.name}
                            </div>
                            <div class="absolute top-2 right-2 bg-black bg-opacity-50 text-white text-xs px-2 py-1 rounded">
                                ${(file.size / 1024 / 1024).toFixed(1)}MB
                            </div>
                        `;
                        imagePreview.appendChild(previewDiv);
                    };
                    reader.readAsDataURL(file);
                });

                // Update the file input with the new files
                const dataTransfer = new DataTransfer();
                files.forEach(file => dataTransfer.items.add(file));
                imageInput.files = dataTransfer.files;
            }

            // Handle existing image removal checkbox styling
            document.querySelectorAll('input[name="remove_images[]"]').forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    const imageContainer = this.closest('.relative');
                    const img = imageContainer.querySelector('img');
                    const label = this.closest('label');
                    
                    if (this.checked) {
                        img.style.opacity = '0.5';
                        img.style.filter = 'grayscale(100%)';
                        imageContainer.style.backgroundColor = '#fee2e2';
                        label.classList.add('bg-red-600');
                        label.classList.remove('bg-red-500');
                    } else {
                        img.style.opacity = '1';
                        img.style.filter = 'none';
                        imageContainer.style.backgroundColor = 'transparent';
                        label.classList.add('bg-red-500');
                        label.classList.remove('bg-red-600');
                    }
                });
            });
        });
    </script>
</x-app-layout>
