<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Waitlist') }}</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-medium mb-4">{{ __('Waitlist Entries') }}</h3>
                <div class="space-y-4">
                    @forelse($entries ?? [] as $entry)
                        <div class="border p-4 rounded-md flex items-center justify-between">
                            <div>
                                <div class="font-medium">{{ $entry->user->name ?? $entry->guest_name }}</div>
                                <div class="text-sm text-gray-500">{{ __('Desired Room Type') }}: {{ $entry->roomType->name ?? '-' }}</div>
                            </div>
                            <div class="flex gap-2">
                                <form method="POST" action="{{ route('staff.waitlist.notify', $entry) }}">
                                    @csrf
                                    @method('PATCH')
                                    <x-primary-button>{{ __('Notify') }}</x-primary-button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div class="text-gray-500">{{ __('No waitlist entries.') }}</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
