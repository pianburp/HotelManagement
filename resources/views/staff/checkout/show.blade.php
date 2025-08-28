<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Check-out') }}</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-medium mb-2">{{ $booking->guest_name ?? $booking->user->name }}</h3>
                <p class="text-sm text-gray-500">{{ __('Room') }}: {{ $booking->room->room_number ?? '-' }}</p>
                <p class="mt-4">{{ __('Check-out Date') }}: {{ $booking->check_out->format('M d, Y') }}</p>

                <form method="POST" action="{{ route('staff.checkout.process', $booking) }}" class="mt-6">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">{{ __('Room Condition') }}</label>
                        <select name="room_condition" class="mt-1 block w-full" required>
                            <option value="good">{{ __('Good') }}</option>
                            <option value="needs_maintenance">{{ __('Needs Maintenance') }}</option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">{{ __('Damages (if any)') }}</label>
                        <input type="text" name="damages" class="mt-1 block w-full" placeholder="Optional" />
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">{{ __('Additional Charges') }}</label>
                        <input type="number" name="additional_charges" min="0" step="0.01" class="mt-1 block w-full" placeholder="0.00" />
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">{{ __('Notes') }}</label>
                        <textarea name="notes" class="mt-1 block w-full" rows="2"></textarea>
                    </div>
                    <x-primary-button>{{ __('Process Check-out') }}</x-primary-button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
