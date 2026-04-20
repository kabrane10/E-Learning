<div class="bg-white rounded-xl shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden group">
    <a href="{{ route('courses.show', $course) }}">
        <div class="relative">
            <img src="{{ $course->thumbnail_url }}" 
                 alt="{{ $course->title }}" 
                 class="w-full h-48 object-cover group-hover:scale-105 transition-transform duration-300">
            
            <span class="absolute top-3 left-3 px-3 py-1 text-xs font-medium rounded-full 
                       {{ $course->level === 'beginner' ? 'bg-green-100 text-green-800' : 
                          ($course->level === 'intermediate' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                {{ ucfirst($course->level) }}
            </span>
        </div>
    </a>
    
    <div class="p-5">
        <div class="flex items-center text-sm text-gray-500 mb-2">
            <span class="bg-indigo-50 text-indigo-700 px-2 py-1 rounded text-xs font-medium">
                {{ $course->category }}
            </span>
        </div>
        
        <a href="{{ route('courses.show', $course) }}" class="block">
            <h3 class="font-semibold text-gray-900 mb-2 line-clamp-2 hover:text-indigo-600">
                {{ $course->title }}
            </h3>
        </a>
        
        <div class="flex items-center text-sm text-gray-500 mb-3">
            <span class="flex items-center">
                <i class="far fa-user mr-1"></i>
                {{ $course->instructor->name }}
            </span>
        </div>
        
        <div class="flex items-center justify-between">
            <div class="flex items-center text-yellow-400">
                @for($i = 1; $i <= 5; $i++)
                    @if($i <= round($course->average_rating))
                        ★
                    @else
                        ☆
                    @endif
                @endfor
                <span class="text-gray-500 text-xs ml-1">({{ $course->reviews_count }})</span>
            </div>
            <span class="text-lg font-bold text-gray-900">Gratuit</span>
        </div>
    </div>
</div>