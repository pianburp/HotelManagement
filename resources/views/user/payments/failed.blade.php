<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Payment Failed') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-center">
                    <!-- Failed Icon -->
                    <div class="mx-auto flex items-center justify-center h-24 w-24 rounded-full bg-red-100 mb-6">
                        <svg class="h-12 w-12 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </div>

                    <!-- Failed Message -->
                    <h3 class="text-2xl font-bold text-red-600 mb-4">{{ __('Payment Failed') }}</h3>
                    <p class="text-gray-600 mb-8">{{ __('We were unable to process your payment. Please check your payment details and try again.') }}</p>

                    <!-- Error Details -->
                    <div class="bg-red-50 rounded-lg p-6 mb-8 text-left">
                        <h4 class="text-lg font-semibold text-red-800 mb-4">{{ __('Transaction Details') }}</h4>
                        <div class="space-y-2 text-sm text-red-700">
                            <p><strong>{{ __('Transaction ID') }}:</strong> TXN-{{ str_pad(rand(1000000000, 9999999999), 10, '0', STR_PAD_LEFT) }}</p>
                            <p><strong>{{ __('Error Code') }}:</strong> 
                                @php 
                                    $errorCodes = ['CARD_DECLINED', 'INSUFFICIENT_FUNDS', 'EXPIRED_CARD', 'INVALID_CVV', 'NETWORK_ERROR'];
                                    $randomError = $errorCodes[array_rand($errorCodes)];
                                @endphp
                                {{ $randomError }}
                            </p>
                            <p><strong>{{ __('Amount') }}:</strong> {{ session('booking.total', 'RM 660.00') }}</p>
                            <p><strong>{{ __('Attempted Date') }}:</strong> {{ date('M d, Y H:i') }}</p>
                            <p><strong>{{ __('Payment Method') }}:</strong> {{ session('booking.payment_method', 'Credit Card') }}</p>
                        </div>
                    </div>

                    <!-- Common Reasons -->
                    <div class="bg-yellow-50 rounded-lg p-6 mb-8 text-left">
                        <h4 class="text-lg font-semibold text-yellow-800 mb-4">{{ __('Common Reasons for Payment Failure') }}</h4>
                        <ul class="space-y-2 text-sm text-yellow-700">
                            <li class="flex items-start">
                                <svg class="h-5 w-5 text-yellow-600 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                </svg>
                                {{ __('Insufficient funds in your account') }}
                            </li>
                            <li class="flex items-start">
                                <svg class="h-5 w-5 text-yellow-600 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                </svg>
                                {{ __('Expired or invalid card details') }}
                            </li>
                            <li class="flex items-start">
                                <svg class="h-5 w-5 text-yellow-600 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                </svg>
                                {{ __('Card blocked or frozen by your bank') }}
                            </li>
                            <li class="flex items-start">
                                <svg class="h-5 w-5 text-yellow-600 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                </svg>
                                {{ __('Network or technical issues') }}
                            </li>
                            <li class="flex items-start">
                                <svg class="h-5 w-5 text-yellow-600 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                </svg>
                                {{ __('Transaction limits exceeded') }}
                            </li>
                        </ul>
                    </div>

                    <!-- Booking Summary (Still Available) -->
                    <div class="bg-gray-50 rounded-lg p-6 mb-8 text-left">
                        <h4 class="text-lg font-semibold mb-4">{{ __('Your Booking is Still Available') }}</h4>
                        <p class="text-sm text-gray-600 mb-4">{{ __('Don\'t worry! Your room selection is temporarily held for you.') }}</p>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm">
                            <div>
                                <p class="text-gray-600"><strong>{{ __('Room Type') }}:</strong> {{ session('booking.room_type', 'Deluxe Suite') }}</p>
                                <p class="text-gray-600"><strong>{{ __('Check-in Date') }}:</strong> {{ session('booking.check_in', date('M d, Y')) }}</p>
                                <p class="text-gray-600"><strong>{{ __('Check-out Date') }}:</strong> {{ session('booking.check_out', date('M d, Y', strtotime('+3 days'))) }}</p>
                            </div>
                            <div>
                                <p class="text-gray-600"><strong>{{ __('Number of Guests') }}:</strong> {{ session('booking.guests', '2') }}</p>
                                <p class="text-lg font-bold text-gray-800"><strong>{{ __('Total Amount') }}:</strong> {{ session('booking.total', 'RM 660.00') }}</p>
                                <p class="text-xs text-red-600 mt-1">{{ __('*Room will be released in 15 minutes if payment is not completed') }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        <a href="{{ route('user.payments.demo') }}" 
                            class="inline-flex items-center px-6 py-3 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                            {{ __('Try Payment Again') }}
                        </a>
                        
                        <a href="{{ route('user.bookings.create', ['room' => 1]) }}?check_in={{ session('booking.check_in') }}&check_out={{ session('booking.check_out') }}&guests={{ session('booking.guests') }}" 
                            class="inline-flex items-center px-6 py-3 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12"></path>
                            </svg>
                            {{ __('Back to Booking') }}
                        </a>
                        
                        <a href="{{ route('dashboard') }}" 
                            class="inline-flex items-center px-6 py-3 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                            </svg>
                            {{ __('Go to Dashboard') }}
                        </a>
                    </div>

                    <!-- Contact Support -->
                    <div class="mt-8 pt-6 border-t">
                        <p class="text-sm text-gray-600">
                            {{ __('Need help?') }} 
                            <a href="#" class="text-indigo-600 hover:text-indigo-900 font-medium">{{ __('Contact our support team') }}</a> 
                            {{ __('or call') }} 
                            <a href="tel:+60123456789" class="text-indigo-600 hover:text-indigo-900 font-medium">+60 12-345-6789</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
