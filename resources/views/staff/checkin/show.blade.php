<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Check-in') }}</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-medium mb-2">{{ $booking->guest_name ?? $booking->user->name }}</h3>
                <p class="text-sm text-gray-500">{{ __('Room') }}: {{ $booking->room->room_number ?? '-' }}</p>
                <p class="mt-4">{{ __('Check-in Date') }}: {{ $booking->check_in->format('M d, Y') }}</p>

                <form method="POST" action="{{ route('staff.checkin.process', $booking) }}" class="mt-6">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">{{ __('Actual Number of Guests') }}</label>
                        <input type="number" name="actual_guests" min="1" max="{{ $booking->room->roomType->max_occupancy }}" value="{{ $booking->number_of_guests }}" class="mt-1 block w-full" required />
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">{{ __('Special Requests / Notes') }}</label>
                        <textarea name="notes" class="mt-1 block w-full" rows="2">{{ old('notes', $booking->special_requests) }}</textarea>
                    </div>
                    <div class="mb-4">
                        <label class="inline-flex items-center">
                            <input type="checkbox" name="id_verified" value="1" required class="form-checkbox" />
                            <span class="ml-2">{{ __('ID Verified') }}</span>
                        </label>
                    </div>
                    <div class="mb-4">
                        <label class="inline-flex items-center">
                            <input type="checkbox" name="payment_confirmed" value="1" required class="form-checkbox" />
                            <span class="ml-2">{{ __('Payment Confirmed') }}</span>
                        </label>
                    </div>
                    <x-primary-button>{{ __('Process Check-in') }}</x-primary-button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
