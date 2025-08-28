<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Payment Successful') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-center">
                    <!-- Success Icon -->
                    <div class="mx-auto flex items-center justify-center h-24 w-24 rounded-full bg-green-100 mb-6">
                        <svg class="h-12 w-12 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>

                    <!-- Success Message -->
                    <h3 class="text-2xl font-bold text-green-600 mb-4">{{ __('Payment Successful!') }}</h3>
                    <p class="text-gray-600 mb-8">{{ __('Your booking has been confirmed. We have sent a confirmation email to your registered email address.') }}</p>

                    <!-- Booking Confirmation Details -->
                    <div class="bg-gray-50 rounded-lg p-6 mb-8 text-left">
                        <h4 class="text-lg font-semibold mb-4">{{ __('Booking Confirmation') }}</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <p class="text-sm text-gray-600"><strong>{{ __('Confirmation Number') }}:</strong> HMS-{{ str_pad(rand(100000, 999999), 6, '0', STR_PAD_LEFT) }}</p>
                                <p class="text-sm text-gray-600"><strong>{{ __('Room Type') }}:</strong> {{ session('booking.room_type', 'Deluxe Suite') }}</p>
                                <p class="text-sm text-gray-600"><strong>{{ __('Check-in Date') }}:</strong> {{ session('booking.check_in', date('M d, Y')) }}</p>
                                <p class="text-sm text-gray-600"><strong>{{ __('Check-out Date') }}:</strong> {{ session('booking.check_out', date('M d, Y', strtotime('+3 days'))) }}</p>
                                <p class="text-sm text-gray-600"><strong>{{ __('Number of Guests') }}:</strong> {{ session('booking.guests', '2') }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600"><strong>{{ __('Payment Method') }}:</strong> {{ session('booking.payment_method', 'Credit Card') }}</p>
                                <p class="text-sm text-gray-600"><strong>{{ __('Transaction ID') }}:</strong> TXN-{{ str_pad(rand(1000000000, 9999999999), 10, '0', STR_PAD_LEFT) }}</p>
                                <p class="text-sm text-gray-600"><strong>{{ __('Payment Date') }}:</strong> {{ date('M d, Y H:i') }}</p>
                                <p class="text-lg font-bold text-green-600"><strong>{{ __('Total Paid') }}:</strong> {{ session('booking.total', 'RM 660.00') }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Next Steps -->
                    <div class="bg-blue-50 rounded-lg p-6 mb-8 text-left">
                        <h4 class="text-lg font-semibold text-blue-800 mb-4">{{ __('What\'s Next?') }}</h4>
                        <ul class="space-y-2 text-sm text-blue-700">
                            <li class="flex items-start">
                                <svg class="h-5 w-5 text-blue-600 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                {{ __('You will receive a confirmation email with your booking details within 5 minutes.') }}
                            </li>
                            <li class="flex items-start">
                                <svg class="h-5 w-5 text-blue-600 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                {{ __('Check-in starts at 3:00 PM on your arrival date.') }}
                            </li>
                            <li class="flex items-start">
                                <svg class="h-5 w-5 text-blue-600 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                {{ __('Present your confirmation number and valid ID at check-in.') }}
                            </li>
                            <li class="flex items-start">
                                <svg class="h-5 w-5 text-blue-600 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                {{ __('Contact us if you need to modify or cancel your booking.') }}
                            </li>
                        </ul>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        <a href="{{ route('dashboard') }}" 
                            class="inline-flex items-center px-6 py-3 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                            </svg>
                            {{ __('Go to Dashboard') }}
                        </a>
                        
                        <button onclick="window.print()" 
                            class="inline-flex items-center px-6 py-3 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                            </svg>
                            {{ __('Print Confirmation') }}
                        </button>
                        
                        <a href="{{ route('user.bookings.index') }}" 
                            class="inline-flex items-center px-6 py-3 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                            {{ __('View My Bookings') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        @media print {
            .no-print, nav, header, footer {
                display: none !important;
            }
        }
    </style>
</x-app-layout>
