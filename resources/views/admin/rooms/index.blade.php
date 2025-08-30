<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Room Management') }}
            </h2>
            <x-primary-button onclick="window.location='{{ route('admin.rooms.create') }}'">
                {{ __('Add New Room') }}
            </x-primary-button>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Filters -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <form method="GET" action="{{ route('admin.rooms.index') }}" class="space-y-4">
                        <!-- Search Bar -->
                        <div class="col-span-full">
                            <x-input-label for="search" :value="__('Search Rooms')" />
                            <x-text-input id="search" class="block mt-1 w-full" type="text" name="search" 
                                        :value="request('search')" placeholder="{{ __('Search by room number, notes, or room type...') }}" />
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
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
                                    <option value="available" {{ request('status') == 'available' ? 'selected' : '' }}>{{ __('Available') }}</option>
                                    <option value="reserved" {{ request('status') == 'reserved' ? 'selected' : '' }}>{{ __('Reserved') }}</option>
                                    <option value="onboard" {{ request('status') == 'onboard' ? 'selected' : '' }}>{{ __('Onboard') }}</option>
                                    <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>{{ __('Closed') }}</option>
                                </select>
                            </div>

                            <div>
                                <x-input-label for="floor" :value="__('Floor')" />
                                <select id="floor" name="floor" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="">{{ __('All Floors') }}</option>
                                    @foreach($floors as $floor)
                                        <option value="{{ $floor }}" {{ request('floor') == $floor ? 'selected' : '' }}>
                                            {{ __('Floor') }} {{ $floor }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="flex items-end space-x-2">
                                <x-primary-button type="submit">
                                    {{ __('Filter') }}
                                </x-primary-button>
                                <x-secondary-button onclick="window.location='{{ route('admin.rooms.index') }}'">
                                    {{ __('Clear') }}
                                </x-secondary-button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-center">
                        <div class="text-2xl font-bold text-blue-600">{{ $stats['total'] }}</div>
                        <div class="text-sm text-gray-600">{{ __('Total Rooms') }}</div>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-center">
                        <div class="text-2xl font-bold text-green-600">{{ $stats['available'] }}</div>
                        <div class="text-sm text-gray-600">{{ __('Available') }}</div>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-center">
                        <div class="text-2xl font-bold text-yellow-600">{{ $stats['reserved'] }}</div>
                        <div class="text-sm text-gray-600">{{ __('Reserved') }}</div>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-center">
                        <div class="text-2xl font-bold text-red-600">{{ $stats['onboard'] }}</div>
                        <div class="text-sm text-gray-600">{{ __('Occupied') }}</div>
                    </div>
                </div>
            </div>

            <!-- Rooms Table -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if(session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                            {{ session('error') }}
                        </div>
                    @endif

                    <!-- Bulk Actions -->
                    <div class="mb-4 p-4 bg-gray-50 rounded-lg" id="bulk-actions" style="display: none;">
                        <form id="bulk-form" method="POST" action="{{ route('admin.rooms.bulk-status') }}" class="flex items-end space-x-4">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="room_ids" id="bulk-room-ids">
                            
                            <div>
                                <x-input-label for="bulk_status" :value="__('Change Status to')" />
                                <select id="bulk_status" name="status" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                    <option value="">{{ __('Select Status') }}</option>
                                    <option value="available">{{ __('Available') }}</option>
                                    <option value="closed">{{ __('Closed') }}</option>
                                </select>
                            </div>
                            
                            <div>
                                <x-input-label for="bulk_reason" :value="__('Reason')" />
                                <x-text-input id="bulk_reason" class="block mt-1 w-full" type="text" name="reason" 
                                            placeholder="{{ __('Reason for status change') }}" required />
                            </div>
                            
                            <x-primary-button type="submit">
                                {{ __('Update Selected') }}
                            </x-primary-button>
                            
                            <x-secondary-button type="button" onclick="clearSelection()">
                                {{ __('Cancel') }}
                            </x-secondary-button>
                        </form>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left">
                                        <input type="checkbox" id="select-all" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        <a href="{{ route('admin.rooms.index', array_merge(request()->query(), ['sort' => 'room_number', 'direction' => request('sort') == 'room_number' && request('direction') == 'asc' ? 'desc' : 'asc'])) }}" class="hover:text-gray-700">
                                            {{ __('Room Number') }}
                                            @if(request('sort') == 'room_number')
                                                <span class="ml-1">{{ request('direction') == 'asc' ? '↑' : '↓' }}</span>
                                            @endif
                                        </a>
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Room Type') }}
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        <a href="{{ route('admin.rooms.index', array_merge(request()->query(), ['sort' => 'floor_number', 'direction' => request('sort') == 'floor_number' && request('direction') == 'asc' ? 'desc' : 'asc'])) }}" class="hover:text-gray-700">
                                            {{ __('Floor') }}
                                            @if(request('sort') == 'floor_number')
                                                <span class="ml-1">{{ request('direction') == 'asc' ? '↑' : '↓' }}</span>
                                            @endif
                                        </a>
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        <a href="{{ route('admin.rooms.index', array_merge(request()->query(), ['sort' => 'status', 'direction' => request('sort') == 'status' && request('direction') == 'asc' ? 'desc' : 'asc'])) }}" class="hover:text-gray-700">
                                            {{ __('Status') }}
                                            @if(request('sort') == 'status')
                                                <span class="ml-1">{{ request('direction') == 'asc' ? '↑' : '↓' }}</span>
                                            @endif
                                        </a>
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Current Booking') }}
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        <a href="{{ route('admin.rooms.index', array_merge(request()->query(), ['sort' => 'updated_at', 'direction' => request('sort') == 'updated_at' && request('direction') == 'asc' ? 'desc' : 'asc'])) }}" class="hover:text-gray-700">
                                            {{ __('Last Updated') }}
                                            @if(request('sort') == 'updated_at')
                                                <span class="ml-1">{{ request('direction') == 'asc' ? '↑' : '↓' }}</span>
                                            @endif
                                        </a>
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Actions') }}
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($rooms as $room)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <input type="checkbox" class="room-checkbox rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" 
                                                   value="{{ $room->id }}" data-room-id="{{ $room->id }}">
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ $room->room_number }}</div>
                                            @if($room->smoking_allowed)
                                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                    {{ __('Smoking') }}
                                                </span>
                                            @endif
                                            @if($room->size)
                                                <div class="text-xs text-gray-500">{{ $room->size }} m²</div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ $room->roomType->name }}</div>
                                            <div class="text-sm text-gray-500">{{ $room->roomType->code }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $room->floor_number }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                @if($room->status === 'available') bg-green-100 text-green-800
                                                @elseif($room->status === 'reserved') bg-yellow-100 text-yellow-800
                                                @elseif($room->status === 'onboard') bg-blue-100 text-blue-800
                                                @else bg-red-100 text-red-800
                                                @endif">
                                                {{ ucfirst($room->status) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            @if($room->currentBooking)
                                                <div class="text-sm">
                                                    <div class="font-medium">{{ $room->currentBooking->guest_name }}</div>
                                                    <div class="text-gray-500">
                                                        {{ $room->currentBooking->check_in->format('M d') }} - 
                                                        {{ $room->currentBooking->check_out->format('M d') }}
                                                    </div>
                                                </div>
                                            @elseif($room->upcomingBooking)
                                                <div class="text-sm">
                                                    <div class="font-medium text-gray-700">{{ __('Upcoming:') }} {{ $room->upcomingBooking->guest_name ?? __('(TBD)') }}</div>
                                                    <div class="text-gray-500">
                                                        {{ $room->upcomingBooking->check_in->format('M d') }} - 
                                                        {{ $room->upcomingBooking->check_out->format('M d') }}
                                                    </div>
                                                </div>
                                            @else
                                                <span class="text-gray-400">{{ __('No active booking') }}</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $room->updated_at->diffForHumans() }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex space-x-2">
                                                <x-secondary-button onclick="window.location='{{ route('admin.rooms.show', $room) }}'" class="text-xs py-1 px-2">
                                                    {{ __('View') }}
                                                </x-secondary-button>
                                                <x-secondary-button onclick="window.location='{{ route('admin.rooms.edit', $room) }}'" class="text-xs py-1 px-2">
                                                    {{ __('Edit') }}
                                                </x-secondary-button>
                                                @if($room->status !== 'onboard')
                                                    <form method="POST" action="{{ route('admin.rooms.destroy', $room) }}" class="inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <x-danger-button type="submit" 
                                                                       onclick="return confirm('{{ __('Are you sure you want to delete this room?') }}')"
                                                                       class="text-xs py-1 px-2">
                                                            {{ __('Delete') }}
                                                        </x-danger-button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="px-6 py-4 text-center text-gray-500">
                                            {{ __('No rooms found.') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($rooms->hasPages())
                        <div class="mt-6">
                            {{ $rooms->withQueryString()->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript for bulk operations -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const selectAllCheckbox = document.getElementById('select-all');
            const roomCheckboxes = document.querySelectorAll('.room-checkbox');
            const bulkActions = document.getElementById('bulk-actions');
            const bulkForm = document.getElementById('bulk-form');
            const bulkRoomIds = document.getElementById('bulk-room-ids');

            // Select all functionality
            selectAllCheckbox.addEventListener('change', function() {
                roomCheckboxes.forEach(checkbox => {
                    checkbox.checked = this.checked;
                });
                updateBulkActions();
            });

            // Individual checkbox functionality
            roomCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    updateBulkActions();
                    updateSelectAllState();
                });
            });

            function updateSelectAllState() {
                const checkedBoxes = document.querySelectorAll('.room-checkbox:checked');
                selectAllCheckbox.checked = checkedBoxes.length === roomCheckboxes.length;
                selectAllCheckbox.indeterminate = checkedBoxes.length > 0 && checkedBoxes.length < roomCheckboxes.length;
            }

            function updateBulkActions() {
                const checkedBoxes = document.querySelectorAll('.room-checkbox:checked');
                
                if (checkedBoxes.length > 0) {
                    const roomIds = Array.from(checkedBoxes).map(cb => cb.value);
                    bulkRoomIds.value = JSON.stringify(roomIds);
                    bulkActions.style.display = 'block';
                } else {
                    bulkActions.style.display = 'none';
                }
            }

            // Clear selection function
            window.clearSelection = function() {
                roomCheckboxes.forEach(checkbox => {
                    checkbox.checked = false;
                });
                selectAllCheckbox.checked = false;
                selectAllCheckbox.indeterminate = false;
                bulkActions.style.display = 'none';
            };

            // Bulk form submission
            bulkForm.addEventListener('submit', function(e) {
                const checkedBoxes = document.querySelectorAll('.room-checkbox:checked');
                const selectedCount = checkedBoxes.length;
                
                if (selectedCount === 0) {
                    e.preventDefault();
                    alert('{{ __("Please select at least one room.") }}');
                    return;
                }

                const status = document.getElementById('bulk_status').value;
                const reason = document.getElementById('bulk_reason').value.trim();

                if (!status || !reason) {
                    e.preventDefault();
                    alert('{{ __("Please select a status and provide a reason.") }}');
                    return;
                }

                if (!confirm(`{{ __("Are you sure you want to update the status of") }} ${selectedCount} {{ __("room(s)?") }}`)) {
                    e.preventDefault();
                }
            });
        });
    </script>
</x-app-layout>
