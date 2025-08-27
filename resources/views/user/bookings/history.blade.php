<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('My Bookings') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Filter Section -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form action="{{ route('user.bookings.history') }}" method="GET" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div>
                                <x-input-label for="status" :value="__('Status')" />
                                <select id="status" name="status" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                    <option value="">{{ __('All Statuses') }}</option>
                                    <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>{{ __('Confirmed') }}</option>
                                    <option value="checked_in" {{ request('status') == 'checked_in' ? 'selected' : '' }}>{{ __('Checked In') }}</option>
                                    <option value="checked_out" {{ request('status') == 'checked_out' ? 'selected' : '' }}>{{ __('Checked Out') }}</option>
                                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>{{ __('Cancelled') }}</option>
                                </select>
                            </div>
                            <div>
                                <x-input-label for="date_from" :value="__('From Date')" />
                                <x-text-input id="date_from" type="date" name="date_from" 
                                    :value="request('date_from')" class="mt-1 block w-full" />
                            </div>
                            <div>
                                <x-input-label for="date_to" :value="__('To Date')" />
                                <x-text-input id="date_to" type="date" name="date_to" 
                                    :value="request('date_to')" class="mt-1 block w-full" />
                            </div>
                            <div class="flex items-end">
                                <x-primary-button>
                                    {{ __('Filter') }}
                                </x-primary-button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Bookings List -->
            <div class="space-y-6">
                @forelse ($bookings as $booking)
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex justify-between items-start mb-4">
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900">
                                        {{ $booking->room->roomType->name }} - {{ __('Room') }} {{ $booking->room->room_number }}
                                    </h3>
                                    <p class="text-sm text-gray-600">{{ __('Booking') }} #{{ $booking->id }}</p>
                                </div>
                                <div class="text-right">
                                    @php
                                        $statusColors = [
                                            'confirmed' => 'bg-blue-100 text-blue-800',
                                            'checked_in' => 'bg-green-100 text-green-800',
                                            'checked_out' => 'bg-gray-100 text-gray-800',
                                            'cancelled' => 'bg-red-100 text-red-800',
                                        ];
                                        $statusColor = $statusColors[$booking->status] ?? 'bg-gray-100 text-gray-800';
                                    @endphp
                                    <span class="{{ $statusColor }} px-2 py-1 rounded-full text-xs font-medium">
                                        {{ __(ucfirst($booking->status)) }}
                                    </span>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
                                <div>
                                    <p class="text-sm text-gray-600">{{ __('Check-in') }}</p>
                                    <p class="font-medium">{{ $booking->check_in->format('M d, Y') }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">{{ __('Check-out') }}</p>
                                    <p class="font-medium">{{ $booking->check_out->format('M d, Y') }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">{{ __('Guests') }}</p>
                                    <p class="font-medium">{{ $booking->number_of_guests }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">{{ __('Total Amount') }}</p>
                                    <p class="font-medium">{{ money($booking->total_amount) }}</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div>
                                    <p class="text-sm text-gray-600">{{ __('Booked on') }}</p>
                                    <p class="font-medium">{{ $booking->created_at->format('M d, Y H:i') }}</p>
                                </div>
                                @if($booking->special_requests)
                                <div>
                                    <p class="text-sm text-gray-600">{{ __('Special Requests') }}</p>
                                    <p class="font-medium">{{ $booking->special_requests }}</p>
                                </div>
                                @endif
                            </div>

                            <!-- Actions -->
                            <div class="flex justify-between items-center border-t pt-4">
                                <div class="flex space-x-3">
                                    <a href="{{ route('user.bookings.show', $booking) }}" 
                                       class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">
                                        {{ __('View Details') }}
                                    </a>
                                    
                                    @if($booking->status === 'confirmed' && $booking->check_in->isFuture())
                                        <button class="text-red-600 hover:text-red-900 text-sm font-medium"
                                                onclick="if(confirm('{{ __('Are you sure you want to cancel this booking?') }}')) { document.getElementById('cancel-form-{{ $booking->id }}').submit(); }">
                                            {{ __('Cancel Booking') }}
                                        </button>
                                        <form id="cancel-form-{{ $booking->id }}" action="{{ route('user.bookings.cancel', $booking) }}" method="POST" class="hidden">
                                            @csrf
                                            @method('PATCH')
                                        </form>
                                    @endif
                                </div>
                                
                                <div class="text-sm text-gray-500">
                                    {{ $booking->check_in->diffForHumans() }}
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-center">
                            <div class="mb-4">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">{{ __('No bookings found') }}</h3>
                            <p class="text-gray-600 mb-4">{{ __('You haven\'t made any bookings yet.') }}</p>
                            <x-primary-button onclick="window.location='{{ route('user.rooms.index') }}'">
                                {{ __('Browse Rooms') }}
                            </x-primary-button>
                        </div>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if ($bookings->hasPages())
                <div class="mt-6">
                    {{ $bookings->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
