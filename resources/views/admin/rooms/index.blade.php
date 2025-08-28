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
                    <form method="GET" action="{{ route('admin.rooms.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
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
                            <x-text-input id="floor" class="block mt-1 w-full" type="number" name="floor" 
                                        :value="request('floor')" placeholder="{{ __('Floor number') }}" />
                        </div>

                        <div class="flex items-end space-x-2">
                            <x-primary-button type="submit">
                                {{ __('Filter') }}
                            </x-primary-button>
                            <x-secondary-button onclick="window.location='{{ route('admin.rooms.index') }}'">
                                {{ __('Clear') }}
                            </x-secondary-button>
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
                        <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Room Number') }}
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Room Type') }}
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Floor') }}
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Status') }}
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Current Booking') }}
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Last Updated') }}
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
                                            <div class="text-sm font-medium text-gray-900">{{ $room->room_number }}</div>
                                            @if($room->smoking_allowed)
                                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                    {{ __('Smoking') }}
                                                </span>
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
                                        <td colspan="7" class="px-6 py-4 text-center text-gray-500">
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
</x-app-layout>
