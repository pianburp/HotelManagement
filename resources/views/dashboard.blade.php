<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('My Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Find Rooms Card -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold mb-4">{{ __('Find Your Perfect Room') }}</h3>
                        <p class="text-gray-600 mb-4">{{ __('Search available rooms and make a reservation for your stay.') }}</p>
                        <x-primary-button onclick="window.location='{{ route('user.rooms.index') }}'">
                            {{ __('Search Rooms') }}
                        </x-primary-button>
                    </div>
                </div>

                <!-- My Bookings Card -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold mb-4">{{ __('My Bookings') }}</h3>
                        <p class="text-gray-600 mb-4">{{ __('View and manage your current and upcoming reservations.') }}</p>
                        <x-primary-button onclick="window.location='{{ route('user.bookings.history') }}'">
                            {{ __('View Bookings') }}
                        </x-primary-button>
                    </div>
                </div>

                <!-- Waitlist Card -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold mb-4">{{ __('Room Waitlist') }}</h3>
                        <p class="text-gray-600 mb-4">{{ __('Join the waitlist for fully booked rooms and get notified when they become available.') }}</p>
                        <x-primary-button onclick="window.location='{{ route('user.waitlist.index') }}'">
                            {{ __('Check Waitlist') }}
                        </x-primary-button>
                    </div>
                </div>

                @if(isset($upcomingBooking))
                <!-- Upcoming Stay Card -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg md:col-span-2 lg:col-span-3">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold mb-4">{{ __('Your Upcoming Stay') }}</h3>
                        <div class="flex flex-wrap gap-4 items-center justify-between">
                            <div>
                                <p class="text-gray-600">{{ $upcomingBooking->room->roomType->name }} - {{ __('Room') }} {{ $upcomingBooking->room->room_number }}</p>
                                <p class="text-sm text-gray-500">
                                    {{ __('Check-in') }}: {{ $upcomingBooking->check_in->format('M d, Y') }} |
                                    {{ __('Check-out') }}: {{ $upcomingBooking->check_out->format('M d, Y') }}
                                </p>
                            </div>
                            <x-primary-button onclick="window.location='{{ route('user.bookings.show', $upcomingBooking) }}'">
                                {{ __('View Details') }}
                            </x-primary-button>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
