<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('My Waitlist') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Header Actions -->
            <div class="mb-6 flex justify-between items-center">
                <div>
                    <p class="text-gray-600">{{ __('You are on the waitlist for the following room types. You will be notified when a room becomes available.') }}</p>
                </div>
                <div>
                    <x-primary-button onclick="window.location='{{ route('user.rooms.index') }}'">
                        {{ __('Browse Rooms') }}
                    </x-primary-button>
                </div>
            </div>

            <!-- Waitlist Items -->
            <div class="space-y-6">
                @forelse ($waitlistItems as $waitlist)
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex justify-between items-start mb-4">
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900">
                                        {{ $waitlist->roomType->name }}
                                    </h3>
                                    <p class="text-sm text-gray-600">{{ __('Added to waitlist on') }} {{ $waitlist->created_at->format('M d, Y H:i') }}</p>
                                </div>
                                <div class="text-right">
                                    @php
                                        $statusColors = [
                                            'active' => 'bg-blue-100 text-blue-800',
                                            'notified' => 'bg-green-100 text-green-800',
                                            'expired' => 'bg-red-100 text-red-800',
                                        ];
                                        $statusColor = $statusColors[$waitlist->status] ?? 'bg-gray-100 text-gray-800';
                                    @endphp
                                    <span class="{{ $statusColor }} px-3 py-1 rounded-full text-xs font-medium">
                                        {{ __(ucfirst($waitlist->status)) }}
                                    </span>
                                </div>
                            </div>

                            <!-- Waitlist Details -->
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
                                <div>
                                    <p class="text-sm text-gray-600">{{ __('Check-in Date') }}</p>
                                    <p class="font-medium">{{ $waitlist->preferred_check_in->format('M d, Y') }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">{{ __('Check-out Date') }}</p>
                                    <p class="font-medium">{{ $waitlist->preferred_check_out->format('M d, Y') }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">{{ __('Guests') }}</p>
                                    <p class="font-medium">{{ $waitlist->number_of_guests }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">{{ __('Max Price') }}</p>
                                    <p class="font-medium">{{ money($waitlist->max_price) }}</p>
                                </div>
                            </div>

                            <!-- Room Type Information -->
                            <div class="bg-gray-50 rounded-lg p-4 mb-4">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        @if($waitlist->roomType->getFirstMediaUrl('images'))
                                            <img src="{{ $waitlist->roomType->getFirstMediaUrl('images') }}" 
                                                 alt="{{ $waitlist->roomType->name }}"
                                                 class="w-full h-32 object-cover rounded-lg">
                                        @else
                                            <div class="w-full h-32 bg-gray-200 flex items-center justify-center rounded-lg">
                                                <span class="text-gray-400 text-sm">{{ __('No image available') }}</span>
                                            </div>
                                        @endif
                                    </div>
                                    <div>
                                        <h4 class="font-medium text-gray-900 mb-2">{{ $waitlist->roomType->name }}</h4>
                                        <p class="text-sm text-gray-600 mb-2">{{ Str::limit($waitlist->roomType->description, 150) }}</p>
                                        <div class="text-sm text-gray-600">
                                            <span class="font-medium">{{ __('Base Price') }}:</span> {{ money($waitlist->roomType->base_price) }} / {{ __('night') }}
                                        </div>
                                        <div class="text-sm text-gray-600">
                                            <span class="font-medium">{{ __('Max Occupancy') }}:</span> {{ $waitlist->roomType->max_occupancy }} {{ __('guests') }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Contact Information -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div>
                                    <p class="text-sm text-gray-600">{{ __('Contact Name') }}</p>
                                    <p class="font-medium">{{ $waitlist->contact_name }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">{{ __('Contact Email') }}</p>
                                    <p class="font-medium">{{ $waitlist->contact_email }}</p>
                                </div>
                            </div>

                            @if($waitlist->special_requests)
                            <div class="mb-4">
                                <p class="text-sm text-gray-600">{{ __('Special Requests') }}</p>
                                <p class="font-medium">{{ $waitlist->special_requests }}</p>
                            </div>
                            @endif

                            <!-- Status Information -->
                            <div class="border-t pt-4">
                                @if($waitlist->status === 'active')
                                    <div class="flex justify-between items-center">
                                        <div class="text-sm text-gray-600">
                                            <p>{{ __('You are currently on the waitlist. We will notify you when a room becomes available.') }}</p>
                                            <p class="mt-1">{{ __('Priority in queue') }}: #{{ $loop->iteration }}</p>
                                        </div>
                                        <form action="{{ route('user.waitlist.destroy', $waitlist) }}" method="POST" 
                                              onsubmit="return confirm('{{ __('Are you sure you want to remove yourself from this waitlist?') }}')">
                                            @csrf
                                            @method('DELETE')
                                            <x-danger-button type="submit">
                                                {{ __('Remove from Waitlist') }}
                                            </x-danger-button>
                                        </form>
                                    </div>
                                @elseif($waitlist->status === 'notified')
                                    <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                                        <div class="flex">
                                            <div class="flex-shrink-0">
                                                <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                                </svg>
                                            </div>
                                            <div class="ml-3">
                                                <h3 class="text-sm font-medium text-green-800">{{ __('Room Available!') }}</h3>
                                                <div class="mt-2 text-sm text-green-700">
                                                    <p>{{ __('A room matching your criteria has become available. Please check your email for booking instructions.') }}</p>
                                                    @if($waitlist->notified_at)
                                                        <p class="mt-1">{{ __('Notified on') }}: {{ $waitlist->notified_at->format('M d, Y H:i') }}</p>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @elseif($waitlist->status === 'expired')
                                    <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                                        <div class="flex">
                                            <div class="flex-shrink-0">
                                                <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                                </svg>
                                            </div>
                                            <div class="ml-3">
                                                <h3 class="text-sm font-medium text-red-800">{{ __('Waitlist Expired') }}</h3>
                                                <div class="mt-2 text-sm text-red-700">
                                                    <p>{{ __('This waitlist entry has expired. You can create a new waitlist entry for future dates.') }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-center">
                            <div class="mb-4">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">{{ __('No waitlist entries') }}</h3>
                            <p class="text-gray-600 mb-4">{{ __('You are not currently on any waitlists.') }}</p>
                            <x-primary-button onclick="window.location='{{ route('user.rooms.index') }}'">
                                {{ __('Browse Rooms') }}
                            </x-primary-button>
                        </div>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if ($waitlistItems->hasPages())
                <div class="mt-6">
                    {{ $waitlistItems->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
