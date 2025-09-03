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
                
                <!-- Waitlist Form -->
                <div id="waitlist-container" class="mt-6 border-t pt-6 hidden">
                    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-yellow-700">
                                    {{ __('This room is not available for the selected dates. Would you like to join the waitlist?') }}
                                </p>
                                <p class="text-sm text-yellow-700 mt-1 font-semibold waitlist-date-display"></p>
                            </div>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('user.waitlist.store') }}" class="space-y-6">
                        @csrf
                        <input type="hidden" name="room_type_id" value="{{ $room->roomType->id }}">
                        <input type="hidden" name="check_in" id="waitlist_check_in">
                        <input type="hidden" name="check_out" id="waitlist_check_out">
                        <input type="hidden" name="guests" id="waitlist_guests">
                        
                        <div>
                            <x-input-label for="waitlist_note" :value="__('Additional Notes')" />
                            <textarea id="waitlist_note" name="note" rows="2" 
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                                placeholder="{{ __('Any flexibility with your dates or special requirements') }}"></textarea>
                        </div>
                        
                        <div class="flex items-center">
                            <input type="checkbox" id="waitlist_notification" name="notification" checked
                                class="rounded border-gray-300 text-indigo-600 shadow-sm">
                            <label for="waitlist_notification" class="ml-2 text-sm text-gray-600">
                                {{ __('Notify me when this room becomes available') }}
                            </label>
                        </div>
                        
                        <div class="flex justify-end">
                            <x-primary-button>
                                {{ __('Join Waitlist') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const checkInInput = document.getElementById('check_in');
            const checkOutInput = document.getElementById('check_out');
            const guestsInput = document.getElementById('guests');
            const nightsSpan = document.querySelector('.nights-count');
            const subtotalSpan = document.querySelector('.subtotal-amount');
            const taxesSpan = document.querySelector('.taxes-amount');
            const totalSpan = document.querySelector('.total-amount');
            const checkoutError = document.getElementById('checkout-error');
            const form = document.querySelector('form');
            const submitButton = form.querySelector('button[type="submit"]');
            
            // Waitlist form inputs
            const waitlistCheckIn = document.getElementById('waitlist_check_in');
            const waitlistCheckOut = document.getElementById('waitlist_check_out');
            const waitlistGuests = document.getElementById('waitlist_guests');
            
            const basePrice = {{ $room->roomType->base_price }};
            const taxRate = 0.10; // 10% tax rate
            const roomId = {{ $room->id }};
            
            // Create availability error container
            const availabilityError = document.createElement('div');
            availabilityError.id = 'availability-error';
            availabilityError.className = 'text-red-600 text-sm mt-1 hidden';
            checkOutInput.parentNode.appendChild(availabilityError);
            
            function calculateTotal() {
                const checkIn = new Date(checkInInput.value);
                const checkOut = new Date(checkOutInput.value);
                
                // Sync values with waitlist form
                syncWaitlistFormValues();
                
                // Validate date order
                if (checkOut <= checkIn) {
                    if (checkoutError) checkoutError.classList.remove('hidden');
                    if (nightsSpan) nightsSpan.textContent = '0';
                    if (subtotalSpan) subtotalSpan.textContent = formatMoney(0);
                    if (taxesSpan) taxesSpan.textContent = formatMoney(0);
                    if (totalSpan) totalSpan.textContent = formatMoney(0);
                    disableSubmitButton('Invalid date range');
                    return;
                }
                
                // Clear error state
                if (checkoutError) checkoutError.classList.add('hidden');
                
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
                    
                    // Check availability with server
                    checkAvailability(checkInInput.value, checkOutInput.value);
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
            
            function disableSubmitButton(reason) {
                if (submitButton) {
                    submitButton.disabled = true;
                    submitButton.classList.add('opacity-50', 'cursor-not-allowed');
                    submitButton.title = reason;
                }
            }
            
            function enableSubmitButton() {
                if (submitButton) {
                    submitButton.disabled = false;
                    submitButton.classList.remove('opacity-50', 'cursor-not-allowed');
                    submitButton.title = '';
                }
            }
            
            async function checkAvailability(checkIn, checkOut) {
                try {
                    const response = await fetch(`/api/rooms/${roomId}/check-availability`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            check_in: checkIn,
                            check_out: checkOut
                        })
                    });
                    
                    const data = await response.json();
                    
                    if (data.available) {
                        availabilityError.classList.add('hidden');
                        enableSubmitButton();
                    } else {
                        let errorMessage = 'Selected dates are not available.';
                        if (data.conflicts && data.conflicts.length > 0) {
                            const conflictDates = data.conflicts.map(conflict => 
                                `${conflict.check_in} - ${conflict.check_out}`
                            ).join(', ');
                            errorMessage += ` Conflicting bookings: ${conflictDates}`;
                        }
                        
                        availabilityError.textContent = errorMessage;
                        availabilityError.classList.remove('hidden');
                        disableSubmitButton('Dates not available');
                        
                        // Show waitlist option
                        document.getElementById('waitlist-container').classList.remove('hidden');
                        
                        // Make sure the waitlist form has the latest values
                        syncWaitlistFormValues();
                    } else {
                        // Hide waitlist option when room is available
                        document.getElementById('waitlist-container').classList.add('hidden');
                    }
                } catch (error) {
                    console.error('Error checking availability:', error);
                    // On error, allow submission but show warning
                    availabilityError.textContent = 'Unable to verify availability. Please check dates.';
                    availabilityError.classList.remove('hidden');
                    enableSubmitButton();
                    
                    // Hide waitlist option on error
                    document.getElementById('waitlist-container').classList.add('hidden');
                }
            }
            
            function syncWaitlistFormValues() {
                // Sync form values to waitlist form
                if (waitlistCheckIn && checkInInput.value) {
                    waitlistCheckIn.value = checkInInput.value;
                }
                
                if (waitlistCheckOut && checkOutInput.value) {
                    waitlistCheckOut.value = checkOutInput.value;
                }
                
                if (waitlistGuests && guestsInput.value) {
                    waitlistGuests.value = guestsInput.value;
                }
                
                // Update the date display in the waitlist notification
                const dateDisplay = document.querySelector('.waitlist-date-display');
                if (dateDisplay && checkInInput.value && checkOutInput.value) {
                    const checkIn = new Date(checkInInput.value);
                    const checkOut = new Date(checkOutInput.value);
                    const options = { weekday: 'short', year: 'numeric', month: 'short', day: 'numeric' };
                    
                    if (checkOut > checkIn) {
                        const nights = Math.ceil((checkOut - checkIn) / (1000 * 60 * 60 * 24));
                        dateDisplay.textContent = `${checkIn.toLocaleDateString(undefined, options)} to ${checkOut.toLocaleDateString(undefined, options)} (${nights} nights)`;
                    }
                }
            }
            
            // Event listeners
            checkInInput.addEventListener('change', function() {
                updateCheckoutMinDate();
                calculateTotal();
                syncWaitlistFormValues();
            });
            
            checkOutInput.addEventListener('change', function() {
                calculateTotal();
                syncWaitlistFormValues();
            });
            
            guestsInput.addEventListener('change', syncWaitlistFormValues);

            // Prevent form submission with invalid dates or unavailable rooms
            if (form) {
                form.addEventListener('submit', function(e) {
                    const checkIn = new Date(checkInInput.value);
                    const checkOut = new Date(checkOutInput.value);
                    
                    if (checkOut <= checkIn) {
                        e.preventDefault();
                        if (checkoutError) checkoutError.classList.remove('hidden');
                        checkOutInput.focus();
                        return;
                    }
                    
                    if (submitButton.disabled) {
                        e.preventDefault();
                        alert('Please select valid available dates before proceeding.');
                        return;
                    }
                });
            }
            
            // Add validation for waitlist form
            const waitlistForm = document.querySelector('#waitlist-container form');
            if (waitlistForm) {
                waitlistForm.addEventListener('submit', function(e) {
                    const checkIn = new Date(waitlistCheckIn.value);
                    const checkOut = new Date(waitlistCheckOut.value);
                    
                    if (checkOut <= checkIn) {
                        e.preventDefault();
                        alert('Check-out date must be after check-in date');
                        return;
                    }
                    
                    // Additional validation could be added here
                });
            }
            
            // Make sure to sync the waitlist form initially
            document.addEventListener('DOMContentLoaded', function() {
                syncWaitlistFormValues();
                
                // Calculate on page load if dates are present
                calculateTotal();
            });
        });
    </script>
</x-app-layout>
