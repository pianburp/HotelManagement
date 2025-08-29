<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Payment Management') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-center">
                        <div class="text-2xl font-bold text-green-600">{{ money($stats['total_revenue']) }}</div>
                        <div class="text-sm text-gray-600">{{ __('Total Revenue') }}</div>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-center">
                        <div class="text-2xl font-bold text-blue-600">{{ $stats['completed'] }}</div>
                        <div class="text-sm text-gray-600">{{ __('Completed') }}</div>
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
                        <div class="text-2xl font-bold text-red-600">{{ $stats['failed'] }}</div>
                        <div class="text-sm text-gray-600">{{ __('Failed') }}</div>
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <form method="GET" action="{{ route('admin.payments.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                        <div>
                            <x-input-label for="status" :value="__('Status')" />
                            <select id="status" name="status" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="">{{ __('All Statuses') }}</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>{{ __('Pending') }}</option>
                                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>{{ __('Completed') }}</option>
                                <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>{{ __('Failed') }}</option>
                                <option value="refunded" {{ request('status') == 'refunded' ? 'selected' : '' }}>{{ __('Refunded') }}</option>
                            </select>
                        </div>

                        <div>
                            <x-input-label for="payment_method" :value="__('Payment Method')" />
                            <select id="payment_method" name="payment_method" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="">{{ __('All Methods') }}</option>
                                <option value="credit_card" {{ request('payment_method') == 'credit_card' ? 'selected' : '' }}>{{ __('Credit Card') }}</option>
                                <option value="debit_card" {{ request('payment_method') == 'debit_card' ? 'selected' : '' }}>{{ __('Debit Card') }}</option>
                                <option value="cash" {{ request('payment_method') == 'cash' ? 'selected' : '' }}>{{ __('Cash') }}</option>
                                <option value="bank_transfer" {{ request('payment_method') == 'bank_transfer' ? 'selected' : '' }}>{{ __('Bank Transfer') }}</option>
                            </select>
                        </div>

                        <div>
                            <x-input-label for="date_from" :value="__('Date From')" />
                            <x-text-input id="date_from" class="block mt-1 w-full" type="date" name="date_from" 
                                        :value="request('date_from')" />
                        </div>

                        <div>
                            <x-input-label for="date_to" :value="__('Date To')" />
                            <x-text-input id="date_to" class="block mt-1 w-full" type="date" name="date_to" 
                                        :value="request('date_to')" />
                        </div>

                        <div class="flex items-end space-x-2">
                            <x-primary-button type="submit">
                                {{ __('Filter') }}
                            </x-primary-button>
                            <x-secondary-button onclick="window.location='{{ route('admin.payments.index') }}'">
                                {{ __('Clear') }}
                            </x-secondary-button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Payments Table -->
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
                                        {{ __('Transaction ID') }}
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Booking') }}
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Guest') }}
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Amount') }}
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Method') }}
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Status') }}
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Date') }}
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Actions') }}
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($payments as $payment)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">#{{ $payment->transaction_id }}</div>
                                            @if($payment->gateway_transaction_id)
                                                <div class="text-sm text-gray-500">{{ $payment->gateway_transaction_id }}</div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">
                                                <a href="{{ route('admin.bookings.show', $payment->booking) }}" class="text-indigo-600 hover:text-indigo-900">
                                                    #{{ $payment->booking->booking_number }}
                                                </a>
                                            </div>
                                            <div class="text-sm text-gray-500">{{ $payment->booking->room->roomType->name }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ $payment->booking->guest_name }}</div>
                                            <div class="text-sm text-gray-500">{{ $payment->booking->guest_email }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ money($payment->amount) }}</div>
                                            @if($payment->currency !== 'MYR')
                                                <div class="text-sm text-gray-500">{{ $payment->currency }}</div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium 
                                                    @if($payment->payment_method === 'credit_card') bg-blue-100 text-blue-800
                                                    @elseif($payment->payment_method === 'debit_card') bg-green-100 text-green-800
                                                    @elseif($payment->payment_method === 'cash') bg-yellow-100 text-yellow-800
                                                    @else bg-gray-100 text-gray-800
                                                    @endif">
                                                    {{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}
                                                </span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                @if($payment->payment_status === 'completed') bg-green-100 text-green-800
                                                @elseif($payment->payment_status === 'pending') bg-yellow-100 text-yellow-800
                                                @elseif($payment->payment_status === 'failed') bg-red-100 text-red-800
                                                @elseif($payment->payment_status === 'refunded') bg-purple-100 text-purple-800
                                                @else bg-gray-100 text-gray-800
                                                @endif">
                                                {{ ucfirst($payment->payment_status) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ $payment->created_at->format('M d, Y') }}</div>
                                            <div class="text-sm text-gray-500">{{ $payment->created_at->format('H:i') }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex space-x-2">
                                                <x-secondary-button onclick="viewPaymentDetails('{{ $payment->id }}')" class="text-xs py-1 px-2">
                                                    {{ __('Details') }}
                                                </x-secondary-button>
                                                
                                                @if($payment->payment_status === 'completed')
                                                    <x-secondary-button onclick="downloadReceipt('{{ $payment->id }}')" class="text-xs py-1 px-2">
                                                        {{ __('Receipt') }}
                                                    </x-secondary-button>
                                                    
                                                    <x-danger-button onclick="processRefund('{{ $payment->id }}')" class="text-xs py-1 px-2">
                                                        {{ __('Refund') }}
                                                    </x-danger-button>
                                                @endif

                                                @if($payment->payment_status === 'pending')
                                                    <x-primary-button onclick="markAsCompleted('{{ $payment->id }}')" class="text-xs py-1 px-2">
                                                        {{ __('Mark Paid') }}
                                                    </x-primary-button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="px-6 py-4 text-center text-gray-500">
                                            {{ __('No payments found.') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($payments->hasPages())
                        <div class="mt-6">
                            {{ $payments->withQueryString()->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Details Modal -->
    <x-modal name="payment-details" :show="false" maxWidth="lg">
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900">
                {{ __('Payment Details') }}
            </h2>
            <div id="payment-details-content">
                <!-- Details will be loaded via JavaScript -->
            </div>
            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close')">
                    {{ __('Close') }}
                </x-secondary-button>
            </div>
        </div>
    </x-modal>

    <!-- Refund Modal -->
    <x-modal name="refund-payment" :show="false" maxWidth="md">
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900">
                {{ __('Process Refund') }}
            </h2>
            <p class="mt-1 text-sm text-gray-600">
                {{ __('Are you sure you want to process a refund for this payment? This action cannot be undone.') }}
            </p>
            
            <div class="mt-4">
                <x-input-label for="refund_amount" :value="__('Refund Amount')" />
                <x-text-input id="refund_amount" class="block mt-1 w-full" type="number" step="0.01" 
                            placeholder="{{ __('Enter refund amount') }}" />
                <p class="mt-1 text-sm text-gray-600">{{ __('Leave empty to refund full amount') }}</p>
            </div>

            <div class="mt-4">
                <x-input-label for="refund_reason" :value="__('Refund Reason')" />
                <select id="refund_reason" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                    <option value="guest_request">{{ __('Guest Request') }}</option>
                    <option value="cancellation">{{ __('Booking Cancellation') }}</option>
                    <option value="overbooking">{{ __('Overbooking') }}</option>
                    <option value="service_issue">{{ __('Service Issue') }}</option>
                    <option value="other">{{ __('Other') }}</option>
                </select>
            </div>

            <div class="mt-4">
                <x-input-label for="refund_notes" :value="__('Notes (Optional)')" />
                <textarea id="refund_notes" rows="3" 
                        class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                        placeholder="{{ __('Additional notes about the refund...') }}"></textarea>
            </div>

            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close')">
                    {{ __('Cancel') }}
                </x-secondary-button>
                <x-danger-button class="ml-3" onclick="submitRefund()">
                    {{ __('Process Refund') }}
                </x-danger-button>
            </div>
        </div>
    </x-modal>

    <script>
        let currentPaymentId = null;

        function viewPaymentDetails(paymentId) {
            // You would fetch details via AJAX here
            fetch(`/admin/payments/${paymentId}/details`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('payment-details-content').innerHTML = `
                        <div class="mt-4 space-y-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">{{ __('Transaction ID') }}</dt>
                                    <dd class="mt-1 text-sm text-gray-900">${data.transaction_id}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">{{ __('Gateway Transaction ID') }}</dt>
                                    <dd class="mt-1 text-sm text-gray-900">${data.gateway_transaction_id || 'N/A'}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">{{ __('Amount') }}</dt>
                                    <dd class="mt-1 text-sm text-gray-900">$${data.amount}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">{{ __('Payment Method') }}</dt>
                                    <dd class="mt-1 text-sm text-gray-900">${data.payment_method}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">{{ __('Status') }}</dt>
                                    <dd class="mt-1 text-sm text-gray-900">${data.status}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">{{ __('Processed At') }}</dt>
                                    <dd class="mt-1 text-sm text-gray-900">${data.processed_at || 'N/A'}</dd>
                                </div>
                            </div>
                            ${data.notes ? `
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">{{ __('Notes') }}</dt>
                                    <dd class="mt-1 text-sm text-gray-900">${data.notes}</dd>
                                </div>
                            ` : ''}
                        </div>
                    `;
                    window.dispatchEvent(new CustomEvent('open-modal', { detail: 'payment-details' }));
                })
                .catch(error => {
                    alert('{{ __("Error loading payment details") }}');
                });
        }

        function downloadReceipt(paymentId) {
            window.open(`/admin/payments/${paymentId}/receipt`, '_blank');
        }

        function processRefund(paymentId) {
            currentPaymentId = paymentId;
            window.dispatchEvent(new CustomEvent('open-modal', { detail: 'refund-payment' }));
        }

        function markAsCompleted(paymentId) {
            if (confirm('{{ __("Are you sure you want to mark this payment as completed?") }}')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/admin/payments/${paymentId}/complete`;
                
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

        function submitRefund() {
            if (currentPaymentId) {
                const amount = document.getElementById('refund_amount').value;
                const reason = document.getElementById('refund_reason').value;
                const notes = document.getElementById('refund_notes').value;
                
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/admin/payments/${currentPaymentId}/refund`;
                
                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = '{{ csrf_token() }}';
                
                const methodField = document.createElement('input');
                methodField.type = 'hidden';
                methodField.name = '_method';
                methodField.value = 'POST';
                
                if (amount) {
                    const amountField = document.createElement('input');
                    amountField.type = 'hidden';
                    amountField.name = 'amount';
                    amountField.value = amount;
                    form.appendChild(amountField);
                }
                
                const reasonField = document.createElement('input');
                reasonField.type = 'hidden';
                reasonField.name = 'reason';
                reasonField.value = reason;
                
                const notesField = document.createElement('input');
                notesField.type = 'hidden';
                notesField.name = 'notes';
                notesField.value = notes;
                
                form.appendChild(csrfToken);
                form.appendChild(methodField);
                form.appendChild(reasonField);
                form.appendChild(notesField);
                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
</x-app-layout>
