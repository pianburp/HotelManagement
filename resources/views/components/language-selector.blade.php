<div x-data="{ open: false }" class="relative">
    <button @click="open = !open" class="flex items-center text-sm font-medium text-gray-600 hover:text-gray-900 focus:outline-none transition duration-150 ease-in-out">
        <span>{{ $locales[app()->getLocale()] ?? app()->getLocale() }}</span>
        <svg class="ml-1 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
        </svg>
    </button>

    <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 mt-2 w-48 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5 z-50">
        @foreach ($locales as $code => $name)
            <a href="{{ route('language.switch', $code) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900 {{ app()->getLocale() === $code ? 'bg-gray-100 font-medium' : '' }}" role="menuitem">
                {{ $name }}
                @if(app()->getLocale() === $code)
                    <span class="ml-2 text-green-600">✓</span>
                @endif
            </a>
        @endforeach
    </div>
</div>
