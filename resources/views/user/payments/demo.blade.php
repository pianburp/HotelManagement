<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Demo Payment Processing') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <!-- Payment Summary -->
                    <div class="mb-8">
                        <h3 class="text-lg font-medium mb-4">{{ __('Payment Summary') }}</h3>
                        <div class="bg-gray-50 rounded-lg p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <h4 class="font-medium mb-2">{{ __('Booking Details') }}</h4>
                                    <p class="text-sm text-gray-600">{{ __('Room Type') }}: {{ session('booking.room_type', 'Deluxe Suite') }}</p>
                                    <p class="text-sm text-gray-600">{{ __('Check-in') }}: {{ session('booking.check_in', date('Y-m-d')) }}</p>
                                    <p class="text-sm text-gray-600">{{ __('Check-out') }}: {{ session('booking.check_out', date('Y-m-d', strtotime('+3 days'))) }}</p>
                                    <p class="text-sm text-gray-600">{{ __('Guests') }}: {{ session('booking.guests', '2') }}</p>
                                </div>
                                <div>
                                    <h4 class="font-medium mb-2">{{ __('Payment Details') }}</h4>
                                    <p class="text-sm text-gray-600">{{ __('Subtotal') }}: {{ session('booking.subtotal', 'RM 600.00') }}</p>
                                    <p class="text-sm text-gray-600">{{ __('Taxes & Fees') }}: {{ session('booking.taxes', 'RM 60.00') }}</p>
                                    <p class="text-lg font-bold text-green-600">{{ __('Total') }}: {{ session('booking.total', 'RM 660.00') }}</p>
                                    <p class="text-sm text-gray-600 mt-2">{{ __('Payment Method') }}: {{ session('booking.payment_method', 'Credit Card') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Form -->
                    <div class="mb-8">
                        <h3 class="text-lg font-medium mb-4">{{ __('Payment Information') }}</h3>
                        <form id="payment-form" class="space-y-6">
                            @csrf
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="card_number" class="block text-sm font-medium text-gray-700">{{ __('Card Number') }}</label>
                                    <input type="text" id="card_number" name="card_number" 
                                        placeholder="1234 5678 9012 3456" maxlength="19"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                </div>
                                
                                <div>
                                    <label for="card_holder" class="block text-sm font-medium text-gray-700">{{ __('Card Holder Name') }}</label>
                                    <input type="text" id="card_holder" name="card_holder" 
                                        placeholder="John Doe"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                </div>
                                
                                <div>
                                    <label for="expiry_date" class="block text-sm font-medium text-gray-700">{{ __('Expiry Date') }}</label>
                                    <input type="text" id="expiry_date" name="expiry_date" 
                                        placeholder="MM/YY" maxlength="5"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                </div>
                                
                                <div>
                                    <label for="cvv" class="block text-sm font-medium text-gray-700">{{ __('CVV') }}</label>
                                    <input type="text" id="cvv" name="cvv" 
                                        placeholder="123" maxlength="4"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                </div>
                            </div>

                            <!-- Demo Payment Buttons -->
                            <div class="border-t pt-6">
                                <h4 class="text-md font-medium mb-4 text-blue-600">{{ __('Demo Payment Options') }}</h4>
                                <p class="text-sm text-gray-600 mb-4">{{ __('Choose a demo scenario to test the payment flow:') }}</p>
                                
                                <div class="flex flex-wrap gap-4">
                                    <button type="button" id="success-payment" 
                                        class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        {{ __('Simulate Successful Payment') }}
                                    </button>
                                    
                                    <button type="button" id="failed-payment" 
                                        class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                        {{ __('Simulate Failed Payment') }}
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Processing Indicator -->
                    <div id="processing" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full flex items-center justify-center">
                        <div class="bg-white p-8 rounded-lg shadow-lg text-center">
                            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600 mx-auto mb-4"></div>
                            <p class="text-lg font-medium">{{ __('Processing Payment...') }}</p>
                            <p class="text-sm text-gray-600">{{ __('Please wait while we process your payment.') }}</p>
                        </div>
                    </div>

                    <!-- Navigation -->
                    <div class="flex justify-between">
                        <a href="{{ url()->previous() }}" 
                            class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 focus:bg-gray-400 active:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            {{ __('Back to Booking') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const successBtn = document.getElementById('success-payment');
            const failedBtn = document.getElementById('failed-payment');
            const processing = document.getElementById('processing');
            
            // Format card number input
            const cardNumberInput = document.getElementById('card_number');
            if (cardNumberInput) {
                cardNumberInput.addEventListener('input', function(e) {
                    let value = e.target.value.replace(/\s/g, '').replace(/[^0-9]/gi, '');
                    let formattedValue = value.replace(/(.{4})/g, '$1 ').trim();
                    if (formattedValue.length > 19) {
                        formattedValue = formattedValue.substring(0, 19);
                    }
                    e.target.value = formattedValue;
                });
            }
            
            // Format expiry date input
            const expiryInput = document.getElementById('expiry_date');
            if (expiryInput) {
                expiryInput.addEventListener('input', function(e) {
                    let value = e.target.value.replace(/\D/g, '');
                    if (value.length >= 2) {
                        value = value.substring(0, 2) + '/' + value.substring(2, 4);
                    }
                    e.target.value = value;
                });
            }
            
            // CVV input validation
            const cvvInput = document.getElementById('cvv');
            if (cvvInput) {
                cvvInput.addEventListener('input', function(e) {
                    e.target.value = e.target.value.replace(/\D/g, '');
                });
            }
            
            function showProcessing() {
                processing.classList.remove('hidden');
            }
            
            function hideProcessing() {
                processing.classList.add('hidden');
            }
            
            function processPayment(status) {
                showProcessing();
                
                // Simulate payment processing delay
                setTimeout(() => {
                    hideProcessing();
                    
                    if (status === 'success') {
                        window.location.href = '{{ route("user.payments.success") }}';
                    } else if (status === 'failed') {
                        window.location.href = '{{ route("user.payments.failed") }}';
                    }
                }, 2000);
            }
            
            if (successBtn) {
                successBtn.addEventListener('click', () => processPayment('success'));
            }
            
            if (failedBtn) {
                failedBtn.addEventListener('click', () => processPayment('failed'));
            }
        });
    </script>
</x-app-layout>
