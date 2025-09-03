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

            <!-- Current and Upcoming Bookings -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('Booking Information') }}</h3>
                    
                    <!-- Current Booking -->
                    @if($room->currentBooking)
                        <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                            <h4 class="text-md font-semibold text-blue-900 mb-3">{{ __('Current Guest') }}</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <div class="mb-2">
                                        <label class="block text-sm font-medium text-blue-700">{{ __('Guest Name') }}</label>
                                        <p class="text-blue-900 font-semibold">{{ $room->currentBooking->guest_name }}</p>
                                    </div>
                                    
                                    @if($room->currentBooking->guest_email)
                                    <div class="mb-2">
                                        <label class="block text-sm font-medium text-blue-700">{{ __('Email') }}</label>
                                        <p class="text-blue-900">{{ $room->currentBooking->guest_email }}</p>
                                    </div>
                                    @endif
                                    
                                    @if($room->currentBooking->guest_phone)
                                    <div class="mb-2">
                                        <label class="block text-sm font-medium text-blue-700">{{ __('Phone') }}</label>
                                        <p class="text-blue-900">{{ $room->currentBooking->guest_phone }}</p>
                                    </div>
                                    @endif
                                    
                                    <div class="mb-2">
                                        <label class="block text-sm font-medium text-blue-700">{{ __('Guests') }}</label>
                                        <p class="text-blue-900">{{ $room->currentBooking->number_of_guests }} {{ __('guests') }}</p>
                                    </div>
                                </div>
                                
                                <div>
                                    <div class="mb-2">
                                        <label class="block text-sm font-medium text-blue-700">{{ __('Check-in Date') }}</label>
                                        <p class="text-blue-900">{{ $room->currentBooking->check_in_date->format('M d, Y') }}</p>
                                    </div>
                                    
                                    <div class="mb-2">
                                        <label class="block text-sm font-medium text-blue-700">{{ __('Check-out Date') }}</label>
                                        <p class="text-blue-900">{{ $room->currentBooking->check_out_date->format('M d, Y') }}</p>
                                    </div>
                                    
                                    <div class="mb-2">
                                        <label class="block text-sm font-medium text-blue-700">{{ __('Booking Status') }}</label>
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                            @if($room->currentBooking->status === 'confirmed') bg-green-100 text-green-800
                                            @elseif($room->currentBooking->status === 'checked_in') bg-blue-100 text-blue-800
                                            @elseif($room->currentBooking->status === 'checked_out') bg-gray-100 text-gray-800
                                            @else bg-yellow-100 text-yellow-800
                                            @endif">
                                            {{ ucfirst(str_replace('_', ' ', $room->currentBooking->status)) }}
                                        </span>
                                    </div>
                                    
                                    <div class="mb-2">
                                        <label class="block text-sm font-medium text-blue-700">{{ __('Total Amount') }}</label>
                                        <p class="text-blue-900 font-semibold">RM {{ number_format($room->currentBooking->total_amount, 2) }}</p>
                                    </div>
                                </div>
                            </div>
                            
                            @if($room->currentBooking->special_requests)
                            <div class="mt-4">
                                <label class="block text-sm font-medium text-blue-700 mb-1">{{ __('Special Requests') }}</label>
                                <div class="bg-white p-3 rounded border">
                                    <p class="text-blue-900 text-sm">{{ $room->currentBooking->special_requests }}</p>
                                </div>
                            </div>
                            @endif
                            
                            <div class="mt-4 flex space-x-2">
                                <a href="{{ route('admin.bookings.show', $room->currentBooking) }}" 
                                   class="inline-flex items-center px-3 py-2 border border-blue-300 shadow-sm text-sm leading-4 font-medium rounded-md text-blue-700 bg-white hover:bg-blue-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    {{ __('View Full Booking') }}
                                </a>
                                @if($room->currentBooking->status === 'confirmed')
                                    <a href="{{ route('staff.checkin.show', $room->currentBooking) }}" 
                                       class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                        {{ __('Check In') }}
                                    </a>
                                @elseif($room->currentBooking->status === 'checked_in')
                                    <a href="{{ route('staff.checkout.show', $room->currentBooking) }}" 
                                       class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                        {{ __('Check Out') }}
                                    </a>
                                @endif
                            </div>
                        </div>
                    @elseif($room->status === 'onboard' && $room->activeBooking)
                        <!-- Fallback for onboard rooms that might have booking issues -->
                        <div class="mb-6 p-4 bg-orange-50 border border-orange-200 rounded-lg">
                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-orange-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h4 class="text-md font-semibold text-orange-900 mb-3">{{ __('Guest in Room (Status Mismatch)') }}</h4>
                                    <p class="text-sm text-orange-700 mb-3">{{ __('This room is marked as onboard but the booking dates may not align perfectly. Please verify the booking details.') }}</p>
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                                <div>
                                    <div class="mb-2">
                                        <label class="block text-sm font-medium text-orange-700">{{ __('Guest Name') }}</label>
                                        <p class="text-orange-900 font-semibold">{{ $room->activeBooking->guest_name }}</p>
                                    </div>
                                    
                                    @if($room->activeBooking->guest_email)
                                    <div class="mb-2">
                                        <label class="block text-sm font-medium text-orange-700">{{ __('Email') }}</label>
                                        <p class="text-orange-900">{{ $room->activeBooking->guest_email }}</p>
                                    </div>
                                    @endif
                                    
                                    @if($room->activeBooking->guest_phone)
                                    <div class="mb-2">
                                        <label class="block text-sm font-medium text-orange-700">{{ __('Phone') }}</label>
                                        <p class="text-orange-900">{{ $room->activeBooking->guest_phone }}</p>
                                    </div>
                                    @endif
                                </div>
                                
                                <div>
                                    <div class="mb-2">
                                        <label class="block text-sm font-medium text-orange-700">{{ __('Check-in Date') }}</label>
                                        <p class="text-orange-900">{{ $room->activeBooking->check_in_date->format('M d, Y') }}</p>
                                    </div>
                                    
                                    <div class="mb-2">
                                        <label class="block text-sm font-medium text-orange-700">{{ __('Check-out Date') }}</label>
                                        <p class="text-orange-900">{{ $room->activeBooking->check_out_date->format('M d, Y') }}</p>
                                    </div>
                                    
                                    <div class="mb-2">
                                        <label class="block text-sm font-medium text-orange-700">{{ __('Booking Status') }}</label>
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-orange-100 text-orange-800">
                                            {{ ucfirst(str_replace('_', ' ', $room->activeBooking->status)) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mt-4 flex space-x-2">
                                <a href="{{ route('admin.bookings.show', $room->activeBooking) }}" 
                                   class="inline-flex items-center px-3 py-2 border border-orange-300 shadow-sm text-sm leading-4 font-medium rounded-md text-orange-700 bg-white hover:bg-orange-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500">
                                    {{ __('View Full Booking') }}
                                </a>
                                <button type="button" onclick="alert('{{ __('Please check the booking dates and status. You may need to update the room status or booking details manually.') }}')"
                                        class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-orange-600 hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500">
                                    {{ __('Resolve Status') }}
                                </button>
                            </div>
                        </div>
                    @endif
                    
                    <!-- Upcoming Booking -->
                    @if($room->upcomingBooking)
                        <div class="mb-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                            <h4 class="text-md font-semibold text-yellow-900 mb-3">{{ __('Upcoming Booking') }}</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <div class="mb-2">
                                        <label class="block text-sm font-medium text-yellow-700">{{ __('Guest Name') }}</label>
                                        <p class="text-yellow-900 font-semibold">{{ $room->upcomingBooking->guest_name }}</p>
                                    </div>
                                    
                                    @if($room->upcomingBooking->guest_email)
                                    <div class="mb-2">
                                        <label class="block text-sm font-medium text-yellow-700">{{ __('Email') }}</label>
                                        <p class="text-yellow-900">{{ $room->upcomingBooking->guest_email }}</p>
                                    </div>
                                    @endif
                                    
                                    @if($room->upcomingBooking->guest_phone)
                                    <div class="mb-2">
                                        <label class="block text-sm font-medium text-yellow-700">{{ __('Phone') }}</label>
                                        <p class="text-yellow-900">{{ $room->upcomingBooking->guest_phone }}</p>
                                    </div>
                                    @endif
                                    
                                    <div class="mb-2">
                                        <label class="block text-sm font-medium text-yellow-700">{{ __('Guests') }}</label>
                                        <p class="text-yellow-900">{{ $room->upcomingBooking->number_of_guests }} {{ __('guests') }}</p>
                                    </div>
                                </div>
                                
                                <div>
                                    <div class="mb-2">
                                        <label class="block text-sm font-medium text-yellow-700">{{ __('Check-in Date') }}</label>
                                        <p class="text-yellow-900">{{ $room->upcomingBooking->check_in_date->format('M d, Y') }}</p>
                                        <p class="text-xs text-yellow-700">{{ __('In') }} {{ $room->upcomingBooking->check_in_date->diffForHumans() }}</p>
                                    </div>
                                    
                                    <div class="mb-2">
                                        <label class="block text-sm font-medium text-yellow-700">{{ __('Check-out Date') }}</label>
                                        <p class="text-yellow-900">{{ $room->upcomingBooking->check_out_date->format('M d, Y') }}</p>
                                    </div>
                                    
                                    <div class="mb-2">
                                        <label class="block text-sm font-medium text-yellow-700">{{ __('Booking Status') }}</label>
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                            {{ ucfirst(str_replace('_', ' ', $room->upcomingBooking->status)) }}
                                        </span>
                                    </div>
                                    
                                    <div class="mb-2">
                                        <label class="block text-sm font-medium text-yellow-700">{{ __('Total Amount') }}</label>
                                        <p class="text-yellow-900 font-semibold">RM {{ number_format($room->upcomingBooking->total_amount, 2) }}</p>
                                    </div>
                                </div>
                            </div>
                            
                            @if($room->upcomingBooking->special_requests)
                            <div class="mt-4">
                                <label class="block text-sm font-medium text-yellow-700 mb-1">{{ __('Special Requests') }}</label>
                                <div class="bg-white p-3 rounded border">
                                    <p class="text-yellow-900 text-sm">{{ $room->upcomingBooking->special_requests }}</p>
                                </div>
                            </div>
                            @endif
                            
                            <div class="mt-4">
                                <a href="{{ route('admin.bookings.show', $room->upcomingBooking) }}" 
                                   class="inline-flex items-center px-3 py-2 border border-yellow-300 shadow-sm text-sm leading-4 font-medium rounded-md text-yellow-700 bg-white hover:bg-yellow-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                                    {{ __('View Full Booking') }}
                                </a>
                            </div>
                        </div>
                    @endif
                    
                    <!-- No Current/Upcoming Bookings -->
                    @if(!$room->currentBooking && !$room->upcomingBooking && !($room->status === 'onboard' && $room->activeBooking))
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a2 2 0 012-2h4a2 2 0 012 2v4m-6 0V7a2 2 0 012-2h4a2 2 0 012 2v0m-6 0h8m-8 0H6a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V9a2 2 0 00-2-2h-2m-8 0V7a2 2 0 012-2h4a2 2 0 012 2v2m-6 0h8" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">{{ __('No Active Bookings') }}</h3>
                            <p class="mt-1 text-sm text-gray-500">{{ __('This room has no current or upcoming bookings.') }}</p>
                            @if($room->status === 'onboard')
                                <div class="mt-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                                    <div class="flex">
                                        <div class="flex-shrink-0">
                                            <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                        <div class="ml-3">
                                            <h3 class="text-sm font-medium text-red-800">{{ __('Status Inconsistency Warning') }}</h3>
                                            <div class="mt-2 text-sm text-red-700">
                                                <p>{{ __('This room is marked as "onboard" but has no associated booking. This may indicate:') }}</p>
                                                <ul class="list-disc pl-5 mt-2">
                                                    <li>{{ __('A booking was deleted without updating room status') }}</li>
                                                    <li>{{ __('Manual status change without corresponding booking') }}</li>
                                                    <li>{{ __('Data synchronization issue') }}</li>
                                                </ul>
                                                <p class="mt-2">{{ __('Please update the room status to reflect the actual occupancy.') }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                            <div class="mt-6">
                                <a href="{{ route('admin.bookings.create', ['room_id' => $room->id]) }}" 
                                   class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    {{ __('Create New Booking') }}
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Recent Booking History -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('Recent Booking History') }}</h3>
                    
                    @if($room->bookings->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Guest') }}</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Dates') }}</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Status') }}</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Amount') }}</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($room->bookings->sortByDesc('check_in_date')->take(5) as $booking)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900">{{ $booking->guest_name }}</div>
                                                @if($booking->guest_email)
                                                    <div class="text-sm text-gray-500">{{ $booking->guest_email }}</div>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">
                                                    {{ $booking->check_in_date->format('M d') }} - {{ $booking->check_out_date->format('M d, Y') }}
                                                </div>
                                                <div class="text-sm text-gray-500">
                                                    {{ $booking->check_in_date->diffInDays($booking->check_out_date) }} {{ __('nights') }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                    @if($booking->status === 'confirmed') bg-green-100 text-green-800
                                                    @elseif($booking->status === 'checked_in') bg-blue-100 text-blue-800
                                                    @elseif($booking->status === 'checked_out') bg-gray-100 text-gray-800
                                                    @elseif($booking->status === 'cancelled') bg-red-100 text-red-800
                                                    @else bg-yellow-100 text-yellow-800
                                                    @endif">
                                                    {{ ucfirst(str_replace('_', ' ', $booking->status)) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                RM {{ number_format($booking->total_amount, 2) }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <a href="{{ route('admin.bookings.show', $booking) }}" 
                                                   class="text-indigo-600 hover:text-indigo-900">{{ __('View') }}</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        @if($room->bookings->count() > 5)
                            <div class="mt-4">
                                <a href="{{ route('admin.bookings.index', ['room_id' => $room->id]) }}" 
                                   class="text-sm text-indigo-600 hover:text-indigo-900">
                                    {{ __('View All Bookings for This Room') }} ({{ $room->bookings->count() }} {{ __('total') }})
                                </a>
                            </div>
                        @endif
                    @else
                        <div class="text-center py-4">
                            <p class="text-gray-500">{{ __('No booking history available for this room.') }}</p>
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
