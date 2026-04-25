@props(['course'])

<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-lg transition-all duration-300">
    <a href="{{ route('instructor.courses.show', $course['id']) }}">
        <img src="{{ $course['thumbnail'] }}" alt="{{ $course['title'] }}" class="w-full h-40 object-cover">
    </a>
    <div class="p-5">
        <span class="px-2.5 py-1 text-xs font-medium rounded-full {{ $course['status'] === 'published' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">{{ $course['status'] === 'published' ? 'Publié' : 'Brouillon' }}</span>
        <a href="{{ route('instructor.courses.show', $course['id']) }}" class="block mt-2"><h3 class="font-semibold text-gray-900 line-clamp-2 hover:text-indigo-600">{{ $course['title'] }}</h3></a>
        <div class="grid grid-cols-3 gap-2 mt-4"><div class="text-center"><p class="text-lg font-bold">{{ number_format($course['students_count']) }}</p><p class="text-xs text-gray-500">Étudiants</p></div><div class="text-center"><p class="text-lg font-bold">{{ $course['lessons_count'] }}</p><p class="text-xs text-gray-500">Leçons</p></div><div class="text-center"><i class="fas fa-star text-yellow-400 text-sm"></i><span class="text-lg font-bold ml-1">{{ $course['rating'] ?: '-' }}</span></div></div>
        <div class="flex items-center justify-between pt-4 border-t mt-4"><span class="text-xs text-gray-400">{{ $course['updated_at'] }}</span><div class="flex gap-2"><a href="{{ route('instructor.courses.edit', $course['id']) }}" class="p-2 text-gray-400 hover:text-blue-600"><i class="fas fa-edit"></i></a><div class="relative" x-data="{ open: false }"><button @click="open = !open" class="p-2 text-gray-400 hover:text-gray-600"><i class="fas fa-ellipsis-v"></i></button><div x-show="open" @click.away="open = false" x-cloak class="absolute right-0 bottom-8 w-40 bg-white rounded-lg shadow-xl border py-1 z-50"><button class="w-full text-left px-4 py-2 text-sm hover:bg-gray-50"><i class="fas fa-chart-bar mr-2"></i>Statistiques</button><button class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50"><i class="fas fa-trash mr-2"></i>Supprimer</button></div></div></div></div>
    </div>
</div>