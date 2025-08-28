<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Waitlist Management') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-center">
                        <div class="text-2xl font-bold text-orange-600">{{ $stats['total'] }}</div>
                        <div class="text-sm text-gray-600">{{ __('Total Requests') }}</div>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-center">
                        <div class="text-2xl font-bold text-yellow-600">{{ $stats['pending'] }}</div>
                        <div class="text-sm text-gray-600">{{ __('Pending') }}</div>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-center">
                        <div class="text-2xl font-bold text-blue-600">{{ $stats['notified'] }}</div>
                        <div class="text-sm text-gray-600">{{ __('Notified') }}</div>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-center">
                        <div class="text-2xl font-bold text-green-600">{{ $stats['completed'] }}</div>
                        <div class="text-sm text-gray-600">{{ __('Completed') }}</div>
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <form method="GET" action="{{ route('admin.waitlist.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <x-input-label for="room_type" :value="__('Room Type')" />
                            <select id="room_type" name="room_type" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="">{{ __('All Room Types') }}</option>
                                @foreach($roomTypes as $roomType)
                                    <option value="{{ $roomType->id }}" {{ request('room_type') == $roomType->id ? 'selected' : '' }}>
                                        {{ $roomType->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <x-input-label for="status" :value="__('Status')" />
                            <select id="status" name="status" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="">{{ __('All Statuses') }}</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>{{ __('Pending') }}</option>
                                <option value="notified" {{ request('status') == 'notified' ? 'selected' : '' }}>{{ __('Notified') }}</option>
                                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>{{ __('Completed') }}</option>
                                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>{{ __('Cancelled') }}</option>
                            </select>
                        </div>

                        <div>
                            <x-input-label for="priority" :value="__('Priority')" />
                            <select id="priority" name="priority" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="">{{ __('All Priorities') }}</option>
                                <option value="high" {{ request('priority') == 'high' ? 'selected' : '' }}>{{ __('High') }}</option>
                                <option value="normal" {{ request('priority') == 'normal' ? 'selected' : '' }}>{{ __('Normal') }}</option>
                                <option value="low" {{ request('priority') == 'low' ? 'selected' : '' }}>{{ __('Low') }}</option>
                            </select>
                        </div>

                        <div class="flex items-end space-x-2">
                            <x-primary-button type="submit">
                                {{ __('Filter') }}
                            </x-primary-button>
                            <x-secondary-button onclick="window.location='{{ route('admin.waitlist.index') }}'">
                                {{ __('Clear') }}
                            </x-secondary-button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Waitlist Table -->
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
                                        {{ __('Guest Information') }}
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Room Type') }}
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Dates') }}
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Priority') }}
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Status') }}
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Wait Time') }}
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Actions') }}
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($waitlists as $waitlist)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-10 w-10">
                                                    <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                                        <span class="text-sm font-medium text-gray-700">
                                                            {{ substr($waitlist->guest_name, 0, 1) }}
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900">{{ $waitlist->guest_name }}</div>
                                                    <div class="text-sm text-gray-500">{{ $waitlist->guest_email }}</div>
                                                    <div class="text-sm text-gray-500">{{ $waitlist->guest_phone }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ $waitlist->roomType->name }}</div>
                                            <div class="text-sm text-gray-500">{{ $waitlist->number_of_guests }} {{ __('guests') }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">
                                                <div>{{ __('From') }}: {{ $waitlist->preferred_check_in->format('M d, Y') }}</div>
                                                <div>{{ __('To') }}: {{ $waitlist->preferred_check_out->format('M d, Y') }}</div>
                                                <div class="text-gray-500">{{ $waitlist->preferred_check_in->diffInDays($waitlist->preferred_check_out) }} {{ __('nights') }}</div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                @if($waitlist->priority === 'high') bg-red-100 text-red-800
                                                @elseif($waitlist->priority === 'normal') bg-yellow-100 text-yellow-800
                                                @else bg-green-100 text-green-800
                                                @endif">
                                                {{ ucfirst($waitlist->priority) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                @if($waitlist->status === 'pending') bg-yellow-100 text-yellow-800
                                                @elseif($waitlist->status === 'notified') bg-blue-100 text-blue-800
                                                @elseif($waitlist->status === 'completed') bg-green-100 text-green-800
                                                @else bg-red-100 text-red-800
                                                @endif">
                                                {{ ucfirst($waitlist->status) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $waitlist->created_at->diffForHumans() }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex space-x-2">
                                                @if($waitlist->status === 'pending')
                                                    <x-primary-button onclick="notifyGuest('{{ $waitlist->id }}')" class="text-xs py-1 px-2">
                                                        {{ __('Notify') }}
                                                    </x-primary-button>
                                                @endif

                                                @if(in_array($waitlist->status, ['pending', 'notified']))
                                                    <x-secondary-button onclick="markCompleted('{{ $waitlist->id }}')" class="text-xs py-1 px-2">
                                                        {{ __('Complete') }}
                                                    </x-secondary-button>
                                                @endif

                                                <x-secondary-button onclick="viewDetails('{{ $waitlist->id }}')" class="text-xs py-1 px-2">
                                                    {{ __('Details') }}
                                                </x-secondary-button>

                                                @if($waitlist->status !== 'completed')
                                                    <form method="POST" action="{{ route('admin.waitlist.destroy', $waitlist) }}" class="inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <x-danger-button type="submit" 
                                                                       onclick="return confirm('{{ __('Are you sure you want to remove this waitlist entry?') }}')"
                                                                       class="text-xs py-1 px-2">
                                                            {{ __('Remove') }}
                                                        </x-danger-button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                                            {{ __('No waitlist entries found.') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($waitlists->hasPages())
                        <div class="mt-6">
                            {{ $waitlists->withQueryString()->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Notification Modal -->
    <x-modal name="notify-guest" :show="false" maxWidth="lg">
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900">
                {{ __('Notify Guest') }}
            </h2>
            <p class="mt-1 text-sm text-gray-600">
                {{ __('Send a notification to the guest about room availability.') }}
            </p>
            
            <div class="mt-4">
                <x-input-label for="notification_message" :value="__('Message')" />
                <textarea id="notification_message" rows="4" 
                        class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                        placeholder="{{ __('Enter notification message...') }}">{{ __('Good news! A room matching your preferences is now available. Please contact us to confirm your booking.') }}</textarea>
            </div>

            <div class="mt-4 flex items-center">
                <input id="send_email" type="checkbox" checked class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                <x-input-label for="send_email" :value="__('Send Email Notification')" class="ml-2" />
            </div>

            <div class="mt-4 flex items-center">
                <input id="send_sms" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                <x-input-label for="send_sms" :value="__('Send SMS Notification')" class="ml-2" />
            </div>

            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close')">
                    {{ __('Cancel') }}
                </x-secondary-button>
                <x-primary-button class="ml-3" onclick="submitNotification()">
                    {{ __('Send Notification') }}
                </x-primary-button>
            </div>
        </div>
    </x-modal>

    <!-- Details Modal -->
    <x-modal name="waitlist-details" :show="false" maxWidth="lg">
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900">
                {{ __('Waitlist Details') }}
            </h2>
            <div id="waitlist-details-content">
                <!-- Details will be loaded via JavaScript -->
            </div>
            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close')">
                    {{ __('Close') }}
                </x-secondary-button>
            </div>
        </div>
    </x-modal>

    <script>
        let currentWaitlistId = null;

        function notifyGuest(waitlistId) {
            currentWaitlistId = waitlistId;
            window.dispatchEvent(new CustomEvent('open-modal', { detail: 'notify-guest' }));
        }

        function markCompleted(waitlistId) {
            if (confirm('{{ __("Are you sure you want to mark this waitlist entry as completed?") }}')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/admin/waitlist/${waitlistId}/complete`;
                
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

        function viewDetails(waitlistId) {
            // You would fetch details via AJAX here
            fetch(`/admin/waitlist/${waitlistId}/details`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('waitlist-details-content').innerHTML = `
                        <div class="mt-4 space-y-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">{{ __('Guest Name') }}</dt>
                                    <dd class="mt-1 text-sm text-gray-900">${data.guest_name}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">{{ __('Email') }}</dt>
                                    <dd class="mt-1 text-sm text-gray-900">${data.guest_email}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">{{ __('Phone') }}</dt>
                                    <dd class="mt-1 text-sm text-gray-900">${data.guest_phone}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">{{ __('Number of Guests') }}</dt>
                                    <dd class="mt-1 text-sm text-gray-900">${data.number_of_guests}</dd>
                                </div>
                            </div>
                            ${data.special_requests ? `
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">{{ __('Special Requests') }}</dt>
                                    <dd class="mt-1 text-sm text-gray-900">${data.special_requests}</dd>
                                </div>
                            ` : ''}
                        </div>
                    `;
                    window.dispatchEvent(new CustomEvent('open-modal', { detail: 'waitlist-details' }));
                })
                .catch(error => {
                    alert('{{ __("Error loading details") }}');
                });
        }

        function submitNotification() {
            if (currentWaitlistId) {
                const message = document.getElementById('notification_message').value;
                const sendEmail = document.getElementById('send_email').checked;
                const sendSms = document.getElementById('send_sms').checked;
                
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/admin/waitlist/${currentWaitlistId}/notify`;
                
                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = '{{ csrf_token() }}';
                
                const methodField = document.createElement('input');
                methodField.type = 'hidden';
                methodField.name = '_method';
                methodField.value = 'PATCH';
                
                const messageField = document.createElement('input');
                messageField.type = 'hidden';
                messageField.name = 'message';
                messageField.value = message;
                
                const emailField = document.createElement('input');
                emailField.type = 'hidden';
                emailField.name = 'send_email';
                emailField.value = sendEmail ? '1' : '0';
                
                const smsField = document.createElement('input');
                smsField.type = 'hidden';
                smsField.name = 'send_sms';
                smsField.value = sendSms ? '1' : '0';
                
                form.appendChild(csrfToken);
                form.appendChild(methodField);
                form.appendChild(messageField);
                form.appendChild(emailField);
                form.appendChild(smsField);
                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
</x-app-layout>
