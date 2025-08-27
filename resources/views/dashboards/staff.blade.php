<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Staff Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Room Status Overview -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-green-500 bg-opacity-75">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                            <div class="mx-5">
                                <h4 class="text-2xl font-semibold text-gray-700">{{ $stats['available_rooms'] }}</h4>
                                <div class="text-gray-500">{{ __('Available') }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-blue-500 bg-opacity-75">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                            <div class="mx-5">
                                <h4 class="text-2xl font-semibold text-gray-700">{{ $stats['occupied_rooms'] }}</h4>
                                <div class="text-gray-500">{{ __('Occupied') }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-yellow-500 bg-opacity-75">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                </svg>
                            </div>
                            <div class="mx-5">
                                <h4 class="text-2xl font-semibold text-gray-700">{{ $stats['reserved_rooms'] }}</h4>
                                <div class="text-gray-500">{{ __('Reserved') }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-red-500 bg-opacity-75">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                </svg>
                            </div>
                            <div class="mx-5">
                                <h4 class="text-2xl font-semibold text-gray-700">{{ $stats['out_of_service'] }}</h4>
                                <div class="text-gray-500">{{ __('Out of Service') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Today's Activities -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold">{{ __("Today's Check-ins") }}</h3>
                            <span class="bg-blue-100 text-blue-800 text-sm font-medium px-2.5 py-0.5 rounded">
                                {{ $stats['todays_checkins'] }} {{ __('guests') }}
                            </span>
                        </div>
                        <div class="space-y-3 max-h-64 overflow-y-auto">
                            @forelse($todaysCheckIns as $booking)
                                <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg">
                                    <div>
                                        <p class="font-medium">{{ $booking->guest_name }}</p>
                                        <p class="text-sm text-gray-600">{{ $booking->room->roomType->name }} - Room {{ $booking->room->room_number }}</p>
                                        <p class="text-xs text-gray-500">{{ $booking->number_of_guests }} {{ __('guests') }}</p>
                                    </div>
                                    <div class="text-right">
                                        <x-secondary-button onclick="window.location='{{ route('staff.checkin.show', $booking) }}'" class="text-xs">
                                            {{ __('Check In') }}
                                        </x-secondary-button>
                                    </div>
                                </div>
                            @empty
                                <p class="text-gray-500 text-center py-4">{{ __('No check-ins scheduled for today') }}</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold">{{ __("Today's Check-outs") }}</h3>
                            <span class="bg-green-100 text-green-800 text-sm font-medium px-2.5 py-0.5 rounded">
                                {{ $stats['todays_checkouts'] }} {{ __('guests') }}
                            </span>
                        </div>
                        <div class="space-y-3 max-h-64 overflow-y-auto">
                            @forelse($todaysCheckOuts as $booking)
                                <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg">
                                    <div>
                                        <p class="font-medium">{{ $booking->guest_name }}</p>
                                        <p class="text-sm text-gray-600">{{ $booking->room->roomType->name }} - Room {{ $booking->room->room_number }}</p>
                                        <p class="text-xs text-gray-500">{{ $booking->number_of_guests }} {{ __('guests') }}</p>
                                    </div>
                                    <div class="text-right">
                                        <x-secondary-button onclick="window.location='{{ route('staff.checkout.show', $booking) }}'" class="text-xs">
                                            {{ __('Check Out') }}
                                        </x-secondary-button>
                                    </div>
                                </div>
                            @empty
                                <p class="text-gray-500 text-center py-4">{{ __('No check-outs scheduled for today') }}</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <!-- Rooms Needing Attention -->
            @if($roomsNeedingAttention->count() > 0)
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4 text-red-600">{{ __('Rooms Needing Attention') }}</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($roomsNeedingAttention as $room)
                            <div class="border border-red-200 rounded-lg p-4 bg-red-50">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="font-medium">{{ $room->roomType->name }}</p>
                                        <p class="text-sm text-gray-600">Room {{ $room->room_number }}</p>
                                        <span class="inline-block px-2 py-1 text-xs rounded-full bg-red-100 text-red-800 mt-1">
                                            {{ ucfirst($room->status) }}
                                        </span>
                                    </div>
                                    <x-secondary-button onclick="window.location='{{ route('staff.rooms.edit', $room) }}'" class="text-xs">
                                        {{ __('Update') }}
                                    </x-secondary-button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            <!-- Quick Actions -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold mb-4">{{ __('Room Status') }}</h3>
                        <p class="text-gray-600 mb-4">{{ __('Update room status and manage housekeeping') }}</p>
                        <x-primary-button onclick="window.location='{{ route('staff.rooms.index') }}'" class="w-full">
                            {{ __('Manage Rooms') }}
                        </x-primary-button>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold mb-4">{{ __('Check-in/Check-out') }}</h3>
                        <p class="text-gray-600 mb-4">{{ __('Process guest arrivals and departures') }}</p>
                        <x-primary-button onclick="window.location='{{ route('staff.checkin.index') }}'" class="w-full">
                            {{ __('Manage Check-ins') }}
                        </x-primary-button>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold mb-4">{{ __('Waitlist Management') }}</h3>
                        <div class="text-center mb-2">
                            <span class="text-2xl font-bold text-orange-600">{{ $stats['active_waitlist'] }}</span>
                            <p class="text-sm text-gray-600">{{ __('Active Requests') }}</p>
                        </div>
                        <x-secondary-button onclick="window.location='{{ route('staff.waitlist.index') }}'" class="w-full">
                            {{ __('Manage Waitlist') }}
                        </x-secondary-button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
