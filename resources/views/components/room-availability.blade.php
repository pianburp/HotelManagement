@props(['room', 'checkIn' => null, 'checkOut' => null])

@php
    $isAvailable = $room->isAvailable($checkIn, $checkOut);
    $statusClass = $isAvailable ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800';
    $statusText = $isAvailable ? 'Available' : 'Not Available';
    
    if (!$isAvailable && $room->status !== 'available') {
        $statusText = $room->getAvailabilityStatus();
    }
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {$statusClass}"]) }}>
    @if($isAvailable)
        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
        </svg>
    @else
        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
        </svg>
    @endif
    {{ $statusText }}
</span>
