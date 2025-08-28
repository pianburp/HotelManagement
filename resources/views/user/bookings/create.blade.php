<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Book Room') }} - {{ $room->roomType->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <form method="POST" action="{{ route('user.payments.demo') }}" class="p-6">
                    @csrf
                    <input type="hidden" name="room_id" value="{{ $room->id }}">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Booking Details -->
                        <div class="space-y-6">
                            <h3 class="text-lg font-medium">{{ __('Booking Details') }}</h3>
                            
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <x-input-label for="check_in" :value="__('Check In')" />
                                    <x-text-input id="check_in" type="date" name="check_in" 
                                        :value="old('check_in', request('check_in'))" 
                                        required class="mt-1 block w-full" 
                                        min="{{ date('Y-m-d') }}" />
                                    <x-input-error :messages="$errors->get('check_in')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="check_out" :value="__('Check Out')" />
                                    <x-text-input id="check_out" type="date" name="check_out" 
                                        :value="old('check_out', request('check_out'))" 
                                        required class="mt-1 block w-full" 
                                        min="{{ date('Y-m-d', strtotime('+1 day')) }}" />
                                    <x-input-error :messages="$errors->get('check_out')" class="mt-2" />
                                    <div id="checkout-error" class="text-red-600 text-sm mt-1 hidden">
                                        {{ __('Check-out date must be after check-in date') }}
                                    </div>
                                </div>
                            </div>

                            <div>
                                <x-input-label for="guests" :value="__('Number of Guests')" />
                                <select id="guests" name="guests" required 
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                    @for ($i = 1; $i <= $room->roomType->max_occupancy; $i++)
                                        <option value="{{ $i }}" {{ old('guests', request('guests')) == $i ? 'selected' : '' }}>
                                            {{ $i }} {{ __('Guest(s)') }}
                                        </option>
                                    @endfor
                                </select>
                                <x-input-error :messages="$errors->get('guests')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="special_requests" :value="__('Special Requests')" />
                                <textarea id="special_requests" name="special_requests" rows="3" 
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">{{ old('special_requests') }}</textarea>
                                <x-input-error :messages="$errors->get('special_requests')" class="mt-2" />
                            </div>
                        </div>

                        <!-- Price Summary -->
                        <div class="space-y-6">
                            <h3 class="text-lg font-medium">{{ __('Price Summary') }}</h3>
                            
                            <div class="bg-gray-50 rounded-lg p-6">
                                <div class="space-y-4">
                                    <div class="flex justify-between">
                                        <span>{{ __('Room Rate') }}</span>
                                        <span>{{ money($room->roomType->base_price) }} / {{ __('night') }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span>{{ __('Number of Nights') }}</span>
                                        <span class="nights-count">{{ $numberOfNights }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span>{{ __('Subtotal') }}</span>
                                        <span class="subtotal-amount">{{ money($subtotal) }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span>{{ __('Taxes & Fees') }}</span>
                                        <span class="taxes-amount">{{ money($taxes) }}</span>
                                    </div>
                                    <div class="border-t pt-4 flex justify-between font-bold">
                                        <span>{{ __('Total') }}</span>
                                        <span class="total-amount">{{ money($total) }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Payment Method -->
                            <div>
                                <h4 class="text-md font-medium mb-4">{{ __('Payment Method') }}</h4>
                                <div class="space-y-4">
                                    @foreach(['credit_card', 'debit_card', 'bank_transfer'] as $method)
                                        <label class="flex items-center">
                                            <input type="radio" name="payment_method" value="{{ $method }}"
                                                {{ old('payment_method') == $method ? 'checked' : '' }}
                                                class="border-gray-300 text-indigo-600 shadow-sm" required>
                                            <span class="ml-2">{{ __(ucfirst(str_replace('_', ' ', $method))) }}</span>
                                        </label>
                                    @endforeach
                                </div>
                                <x-input-error :messages="$errors->get('payment_method')" class="mt-2" />
                            </div>
                        </div>
                    </div>

                    <!-- Terms and Conditions -->
                    <div class="mt-6 border-t pt-6">
                        <label class="flex items-center">
                            <input type="checkbox" name="terms" required
                                class="rounded border-gray-300 text-indigo-600 shadow-sm">
                            <span class="ml-2">
                                {{ __('I agree to the') }} 
                                <a href="#" class="text-indigo-600 hover:text-indigo-900">{{ __('terms and conditions') }}</a>
                            </span>
                        </label>
                        <x-input-error :messages="$errors->get('terms')" class="mt-2" />
                    </div>

                    <div class="mt-6 flex justify-end">
                        <x-secondary-button type="button" onclick="window.history.back()" class="mr-3">
                            {{ __('Cancel') }}
                        </x-secondary-button>
                        <x-primary-button>
                            {{ __('Confirm Booking') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const checkInInput = document.getElementById('check_in');
            const checkOutInput = document.getElementById('check_out');
            const nightsSpan = document.querySelector('.nights-count');
            const subtotalSpan = document.querySelector('.subtotal-amount');
            const taxesSpan = document.querySelector('.taxes-amount');
            const totalSpan = document.querySelector('.total-amount');
            const checkoutError = document.getElementById('checkout-error');
            const form = document.querySelector('form');
            const submitButton = form.querySelector('button[type="submit"]');
            
            const basePrice = {{ $room->roomType->base_price }};
            const taxRate = 0.10; // 10% tax rate
            
            function calculateTotal() {
                const checkIn = new Date(checkInInput.value);
                const checkOut = new Date(checkOutInput.value);
                
                // Validate date order
                if (checkOut <= checkIn) {
                    if (checkoutError) checkoutError.classList.remove('hidden');
                    if (nightsSpan) nightsSpan.textContent = '0';
                    if (subtotalSpan) subtotalSpan.textContent = formatMoney(0);
                    if (taxesSpan) taxesSpan.textContent = formatMoney(0);
                    if (totalSpan) totalSpan.textContent = formatMoney(0);
                    if (submitButton) {
                        submitButton.disabled = true;
                        submitButton.classList.add('opacity-50', 'cursor-not-allowed');
                    }
                    return;
                }
                
                // Clear error state
                if (checkoutError) checkoutError.classList.add('hidden');
                if (submitButton) {
                    submitButton.disabled = false;
                    submitButton.classList.remove('opacity-50', 'cursor-not-allowed');
                }
                
                if (checkIn && checkOut && checkOut > checkIn) {
                    const timeDiff = checkOut.getTime() - checkIn.getTime();
                    const nights = Math.ceil(timeDiff / (1000 * 3600 * 24));
                    const subtotal = basePrice * nights;
                    const taxes = subtotal * taxRate;
                    const total = subtotal + taxes;
                    
                    if (nightsSpan) nightsSpan.textContent = nights;
                    if (subtotalSpan) subtotalSpan.textContent = formatMoney(subtotal);
                    if (taxesSpan) taxesSpan.textContent = formatMoney(taxes);
                    if (totalSpan) totalSpan.textContent = formatMoney(total);
                } else {
                    if (nightsSpan) nightsSpan.textContent = '0';
                    if (subtotalSpan) subtotalSpan.textContent = formatMoney(0);
                    if (taxesSpan) taxesSpan.textContent = formatMoney(0);
                    if (totalSpan) totalSpan.textContent = formatMoney(0);
                }
            }

            function updateCheckoutMinDate() {
                if (checkInInput.value) {
                    const checkInDate = new Date(checkInInput.value);
                    checkInDate.setDate(checkInDate.getDate() + 1);
                    const minCheckOut = checkInDate.toISOString().split('T')[0];
                    checkOutInput.min = minCheckOut;
                    
                    // If current checkout is before the new minimum, clear it
                    if (checkOutInput.value && checkOutInput.value <= checkInInput.value) {
                        checkOutInput.value = '';
                    }
                }
            }
            
            function formatMoney(amount) {
                return 'RM' + amount.toFixed(2);
            }
            
            // Event listeners
            checkInInput.addEventListener('change', function() {
                updateCheckoutMinDate();
                calculateTotal();
            });
            
            checkOutInput.addEventListener('change', calculateTotal);

            // Prevent form submission with invalid dates
            if (form) {
                form.addEventListener('submit', function(e) {
                    const checkIn = new Date(checkInInput.value);
                    const checkOut = new Date(checkOutInput.value);
                    
                    if (checkOut <= checkIn) {
                        e.preventDefault();
                        if (checkoutError) checkoutError.classList.remove('hidden');
                        checkOutInput.focus();
                    }
                });
            }
            
            // Calculate on page load if dates are present
            calculateTotal();
        });
    </script>
</x-app-layout>
