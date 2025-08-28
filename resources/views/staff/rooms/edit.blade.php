<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Edit Room') }}</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('staff.rooms.update', $room) }}">
                    @csrf
                    @method('PATCH')

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">{{ __('Room Number') }}</label>
                        <input type="text" name="room_number" value="{{ old('room_number', $room->room_number) }}" class="mt-1 block w-full" />
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">{{ __('Status') }}</label>
                        <select name="status" class="mt-1 block w-full">
                            <option value="available" {{ $room->status=='available' ? 'selected' : '' }}>{{ __('Available') }}</option>
                            <option value="reserved" {{ $room->status=='reserved' ? 'selected' : '' }}>{{ __('Reserved') }}</option>
                            <option value="onboard" {{ $room->status=='onboard' ? 'selected' : '' }}>{{ __('Onboard') }}</option>
                            <option value="closed" {{ $room->status=='closed' ? 'selected' : '' }}>{{ __('Closed') }}</option>
                        </select>
                    </div>

                    <div class="flex justify-end">
                        <x-primary-button>{{ __('Save') }}</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
