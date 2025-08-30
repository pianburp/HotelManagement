<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('My Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Quick Stats -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-blue-500 bg-opacity-75">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <rect x="4" y="4" width="16" height="16" rx="2" stroke="currentColor" stroke-width="2" fill="none"></rect>
                                    <line x1="8" y1="8" x2="16" y2="8" stroke="currentColor" stroke-width="2" stroke-linecap="round"></line>
                                    <line x1="8" y1="12" x2="16" y2="12" stroke="currentColor" stroke-width="2" stroke-linecap="round"></line>
                                    <line x1="8" y1="16" x2="12" y2="16" stroke="currentColor" stroke-width="2" stroke-linecap="round"></line>
                                </svg>
                            </div>
                            <div class="mx-5">
                                <h4 class="text-2xl font-semibold text-gray-700">{{ $stats['total_bookings'] }}</h4>
                                <div class="text-gray-500">{{ __('Total Bookings') }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-green-500 bg-opacity-75">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="mx-5">
                                <h4 class="text-2xl font-semibold text-gray-700">{{ $stats['upcoming_bookings'] }}</h4>
                                <div class="text-gray-500">{{ __('Upcoming Stays') }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-orange-500 bg-opacity-75">
                                <span class="sr-only">{{ __('Waitlist') }}</span>
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <circle cx="4" cy="6" r="1.5" fill="currentColor"></circle>
                                    <circle cx="4" cy="12" r="1.5" fill="currentColor"></circle>
                                    <circle cx="4" cy="18" r="1.5" fill="currentColor"></circle>
                                    <line x1="10" y1="6" x2="20" y2="6" stroke="currentColor" stroke-width="2" stroke-linecap="round"></line>
                                    <line x1="10" y1="12" x2="20" y2="12" stroke="currentColor" stroke-width="2" stroke-linecap="round"></line>
                                    <line x1="10" y1="18" x2="20" y2="18" stroke="currentColor" stroke-width="2" stroke-linecap="round"></line>
                                </svg>
                            </div>
                            <div class="mx-5">
                                <h4 class="text-2xl font-semibold text-gray-700">{{ $stats['active_waitlist'] }}</h4>
                                <div class="text-gray-500">{{ __('Waitlist Requests') }}</div>
                            </div>
                        </div>      
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Actions -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Upcoming Booking -->
                    @if($upcomingBooking)
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold mb-4">{{ __('Your Upcoming Stay') }}</h3>
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="font-medium text-blue-900">{{ $upcomingBooking->room->roomType->name }}</p>
                                        <p class="text-sm text-blue-700">Room {{ $upcomingBooking->room->room_number }}</p>
                                        <p class="text-sm text-blue-600 mt-1">
                                            {{ $upcomingBooking->check_in->format('M d, Y') }} - {{ $upcomingBooking->check_out->format('M d, Y') }}
                                        </p>
                                        <p class="text-xs text-blue-500 mt-1">
                                            {{ (int) abs(now()->startOfDay()->diffInDays($upcomingBooking->check_in->startOfDay())) }} {{ __('days until check-in') }}
                                        </p>
                                    </div>
                                    <x-primary-button onclick="window.location='{{ route('user.bookings.show', $upcomingBooking) }}'">
                                        {{ __('View Details') }}
                                    </x-primary-button>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Recent Bookings -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-semibold">{{ __('Recent Bookings') }}</h3>
                                <x-secondary-button onclick="window.location='{{ route('user.bookings.history') }}'" class="text-sm">
                                    {{ __('View All') }}
                                </x-secondary-button>
                            </div>
                            <div class="space-y-3">
                                @forelse($recentBookings as $booking)
                                    <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg">
                                        <div>
                                            <p class="font-medium">{{ $booking->room->roomType->name }}</p>
                                            <p class="text-sm text-gray-600">Room {{ $booking->room->room_number }}</p>
                                            <p class="text-xs text-gray-500">{{ $booking->check_in->format('M d') }} - {{ $booking->check_out->format('M d, Y') }}</p>
                                        </div>
                                        <div class="text-right">
                                            <span class="px-2 py-1 text-xs rounded-full 
                                                @if($booking->status === 'confirmed') bg-green-100 text-green-800
                                                @elseif($booking->status === 'pending') bg-yellow-100 text-yellow-800
                                                @else bg-red-100 text-red-800
                                                @endif">
                                                {{ ucfirst($booking->status) }}
                                            </span>
                                            <p class="text-sm font-medium mt-1">{{ money($booking->total_amount) }}</p>
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-gray-500 text-center py-4">{{ __('No bookings yet') }}</p>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Active Waitlist -->
                    @if($activeWaitlist->count() > 0)
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-semibold">{{ __('Your Waitlist') }}</h3>
                                <x-secondary-button onclick="window.location='{{ route('user.waitlist.index') }}'" class="text-sm">
                                    {{ __('View All') }}
                                </x-secondary-button>
                            </div>
                            <div class="space-y-3">
                                @foreach($activeWaitlist as $waitlist)
                                    <div class="p-3 border border-orange-200 rounded-lg bg-orange-50">
                                        <p class="font-medium text-orange-900">{{ $waitlist->roomType->name }}</p>
                                        <p class="text-sm text-orange-700">
                                            {{ $waitlist->preferred_check_in->format('M d') }} - {{ $waitlist->preferred_check_out->format('M d, Y') }}
                                        </p>
                                        <p class="text-xs text-orange-600 mt-1">
                                            {{ __('Up to') }} {{ money($waitlist->max_price) }}
                                        </p>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Help & Support -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold mb-4">{{ __('Need Help?') }}</h3>
                            <p class="text-gray-600 text-sm mb-4">{{ __('Contact our support team for assistance with your booking or account.') }}</p>
                            <div class="space-y-2">
                                <p class="text-sm"><strong>{{ __('Phone:') }}</strong> +60 3-1234 5678</p>
                                <p class="text-sm"><strong>{{ __('Email:') }}</strong> support@hotel.com</p>
                                <p class="text-sm"><strong>{{ __('Hours:') }}</strong> 24/7</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const checkInInput = document.getElementById('quick_check_in');
            const checkOutInput = document.getElementById('quick_check_out');

            function updateCheckoutMinDate() {
                if (checkInInput.value) {
                    const checkInDate = new Date(checkInInput.value);
                    checkInDate.setDate(checkInDate.getDate() + 1);
                    const minCheckOut = checkInDate.toISOString().split('T')[0];
                    checkOutInput.min = minCheckOut;
                    
                    if (checkOutInput.value && checkOutInput.value <= checkInInput.value) {
                        checkOutInput.value = '';
                    }
                }
            }

            checkInInput.addEventListener('change', updateCheckoutMinDate);
        });
    </script>
</x-app-layout>
