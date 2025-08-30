<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Real-Time Occupancy Report') }}</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium">{{ __('Current Occupancy Data') }}</h3>
                    <div class="text-sm text-gray-500">
                        {{ __('Last updated') }}: {{ now()->format('M d, Y H:i') }}
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Room') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Status') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Guest Details') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Stay Info') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($occupancyData as $room)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $room['room_number'] }}</div>
                                        <div class="text-xs text-gray-500">{{ $room['room_type'] }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        @php
                                            $statusColors = [
                                                'available' => 'bg-green-100 text-green-800',
                                                'reserved' => 'bg-yellow-100 text-yellow-800',
                                                'onboard' => 'bg-blue-100 text-blue-800',
                                                'closed' => 'bg-red-100 text-red-800',
                                            ];
                                            $statusLabels = [
                                                'available' => 'Available',
                                                'reserved' => 'Reserved',
                                                'onboard' => 'Occupied',
                                                'closed' => 'Closed',
                                            ];
                                            $statusClass = $statusColors[$room['status']] ?? 'bg-gray-100 text-gray-800';
                                            $statusLabel = $statusLabels[$room['status']] ?? ucfirst($room['status']);
                                        @endphp
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $statusClass }}">
                                            {{ __($statusLabel) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($room['guest_name'])
                                            <div class="text-sm font-medium text-gray-900">{{ $room['guest_name'] }}</div>
                                            @if($room['guest_email'])
                                                <div class="text-xs text-gray-500">{{ $room['guest_email'] }}</div>
                                            @endif
                                            @if($room['guest_phone'])
                                                <div class="text-xs text-gray-500">{{ $room['guest_phone'] }}</div>
                                            @endif
                                            @if($room['guests_count'])
                                                <div class="text-xs text-gray-500">{{ $room['guests_count'] }} {{ __('guest(s)') }}</div>
                                            @endif
                                        @else
                                            <div class="text-sm text-gray-500">-</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($room['check_in_date'])
                                            <div class="text-xs text-gray-900">
                                                <strong>{{ __('Check-in') }}:</strong> {{ $room['check_in_date'] }}
                                            </div>
                                            <div class="text-xs text-gray-900">
                                                <strong>{{ __('Check-out') }}:</strong> {{ $room['check_out_date'] }}
                                            </div>
                                            @if($room['days_remaining'] !== null)
                                                <div class="text-xs text-gray-500 mt-1">
                                                    {{ $room['days_remaining'] }} {{ __('day(s) remaining') }}
                                                </div>
                                            @endif
                                        @else
                                            <div class="text-xs text-gray-500">-</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        @if($room['booking_reference'])
                                            <button onclick="showGuestDetails('{{ $room['room_number'] }}', @js($room))" 
                                                    class="text-indigo-600 hover:text-indigo-900 text-xs">
                                                {{ __('View Details') }}
                                            </button>
                                        @else
                                            <span class="text-gray-400 text-xs">{{ __('No booking') }}</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
                                        {{ __('No room data available.') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Summary Cards -->
                <div class="mt-6 grid grid-cols-1 md:grid-cols-5 gap-4">
                    <div class="bg-blue-50 p-4 rounded-lg">
                        <div class="text-2xl font-bold text-blue-600">{{ $statistics['occupied'] ?? 0 }}</div>
                        <div class="text-sm text-blue-600">{{ __('Occupied') }}</div>
                    </div>
                    <div class="bg-green-50 p-4 rounded-lg">
                        <div class="text-2xl font-bold text-green-600">{{ $statistics['available'] ?? 0 }}</div>
                        <div class="text-sm text-green-600">{{ __('Available') }}</div>
                    </div>
                    <div class="bg-yellow-50 p-4 rounded-lg">
                        <div class="text-2xl font-bold text-yellow-600">{{ $statistics['reserved'] ?? 0 }}</div>
                        <div class="text-sm text-yellow-600">{{ __('Reserved') }}</div>
                    </div>
                    <div class="bg-red-50 p-4 rounded-lg">
                        <div class="text-2xl font-bold text-red-600">{{ $statistics['closed'] ?? 0 }}</div>
                        <div class="text-sm text-red-600">{{ __('Closed') }}</div>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <div class="text-2xl font-bold text-gray-600">{{ $statistics['occupancy_rate'] ?? 0 }}%</div>
                        <div class="text-sm text-gray-600">{{ __('Occupancy Rate') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Guest Details Modal -->
    <div id="guestModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900" id="modalTitle">{{ __('Guest Details') }}</h3>
                    <button onclick="closeGuestModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                
                <div id="modalContent" class="space-y-4">
                    <!-- Content will be populated by JavaScript -->
                </div>
                
                <div class="flex justify-end mt-6">
                    <button onclick="closeGuestModal()" 
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                        {{ __('Close') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function showGuestDetails(roomNumber, roomData) {
            document.getElementById('modalTitle').textContent = `{{ __('Guest Details - Room') }} ${roomNumber}`;
            
            const content = document.getElementById('modalContent');
            content.innerHTML = `
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-3">
                        <h4 class="font-semibold text-gray-900">{{ __('Guest Information') }}</h4>
                        <div class="space-y-2 text-sm">
                            <div><strong>{{ __('Name') }}:</strong> ${roomData.guest_name || '-'}</div>
                            <div><strong>{{ __('Email') }}:</strong> ${roomData.guest_email || '-'}</div>
                            <div><strong>{{ __('Phone') }}:</strong> ${roomData.guest_phone || '-'}</div>
                            <div><strong>{{ __('Number of Guests') }}:</strong> ${roomData.guests_count || '-'}</div>
                        </div>
                    </div>
                    
                    <div class="space-y-3">
                        <h4 class="font-semibold text-gray-900">{{ __('Booking Information') }}</h4>
                        <div class="space-y-2 text-sm">
                            <div><strong>{{ __('Booking Reference') }}:</strong> ${roomData.booking_reference || '-'}</div>
                            <div><strong>{{ __('Check-in Date') }}:</strong> ${roomData.check_in_date || '-'}</div>
                            <div><strong>{{ __('Check-out Date') }}:</strong> ${roomData.check_out_date || '-'}</div>
                            <div><strong>{{ __('Total Amount') }}:</strong> ${roomData.total_amount ? 'RM' + roomData.total_amount : '-'}</div>
                            <div><strong>{{ __('Booking Source') }}:</strong> ${roomData.booking_source || '-'}</div>
                        </div>
                    </div>
                </div>
                
                ${roomData.special_requests ? `
                    <div class="mt-4">
                        <h4 class="font-semibold text-gray-900 mb-2">{{ __('Special Requests') }}</h4>
                        <div class="bg-gray-50 p-3 rounded text-sm">
                            ${roomData.special_requests}
                        </div>
                    </div>
                ` : ''}
            `;
            
            document.getElementById('guestModal').classList.remove('hidden');
        }
        
        function closeGuestModal() {
            document.getElementById('guestModal').classList.add('hidden');
        }
        
        // Close modal when clicking outside
        document.getElementById('guestModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeGuestModal();
            }
        });
    </script>
</x-app-layout>
