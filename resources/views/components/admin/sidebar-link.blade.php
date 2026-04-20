@props(['active' => false, 'href' => '#', 'icon' => 'circle'])

<a href="{{ $href }}" 
   class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200 
          {{ $active 
              ? 'bg-indigo-600 text-white shadow-md' 
              : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
    <i class="fas fa-{{ $icon }} w-5 mr-3 {{ $active ? 'text-white' : 'text-gray-400 group-hover:text-white' }}"></i>
    {{ $slot }}
    @if($active)
        <span class="ml-auto w-1.5 h-8 bg-white rounded-full"></span>
    @endif
</a>