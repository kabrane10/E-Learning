@props(['active' => false, 'href' => '#', 'icon' => 'circle'])

<a href="{{ $href }}" 
   class="group flex items-center px-3 py-2 text-base font-medium rounded-md transition-colors duration-200
          {{ $active 
              ? 'bg-indigo-600 text-white' 
              : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
    <i class="fas fa-{{ $icon }} w-6 mr-3 {{ $active ? 'text-white' : 'text-gray-400 group-hover:text-white' }}"></i>
    {{ $slot }}
</a>