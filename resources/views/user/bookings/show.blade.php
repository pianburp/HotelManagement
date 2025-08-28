<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Booking Details') }} #{{ $booking->id }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <!-- Status Header -->
                    <div class="mb-6 flex justify-between items-center">
                        <div>
                            <h3 class="text-2xl font-bold text-gray-900">{{ __('Booking') }} #{{ $booking->id }}</h3>
                            <p class="text-gray-600">{{ __('Created on') }} {{ $booking->created_at->format('M d, Y H:i') }}</p>
                        </div>
                        <div>
                            @php
                                $statusColors = [
                                    'confirmed' => 'bg-blue-100 text-blue-800',
                                    'checked_in' => 'bg-green-100 text-green-800',
                                    'checked_out' => 'bg-gray-100 text-gray-800',
                                    'cancelled' => 'bg-red-100 text-red-800',
                                ];
                                $statusColor = $statusColors[$booking->status] ?? 'bg-gray-100 text-gray-800';
                            @endphp
                            <span class="{{ $statusColor }} px-4 py-2 rounded-lg text-sm font-medium">
                                {{ __(ucfirst($booking->status)) }}
                            </span>
                        </div>
                    </div>

                    <!-- Room Information -->
                    <div class="mb-8 border-b pb-6">
                        <h4 class="text-xl font-semibold text-gray-900 mb-4">{{ __('Room Information') }}</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                @if($booking->room->roomType->getFirstMediaUrl('images'))
                                    <img src="{{ $booking->room->roomType->getFirstMediaUrl('images') }}" 
                                         alt="{{ $booking->room->roomType->name }}"
                                         class="w-full h-48 object-cover rounded-lg">
                                @else
                                    <div class="w-full h-48 bg-gray-200 flex items-center justify-center rounded-lg">
                                        <span class="text-gray-400">{{ __('No image available') }}</span>
                                    </div>
                                @endif
                            </div>
                            <div class="space-y-3">
                                <div>
                                    <span class="text-gray-600 font-medium">{{ __('Room Type') }}:</span>
                                    <span class="ml-2 text-gray-900">{{ $booking->room->roomType->name }}</span>
                                </div>
                                <div>
                                    <span class="text-gray-600 font-medium">{{ __('Room Number') }}:</span>
                                    <span class="ml-2 text-gray-900">{{ $booking->room->room_number }}</span>
                                </div>
                                <div>
                                    <span class="text-gray-600 font-medium">{{ __('Floor') }}:</span>
                                    <span class="ml-2 text-gray-900">{{ $booking->room->floor_number }}</span>
                                </div>
                                <div>
                                    <span class="text-gray-600 font-medium">{{ __('Size') }}:</span>
                                    <span class="ml-2 text-gray-900">{{ $booking->room->size }} m²</span>
                                </div>
                                <div>
                                    <span class="text-gray-600 font-medium">{{ __('Type') }}:</span>
                                    <span class="ml-2 text-gray-900">{{ $booking->room->smoking_allowed ? __('Smoking') : __('Non-Smoking') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Booking Details -->
                    <div class="mb-8 border-b pb-6">
                        <h4 class="text-xl font-semibold text-gray-900 mb-4">{{ __('Booking Details') }}</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-3">
                                <div>
                                    <span class="text-gray-600 font-medium">{{ __('Check-in Date') }}:</span>
                                    <span class="ml-2 text-gray-900">{{ $booking->check_in->format('M d, Y') }}</span>
                                </div>
                                <div>
                                    <span class="text-gray-600 font-medium">{{ __('Check-out Date') }}:</span>
                                    <span class="ml-2 text-gray-900">{{ $booking->check_out->format('M d, Y') }}</span>
                                </div>
                                <div>
                                    <span class="text-gray-600 font-medium">{{ __('Duration') }}:</span>
                                    <span class="ml-2 text-gray-900">{{ $booking->check_in->diffInDays($booking->check_out) }} {{ __('nights') }}</span>
                                </div>
                            </div>
                            <div class="space-y-3">
                                <div>
                                    <span class="text-gray-600 font-medium">{{ __('Number of Guests') }}:</span>
                                    <span class="ml-2 text-gray-900">{{ $booking->number_of_guests }}</span>
                                </div>
                                <div>
                                    <span class="text-gray-600 font-medium">{{ __('Total Amount') }}:</span>
                                    <span class="ml-2 text-gray-900 text-xl font-bold">{{ money($booking->total_amount) }}</span>
                                </div>
                            </div>
                        </div>

                        @if($booking->special_requests)
                        <div class="mt-6">
                            <span class="text-gray-600 font-medium">{{ __('Special Requests') }}:</span>
                            <p class="mt-2 text-gray-900 bg-gray-50 p-3 rounded-lg">{{ $booking->special_requests }}</p>
                        </div>
                        @endif
                    </div>

                    <!-- Guest Information -->
                    <div class="mb-8 border-b pb-6">
                        <h4 class="text-xl font-semibold text-gray-900 mb-4">{{ __('Guest Information') }}</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-3">
                                <div>
                                    <span class="text-gray-600 font-medium">{{ __('Name') }}:</span>
                                    <span class="ml-2 text-gray-900">{{ $booking->guest_name }}</span>
                                </div>
                                <div>
                                    <span class="text-gray-600 font-medium">{{ __('Email') }}:</span>
                                    <span class="ml-2 text-gray-900">{{ $booking->guest_email }}</span>
                                </div>
                            </div>
                            <div class="space-y-3">
                                <div>
                                    <span class="text-gray-600 font-medium">{{ __('Phone') }}:</span>
                                    <span class="ml-2 text-gray-900">{{ $booking->guest_phone }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Information -->
                    @if($booking->payments->isNotEmpty())
                    <div class="mb-8 border-b pb-6">
                        <h4 class="text-xl font-semibold text-gray-900 mb-4">{{ __('Payment Information') }}</h4>
                        <div class="space-y-4">
                            @foreach($booking->payments as $payment)
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                        <div>
                                            <span class="text-gray-600 font-medium">{{ __('Amount') }}:</span>
                                            <span class="ml-2 text-gray-900">{{ money($payment->amount) }}</span>
                                        </div>
                                        <div>
                                            <span class="text-gray-600 font-medium">{{ __('Method') }}:</span>
                                            <span class="ml-2 text-gray-900">{{ __(ucfirst($payment->payment_method)) }}</span>
                                        </div>
                                        <div>
                                            <span class="text-gray-600 font-medium">{{ __('Status') }}:</span>
                                            @php
                                                $paymentStatusColors = [
                                                    'completed' => 'text-green-600',
                                                    'failed' => 'text-red-600',
                                                    'refunded' => 'text-orange-600',
                                                ];
                                                $paymentStatusColor = $paymentStatusColors[$payment->status] ?? 'text-gray-600';
                                            @endphp
                                            <span class="ml-2 {{ $paymentStatusColor }} font-medium">{{ __(ucfirst($payment->status)) }}</span>
                                        </div>
                                        <div>
                                            <span class="text-gray-600 font-medium">{{ __('Date') }}:</span>
                                            <span class="ml-2 text-gray-900">{{ $payment->created_at->format('M d, Y') }}</span>
                                        </div>
                                    </div>
                                    @if($payment->transaction_id)
                                    <div class="mt-2">
                                        <span class="text-gray-600 font-medium">{{ __('Transaction ID') }}:</span>
                                        <span class="ml-2 text-gray-900 font-mono text-sm">{{ $payment->transaction_id }}</span>
                                    </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- Actions -->
                    <div class="flex justify-between items-center">
                        <div class="flex space-x-4">
                            <a href="{{ route('user.bookings.history') }}" 
                               class="text-indigo-600 hover:text-indigo-900 font-medium">
                                ← {{ __('Back to bookings') }}
                            </a>
                        </div>
                        
                        <div class="flex space-x-4">
                            @if($booking->status === 'confirmed' && $booking->check_in->isFuture())
                                <x-danger-button onclick="if(confirm('{{ __('Are you sure you want to cancel this booking?') }}')) { document.getElementById('cancel-form').submit(); }">
                                    {{ __('Cancel Booking') }}
                                </x-danger-button>
                                <form id="cancel-form" action="{{ route('user.bookings.cancel', $booking) }}" method="POST" class="hidden">
                                    @csrf
                                    @method('PATCH')
                                </form>
                            @endif
                            
                            <x-secondary-button onclick="window.print()">
                                {{ __('Print Details') }}
                            </x-secondary-button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
