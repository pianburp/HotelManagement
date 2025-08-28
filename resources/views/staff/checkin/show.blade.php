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
                    <x-primary-button>{{ __('Process Check-in') }}</x-primary-button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
