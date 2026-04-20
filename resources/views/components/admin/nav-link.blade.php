@props(['active' => false, 'href' => '#'])

<a href="{{ $href }}" 
   class="px-3 py-2 rounded-md text-sm font-medium {{ $active ? 'bg-indigo-900 text-white' : 'text-indigo-100 hover:bg-indigo-700 hover:text-white' }} transition-colors duration-200">
    {{ $slot }}
</a>