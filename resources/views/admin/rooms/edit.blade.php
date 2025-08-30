<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Edit Room') }} - {{ $room->room_number }}
            </h2>
            <x-secondary-button onclick="window.location='{{ route('admin.rooms.index') }}'">
                {{ __('Back to Rooms') }}
            </x-secondary-button>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if(session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('admin.rooms.update', $room) }}" class="space-y-6">
                        @csrf
                        @method('PATCH')

                        <!-- Basic Information -->
                        <div class="border-b border-gray-200 pb-6">
                            <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">{{ __('Basic Information') }}</h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <x-input-label for="room_number" :value="__('Room Number')" />
                                    <x-text-input id="room_number" class="block mt-1 w-full" type="text" name="room_number" 
                                                :value="old('room_number', $room->room_number)" required autofocus />
                                    <x-input-error :messages="$errors->get('room_number')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="room_type_id" :value="__('Room Type')" />
                                    <select id="room_type_id" name="room_type_id" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                        <option value="">{{ __('Select Room Type') }}</option>
                                        @foreach($roomTypes as $roomType)
                                            <option value="{{ $roomType->id }}" 
                                                {{ old('room_type_id', $room->room_type_id) == $roomType->id ? 'selected' : '' }}>
                                                {{ $roomType->name }} ({{ $roomType->code }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <x-input-error :messages="$errors->get('room_type_id')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="floor_number" :value="__('Floor Number')" />
                                    <x-text-input id="floor_number" class="block mt-1 w-full" type="number" name="floor_number" 
                                                :value="old('floor_number', $room->floor_number)" min="1" required />
                                    <x-input-error :messages="$errors->get('floor_number')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="size" :value="__('Room Size (m²)')" />
                                    <x-text-input id="size" class="block mt-1 w-full" type="number" name="size" 
                                                :value="old('size', $room->size)" step="0.01" min="0" />
                                    <x-input-error :messages="$errors->get('size')" class="mt-2" />
                                </div>
                            </div>
                        </div>

                        <!-- Features -->
                        <div class="border-b border-gray-200 pb-6">
                            <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">{{ __('Features') }}</h3>
                            
                            <div class="space-y-4">
                                <div class="flex items-center">
                                    <input id="smoking_allowed" name="smoking_allowed" type="checkbox" value="1"
                                        {{ old('smoking_allowed', $room->smoking_allowed) ? 'checked' : '' }}
                                        class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                    <label for="smoking_allowed" class="ml-2 block text-sm text-gray-900">
                                        {{ __('Smoking Allowed') }}
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Maintenance -->
                        <div class="border-b border-gray-200 pb-6">
                            <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">{{ __('Maintenance') }}</h3>
                            
                            <div>
                                <x-input-label for="last_maintenance" :value="__('Last Maintenance Date')" />
                                <x-text-input id="last_maintenance" class="block mt-1 w-full" type="date" name="last_maintenance" 
                                            :value="old('last_maintenance', $room->last_maintenance?->format('Y-m-d'))" />
                                <x-input-error :messages="$errors->get('last_maintenance')" class="mt-2" />
                            </div>
                        </div>

                        <!-- Status Change -->
                        <div class="border-b border-gray-200 pb-6">
                            <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">{{ __('Status Management') }}</h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <x-input-label for="status" :value="__('Room Status')" />
                                    <select id="status" name="status" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                        <option value="available" {{ old('status', $room->status) === 'available' ? 'selected' : '' }}>{{ __('Available') }}</option>
                                        <option value="reserved" {{ old('status', $room->status) === 'reserved' ? 'selected' : '' }}>{{ __('Reserved') }}</option>
                                        <option value="onboard" {{ old('status', $room->status) === 'onboard' ? 'selected' : '' }}>{{ __('Onboard') }}</option>
                                        <option value="closed" {{ old('status', $room->status) === 'closed' ? 'selected' : '' }}>{{ __('Closed') }}</option>
                                    </select>
                                    <x-input-error :messages="$errors->get('status')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="status_change_reason" :value="__('Reason for Status Change (if changed)')" />
                                    <x-text-input id="status_change_reason" class="block mt-1 w-full" type="text" name="status_change_reason" 
                                                :value="old('status_change_reason')" 
                                                placeholder="{{ __('Enter reason if changing status') }}" />
                                    <x-input-error :messages="$errors->get('status_change_reason')" class="mt-2" />
                                </div>
                            </div>
                        </div>

                        <!-- Notes -->
                        <div>
                            <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">{{ __('Additional Information') }}</h3>
                            
                            <div>
                                <x-input-label for="notes" :value="__('Notes')" />
                                <textarea id="notes" name="notes" rows="4" 
                                        class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                        placeholder="{{ __('Any additional notes about this room...') }}">{{ old('notes', $room->notes) }}</textarea>
                                <x-input-error :messages="$errors->get('notes')" class="mt-2" />
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="flex items-center justify-end space-x-4 pt-6">
                            <x-secondary-button type="button" onclick="window.location='{{ route('admin.rooms.show', $room) }}'">
                                {{ __('Cancel') }}
                            </x-secondary-button>
                            <x-primary-button type="submit">
                                {{ __('Update Room') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript for enhanced functionality -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const statusSelect = document.getElementById('status');
            const reasonInput = document.getElementById('status_change_reason');
            const originalStatus = '{{ $room->status }}';

            statusSelect.addEventListener('change', function() {
                if (this.value !== originalStatus) {
                    reasonInput.required = true;
                    reasonInput.parentElement.querySelector('label').innerHTML = '{{ __("Reason for Status Change") }} <span class="text-red-500">*</span>';
                } else {
                    reasonInput.required = false;
                    reasonInput.parentElement.querySelector('label').innerHTML = '{{ __("Reason for Status Change (if changed)") }}';
                }
            });
        });
    </script>
</x-app-layout>
