<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Booking Management') }}
            </h2>
            <x-primary-button onclick="window.location='{{ route('admin.bookings.create') }}'">
                {{ __('Create Booking') }}
            </x-primary-button>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Filters -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <form method="GET" action="{{ route('admin.bookings.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                        <div>
                            <x-input-label for="status" :value="__('Status')" />
                            <select id="status" name="status" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="">{{ __('All Statuses') }}</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>{{ __('Pending') }}</option>
                                <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>{{ __('Confirmed') }}</option>
                                <option value="checked_in" {{ request('status') == 'checked_in' ? 'selected' : '' }}>{{ __('Checked In') }}</option>
                                <option value="checked_out" {{ request('status') == 'checked_out' ? 'selected' : '' }}>{{ __('Checked Out') }}</option>
                                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>{{ __('Cancelled') }}</option>
                            </select>
                        </div>

                        <div>
                            <x-input-label for="check_in_from" :value="__('Check-in From')" />
                            <x-text-input id="check_in_from" class="block mt-1 w-full" type="date" name="check_in_from" 
                                        :value="request('check_in_from')" />
                        </div>

                        <div>
                            <x-input-label for="check_in_to" :value="__('Check-in To')" />
                            <x-text-input id="check_in_to" class="block mt-1 w-full" type="date" name="check_in_to" 
                                        :value="request('check_in_to')" />
                        </div>

                        <div>
                            <x-input-label for="guest_name" :value="__('Guest Name')" />
                            <x-text-input id="guest_name" class="block mt-1 w-full" type="text" name="guest_name" 
                                        :value="request('guest_name')" placeholder="{{ __('Search by guest name') }}" />
                        </div>

                        <div class="flex items-end space-x-2">
                            <x-primary-button type="submit">
                                {{ __('Filter') }}
                            </x-primary-button>
                            <x-secondary-button onclick="window.location='{{ route('admin.bookings.index') }}'">
                                {{ __('Clear') }}
                            </x-secondary-button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-4 text-center">
                        <div class="text-xl font-bold text-blue-600">{{ $stats['total'] }}</div>
                        <div class="text-xs text-gray-600">{{ __('Total') }}</div>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-4 text-center">
                        <div class="text-xl font-bold text-yellow-600">{{ $stats['pending'] }}</div>
                        <div class="text-xs text-gray-600">{{ __('Pending') }}</div>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-4 text-center">
                        <div class="text-xl font-bold text-green-600">{{ $stats['confirmed'] }}</div>
                        <div class="text-xs text-gray-600">{{ __('Confirmed') }}</div>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-4 text-center">
                        <div class="text-xl font-bold text-purple-600">{{ $stats['checked_in'] }}</div>
                        <div class="text-xs text-gray-600">{{ __('Checked In') }}</div>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-4 text-center">
                        <div class="text-xl font-bold text-red-600">{{ $stats['cancelled'] }}</div>
                        <div class="text-xs text-gray-600">{{ __('Cancelled') }}</div>
                    </div>
                </div>
            </div>

            <!-- Bookings Table -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if(session('success'))
                        <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Booking ID') }}
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Guest') }}
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Room') }}
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Dates') }}
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Amount') }}
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Status') }}
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Payment') }}
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Actions') }}
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($bookings as $booking)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">#{{ $booking->booking_number }}</div>
                                            <div class="text-sm text-gray-500">{{ $booking->created_at->format('M d, Y') }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ $booking->guest_name }}</div>
                                            <div class="text-sm text-gray-500">{{ $booking->guest_email }}</div>
                                            <div class="text-sm text-gray-500">{{ $booking->number_of_guests }} {{ __('guests') }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $booking->room->roomType->name }}
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                {{ __('Room') }} {{ $booking->room->room_number }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">
                                                <div>{{ __('In') }}: {{ $booking->check_in->format('M d, Y') }}</div>
                                                <div>{{ __('Out') }}: {{ $booking->check_out->format('M d, Y') }}</div>
                                                <div class="text-gray-500">{{ $booking->check_in->diffInDays($booking->check_out) }} {{ __('nights') }}</div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ money($booking->total_amount) }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                @if($booking->status === 'confirmed') bg-green-100 text-green-800
                                                @elseif($booking->status === 'pending') bg-yellow-100 text-yellow-800
                                                @elseif($booking->status === 'checked_in') bg-blue-100 text-blue-800
                                                @elseif($booking->status === 'checked_out') bg-gray-100 text-gray-800
                                                @else bg-red-100 text-red-800
                                                @endif">
                                                {{ ucfirst(str_replace('_', ' ', $booking->status)) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($booking->payment)
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                    @if($booking->payment->status === 'completed') bg-green-100 text-green-800
                                                    @elseif($booking->payment->status === 'pending') bg-yellow-100 text-yellow-800
                                                    @else bg-red-100 text-red-800
                                                    @endif">
                                                    {{ ucfirst($booking->payment->status) }}
                                                </span>
                                            @else
                                                <span class="text-gray-400 text-xs">{{ __('No Payment') }}</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex space-x-1">
                                                <x-secondary-button onclick="window.location='{{ route('admin.bookings.show', $booking) }}'" class="text-xs py-1 px-2">
                                                    {{ __('View') }}
                                                </x-secondary-button>
                                                
                                                @if($booking->status === 'pending')
                                                    <x-primary-button onclick="confirmBooking('{{ $booking->id }}')" class="text-xs py-1 px-2">
                                                        {{ __('Confirm') }}
                                                    </x-primary-button>
                                                @endif
                                                
                                                @if(in_array($booking->status, ['pending', 'confirmed']))
                                                    <x-secondary-button onclick="window.location='{{ route('admin.bookings.edit', $booking) }}'" class="text-xs py-1 px-2">
                                                        {{ __('Edit') }}
                                                    </x-secondary-button>
                                                @endif

                                                @if($booking->status !== 'checked_out')
                                                    <x-danger-button onclick="cancelBooking('{{ $booking->id }}')" class="text-xs py-1 px-2">
                                                        {{ __('Cancel') }}
                                                    </x-danger-button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="px-6 py-4 text-center text-gray-500">
                                            {{ __('No bookings found.') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($bookings->hasPages())
                        <div class="mt-6">
                            {{ $bookings->withQueryString()->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Confirmation Modal -->
    <x-modal name="confirm-booking" :show="false" maxWidth="md">
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900">
                {{ __('Confirm Booking') }}
            </h2>
            <p class="mt-1 text-sm text-gray-600">
                {{ __('Are you sure you want to confirm this booking? This action will update the booking status and room availability.') }}
            </p>
            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close')">
                    {{ __('Cancel') }}
                </x-secondary-button>
                <x-primary-button class="ml-3" onclick="submitConfirmBooking()">
                    {{ __('Confirm Booking') }}
                </x-primary-button>
            </div>
        </div>
    </x-modal>

    <!-- Cancel Modal -->
    <x-modal name="cancel-booking" :show="false" maxWidth="md">
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900">
                {{ __('Cancel Booking') }}
            </h2>
            <p class="mt-1 text-sm text-gray-600">
                {{ __('Are you sure you want to cancel this booking? This action cannot be undone.') }}
            </p>
            <div class="mt-4">
                <x-input-label for="cancellation_reason" :value="__('Cancellation Reason')" />
                <textarea id="cancellation_reason" rows="3" 
                        class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                        placeholder="{{ __('Enter reason for cancellation...') }}"></textarea>
            </div>
            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close')">
                    {{ __('Cancel') }}
                </x-secondary-button>
                <x-danger-button class="ml-3" onclick="submitCancelBooking()">
                    {{ __('Cancel Booking') }}
                </x-danger-button>
            </div>
        </div>
    </x-modal>

    <script>
        let currentBookingId = null;

        function confirmBooking(bookingId) {
            currentBookingId = bookingId;
            window.dispatchEvent(new CustomEvent('open-modal', { detail: 'confirm-booking' }));
        }

        function cancelBooking(bookingId) {
            currentBookingId = bookingId;
            window.dispatchEvent(new CustomEvent('open-modal', { detail: 'cancel-booking' }));
        }

        function submitConfirmBooking() {
            if (currentBookingId) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/admin/bookings/${currentBookingId}/confirm`;
                
                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = '{{ csrf_token() }}';
                
                const methodField = document.createElement('input');
                methodField.type = 'hidden';
                methodField.name = '_method';
                methodField.value = 'PATCH';
                
                form.appendChild(csrfToken);
                form.appendChild(methodField);
                document.body.appendChild(form);
                form.submit();
            }
        }

        function submitCancelBooking() {
            if (currentBookingId) {
                const reason = document.getElementById('cancellation_reason').value;
                
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/admin/bookings/${currentBookingId}/cancel`;
                
                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = '{{ csrf_token() }}';
                
                const methodField = document.createElement('input');
                methodField.type = 'hidden';
                methodField.name = '_method';
                methodField.value = 'PATCH';
                
                const reasonField = document.createElement('input');
                reasonField.type = 'hidden';
                reasonField.name = 'cancellation_reason';
                reasonField.value = reason;
                
                form.appendChild(csrfToken);
                form.appendChild(methodField);
                form.appendChild(reasonField);
                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
</x-app-layout>
