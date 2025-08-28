<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Check-outs') }}</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                @if(session('success'))
                    <script>
                        window.addEventListener('load', function() {
                            alert(@json(session('success')));
                        });
                    </script>
                @endif
                <h3 class="text-lg font-medium mb-4">{{ __('Pending Check-outs') }}</h3>
                <div class="space-y-4">
                    @forelse($checkOuts ?? [] as $booking)
                        <div class="border p-4 rounded-md flex items-center justify-between">
                            <div>
                                <div class="font-medium">{{ $booking->guest_name ?? $booking->user->name }}</div>
                                <div class="text-sm text-gray-500">{{ __('Room') }}: {{ $booking->room->room_number ?? '-' }}</div>
                                <div class="text-xs text-gray-400">{{ __('Check-out') }}: {{ $booking->check_out_date->format('M d, Y') }}</div>
                            </div>
                            <div class="flex gap-2">
                                <a href="{{ route('staff.checkout.show', $booking) }}" class="text-indigo-600">{{ __('View') }}</a>
                            </div>
                        </div>
                    @empty
                        <div class="text-gray-500">{{ __('No pending check-outs.') }}</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
