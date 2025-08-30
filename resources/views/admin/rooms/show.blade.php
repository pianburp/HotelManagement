<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Room Details') }} - {{ $room->room_number }}
            </h2>
            <div class="flex space-x-2">
                <x-secondary-button onclick="window.location='{{ route('admin.rooms.edit', $room) }}'">
                    {{ __('Edit Room') }}
                </x-secondary-button>
                <x-secondary-button onclick="window.location='{{ route('admin.rooms.index') }}'">
                    {{ __('Back to Rooms') }}
                </x-secondary-button>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Room Information -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('Room Information') }}</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700">{{ __('Room Number') }}</label>
                                <p class="text-lg font-semibold text-gray-900">{{ $room->room_number }}</p>
                            </div>
                            
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700">{{ __('Room Type') }}</label>
                                <p class="text-gray-900">{{ $room->roomType->name }} ({{ $room->roomType->code }})</p>
                            </div>
                            
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700">{{ __('Floor') }}</label>
                                <p class="text-gray-900">{{ $room->floor_number }}</p>
                            </div>
                            
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700">{{ __('Status') }}</label>
                                <span class="px-3 py-1 text-sm font-semibold rounded-full 
                                    @if($room->status === 'available') bg-green-100 text-green-800
                                    @elseif($room->status === 'reserved') bg-yellow-100 text-yellow-800
                                    @elseif($room->status === 'onboard') bg-blue-100 text-blue-800
                                    @else bg-red-100 text-red-800
                                    @endif">
                                    {{ ucfirst($room->status) }}
                                </span>
                            </div>
                        </div>
                        
                        <div>
                            @if($room->size)
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700">{{ __('Size') }}</label>
                                <p class="text-gray-900">{{ $room->size }} m²</p>
                            </div>
                            @endif
                            
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700">{{ __('Smoking Allowed') }}</label>
                                <p class="text-gray-900">
                                    @if($room->smoking_allowed)
                                        <span class="text-green-600">{{ __('Yes') }}</span>
                                    @else
                                        <span class="text-red-600">{{ __('No') }}</span>
                                    @endif
                                </p>
                            </div>
                            
                            @if($room->last_maintenance)
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700">{{ __('Last Maintenance') }}</label>
                                <p class="text-gray-900">{{ $room->last_maintenance->format('M d, Y') }}</p>
                            </div>
                            @endif
                            
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700">{{ __('Last Updated') }}</label>
                                <p class="text-gray-900">{{ $room->updated_at->format('M d, Y H:i') }}</p>
                            </div>
                        </div>
                    </div>
                    
                    @if($room->notes)
                    <div class="mt-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Notes') }}</label>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <p class="text-gray-900">{{ $room->notes }}</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Room Type Details -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('Room Type Details') }}</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700">{{ __('Base Price') }}</label>
                                <p class="text-lg font-semibold text-gray-900">RM {{ number_format($room->roomType->base_price, 2) }}</p>
                            </div>
                            
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700">{{ __('Max Occupancy') }}</label>
                                <p class="text-gray-900">{{ $room->roomType->max_occupancy }} {{ __('guests') }}</p>
                            </div>
                        </div>
                        
                        <div>
                            @if($room->roomType->description)
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700">{{ __('Description') }}</label>
                                <p class="text-gray-900">{{ $room->roomType->description }}</p>
                            </div>
                            @endif
                            
                            @if($room->roomType->amenities)
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700">{{ __('Amenities') }}</label>
                                <div class="text-gray-900">
                                    @foreach($room->roomType->amenities as $amenity)
                                        <span class="inline-block bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full mr-2 mb-1">
                                            {{ $amenity }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Status Change Form -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('Change Room Status') }}</h3>
                    
                    <form method="POST" action="{{ route('admin.rooms.update-status', $room) }}" class="space-y-4">
                        @csrf
                        @method('PATCH')
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="status" :value="__('New Status')" />
                                <select id="status" name="status" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="available" {{ $room->status === 'available' ? 'selected' : '' }}>{{ __('Available') }}</option>
                                    <option value="reserved" {{ $room->status === 'reserved' ? 'selected' : '' }}>{{ __('Reserved') }}</option>
                                    <option value="onboard" {{ $room->status === 'onboard' ? 'selected' : '' }}>{{ __('Onboard') }}</option>
                                    <option value="closed" {{ $room->status === 'closed' ? 'selected' : '' }}>{{ __('Closed') }}</option>
                                </select>
                                <x-input-error :messages="$errors->get('status')" class="mt-2" />
                            </div>
                            
                            <div>
                                <x-input-label for="reason" :value="__('Reason for Change')" />
                                <x-text-input id="reason" class="block mt-1 w-full" type="text" name="reason" 
                                            placeholder="{{ __('Enter reason for status change') }}" required />
                                <x-input-error :messages="$errors->get('reason')" class="mt-2" />
                            </div>
                        </div>
                        
                        <div class="flex justify-start">
                            <x-primary-button type="submit">
                                {{ __('Update Status') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Status History -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('Status History') }}</h3>
                    
                    @if($room->statusHistory->count() > 0)
                        <div class="space-y-4">
                            @foreach($room->statusHistory->sortByDesc('created_at') as $history)
                                <div class="border border-gray-200 rounded-lg p-4">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">
                                                {{ __('Status changed from') }} 
                                                <span class="font-semibold">{{ ucfirst($history->previous_status) }}</span> 
                                                {{ __('to') }} 
                                                <span class="font-semibold">{{ ucfirst($history->new_status) }}</span>
                                            </p>
                                            @if($history->reason)
                                                <p class="text-sm text-gray-600 mt-1">{{ __('Reason:') }} {{ $history->reason }}</p>
                                            @endif
                                            @if($history->changedBy)
                                                <p class="text-xs text-gray-500 mt-1">{{ __('Changed by:') }} {{ $history->changedBy->name }}</p>
                                            @endif
                                        </div>
                                        <div class="text-right">
                                            <p class="text-xs text-gray-500">{{ $history->created_at->format('M d, Y') }}</p>
                                            <p class="text-xs text-gray-500">{{ $history->created_at->format('H:i') }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500">{{ __('No status history available.') }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
