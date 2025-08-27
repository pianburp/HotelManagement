<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Join Waitlist') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('user.waitlist.store') }}" class="space-y-6">
                        @csrf
                        
                        @if($roomType)
                            <input type="hidden" name="room_type_id" value="{{ $roomType->id }}">
                            <div class="bg-gray-50 rounded-lg p-4 mb-6">
                                <h3 class="font-medium">{{ __('Selected Room Type') }}</h3>
                                <p class="mt-2">{{ $roomType->name }}</p>
                            </div>
                        @else
                            <div>
                                <x-input-label for="room_type_id" :value="__('Room Type')" />
                                <select id="room_type_id" name="room_type_id" required 
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                    <option value="">{{ __('Select Room Type') }}</option>
                                    @foreach($roomTypes as $type)
                                        <option value="{{ $type->id }}" {{ old('room_type_id') == $type->id ? 'selected' : '' }}>
                                            {{ $type->name }} ({{ money($type->base_price) }} / {{ __('night') }})
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('room_type_id')" class="mt-2" />
                            </div>
                        @endif

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Preferred Dates -->
                            <div>
                                <x-input-label for="preferred_check_in" :value="__('Preferred Check In')" />
                                <x-text-input id="preferred_check_in" type="date" name="preferred_check_in" 
                                    :value="old('preferred_check_in', request('check_in'))" 
                                    required class="mt-1 block w-full" />
                                <x-input-error :messages="$errors->get('preferred_check_in')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="preferred_check_out" :value="__('Preferred Check Out')" />
                                <x-text-input id="preferred_check_out" type="date" name="preferred_check_out" 
                                    :value="old('preferred_check_out', request('check_out'))" 
                                    required class="mt-1 block w-full" />
                                <x-input-error :messages="$errors->get('preferred_check_out')" class="mt-2" />
                            </div>

                            <!-- Guest Information -->
                            <div>
                                <x-input-label for="guests" :value="__('Number of Guests')" />
                                <x-text-input id="guests" type="number" name="guests" 
                                    :value="old('guests', request('guests'))" 
                                    required class="mt-1 block w-full" min="1" />
                                <x-input-error :messages="$errors->get('guests')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="flexibility_days" :value="__('Flexible Days (±)')" />
                                <x-text-input id="flexibility_days" type="number" name="flexibility_days" 
                                    :value="old('flexibility_days', 3)" 
                                    required class="mt-1 block w-full" min="0" max="30" />
                                <x-input-error :messages="$errors->get('flexibility_days')" class="mt-2" />
                                <p class="mt-1 text-sm text-gray-500">
                                    {{ __('Number of days flexible before and after preferred dates') }}
                                </p>
                            </div>
                        </div>

                        <!-- Additional Notes -->
                        <div>
                            <x-input-label for="notes" :value="__('Additional Notes')" />
                            <textarea id="notes" name="notes" rows="3" 
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">{{ old('notes') }}</textarea>
                            <x-input-error :messages="$errors->get('notes')" class="mt-2" />
                        </div>

                        <!-- Notification Preferences -->
                        <div>
                            <h3 class="text-lg font-medium mb-4">{{ __('Notification Preferences') }}</h3>
                            <div class="space-y-4">
                                <label class="flex items-center">
                                    <input type="checkbox" name="notify_email" value="1" 
                                        {{ old('notify_email', true) ? 'checked' : '' }}
                                        class="rounded border-gray-300 text-indigo-600 shadow-sm">
                                    <span class="ml-2">{{ __('Email Notifications') }}</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" name="notify_sms" value="1" 
                                        {{ old('notify_sms') ? 'checked' : '' }}
                                        class="rounded border-gray-300 text-indigo-600 shadow-sm">
                                    <span class="ml-2">{{ __('SMS Notifications') }}</span>
                                </label>
                            </div>
                        </div>

                        <div class="flex justify-end">
                            <x-secondary-button type="button" onclick="window.history.back()" class="mr-3">
                                {{ __('Cancel') }}
                            </x-secondary-button>
                            <x-primary-button>
                                {{ __('Join Waitlist') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
