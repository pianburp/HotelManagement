<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Admin Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Statistics Overview -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-blue-500 bg-opacity-75">
                                <svg class="w-8 h-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" />
                                </svg>
                            </div>
                            <div class="mx-5">
                                <h4 class="text-2xl font-semibold text-gray-700">{{ $stats['total_users'] }}</h4>
                                <div class="text-gray-500">{{ __('Total Users') }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-green-500 bg-opacity-75">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                            </div>
                            <div class="mx-5">
                                <h4 class="text-2xl font-semibold text-gray-700">{{ $stats['available_rooms'] }}/{{ $stats['total_rooms'] }}</h4>
                                <div class="text-gray-500">{{ __('Available Rooms') }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-yellow-500 bg-opacity-75">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <div class="mx-5">
                                <h4 class="text-2xl font-semibold text-gray-700">{{ $stats['active_bookings'] }}</h4>
                                <div class="text-gray-500">{{ __('Active Bookings') }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-purple-500 bg-opacity-75">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                </svg>
                            </div>
                            <div class="mx-5">
                                <h4 class="text-2xl font-semibold text-gray-700">{{ money($stats['total_revenue']) }}</h4>
                                <div class="text-gray-500">{{ __('Total Revenue') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Recent Bookings -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold">{{ __('Recent Bookings') }}</h3>
                            <x-secondary-button onclick="window.location='{{ route('admin.bookings.index') }}'" class="text-sm">
                                {{ __('View All') }}
                            </x-secondary-button>
                        </div>
                        <div class="space-y-3">
                            @forelse($recentBookings as $booking)
                                <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg">
                                    <div>
                                        <p class="font-medium">{{ $booking->guest_name }}</p>
                                        <p class="text-sm text-gray-600">{{ $booking->room->roomType->name }} - Room {{ $booking->room->room_number }}</p>
                                        <p class="text-xs text-gray-500">{{ $booking->check_in->format('M d') }} - {{ $booking->check_out->format('M d, Y') }}</p>
                                    </div>
                                    <span class="px-2 py-1 text-xs rounded-full 
                                        @if($booking->status === 'pending') bg-yellow-100 text-yellow-800 border border-yellow-300
                                        @elseif($booking->status === 'confirmed') bg-green-100 text-green-800 border border-green-300
                                        @elseif($booking->status === 'checked_in') bg-blue-100 text-blue-800 border border-blue-300
                                        @elseif($booking->status === 'cancelled') bg-gray-200 text-gray-700 border border-gray-400
                                        @elseif($booking->status === 'completed') bg-purple-100 text-purple-800 border border-purple-300
                                        @elseif($booking->status === 'no_show') bg-red-100 text-red-800 border border-red-300
                                        @else bg-gray-100 text-gray-800 border border-gray-300
                                        @endif">
                                        {{ ucfirst($booking->status) }}
                                    </span>
                                </div>
                            @empty
                                <p class="text-gray-500 text-center py-4">{{ __('No recent bookings') }}</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Today's Check-ins -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold mb-4">{{ __("Today's Check-ins") }}</h3>
                        <div class="space-y-3">
                            @forelse($upcomingCheckIns as $booking)
                                <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg">
                                    <div>
                                        <p class="font-medium">{{ $booking->guest_name }}</p>
                                        <p class="text-sm text-gray-600">{{ $booking->room->roomType->name }} - Room {{ $booking->room->room_number }}</p>
                                        <p class="text-xs text-gray-500">{{ $booking->number_of_guests }} {{ __('guests') }}</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm font-medium">{{ money($booking->total_amount) }}</p>
                                        <p class="text-xs text-gray-500">{{ $booking->check_out->format('M d') }} checkout</p>
                                    </div>
                                </div>
                            @empty
                                <p class="text-gray-500 text-center py-4">{{ __('No check-ins today') }}</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="mt-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold mb-4">{{ __('Manage Rooms') }}</h3>
                        <x-primary-button onclick="window.location='{{ route('admin.rooms.index') }}'" class="w-full">
                            {{ __('View Rooms') }}
                        </x-primary-button>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold mb-4">{{ __('Manage Bookings') }}</h3>
                        <x-primary-button onclick="window.location='{{ route('admin.bookings.index') }}'" class="w-full">
                            {{ __('View Bookings') }}
                        </x-primary-button>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold mb-4">{{ __('Reports') }}</h3>
                        <x-primary-button onclick="window.location='{{ route('admin.reports.index') }}'" class="w-full">
                            {{ __('View Reports') }}
                        </x-primary-button>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold mb-4">{{ __('Waitlist') }}</h3>
                        <div class="text-center mb-2">
                            <span class="text-2xl font-bold text-orange-600">{{ $stats['waitlist_count'] }}</span>
                            <p class="text-sm text-gray-600">{{ __('Active Requests') }}</p>
                        </div>
                        <x-secondary-button onclick="window.location='{{ route('admin.waitlist.index') }}'" class="w-full">
                            {{ __('Manage Waitlist') }}
                        </x-secondary-button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
