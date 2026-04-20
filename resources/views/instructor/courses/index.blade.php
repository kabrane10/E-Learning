@extends('layouts.instructor')

@section('title', 'Mes Cours')

@section('content')
<div class="sm:flex sm:items-center sm:justify-between">
    <h1 class="text-2xl font-semibold text-gray-900">Mes Cours</h1>
    <a href="{{ route('instructor.courses.create') }}" 
       class="inline-flex items-center px-4 py-2 mt-4 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:mt-0">
        <svg class="w-5 h-5 mr-2 -ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        Nouveau Cours
    </a>
</div>

<div class="grid grid-cols-1 gap-6 mt-8 sm:grid-cols-2 lg:grid-cols-3">
    @forelse($courses as $course)
        <div class="overflow-hidden bg-white rounded-lg shadow hover:shadow-lg transition-shadow duration-200">
            <a href="{{ route('instructor.courses.show', $course) }}">
                <img src="{{ $course->thumbnail_url }}" 
                     alt="{{ $course->title }}" 
                     class="object-cover w-full h-48">
            </a>
            <div class="p-6">
                <div class="flex items-center justify-between mb-2">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $course->is_published ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                        {{ $course->is_published ? 'Publié' : 'Brouillon' }}
                    </span>
                    <span class="text-sm text-gray-500">{{ $course->lessons_count }} leçons</span>
                </div>
                <a href="{{ route('instructor.courses.show', $course) }}" class="block">
                    <h3 class="text-lg font-semibold text-gray-900 truncate">{{ $course->title }}</h3>
                    <p class="mt-2 text-sm text-gray-500 line-clamp-2">{{ $course->short_description }}</p>
                </a>
                <div class="flex items-center justify-between mt-6">
                    <div class="text-sm text-gray-500">
                        <span class="font-medium text-gray-900">{{ $course->students_count }}</span> étudiants
                    </div>
                    <div class="flex space-x-2">
                        <a href="{{ route('instructor.courses.edit', $course) }}" 
                           class="text-indigo-600 hover:text-indigo-900">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                        </a>
                        <form action="{{ route('instructor.courses.destroy', $course) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    onclick="return confirm('Supprimer ce cours ?')"
                                    class="text-red-600 hover:text-red-900">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-span-full py-12 text-center">
            <svg class="w-12 h-12 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">Aucun cours</h3>
            <p class="mt-1 text-sm text-gray-500">Commencez par créer votre premier cours.</p>
            <div class="mt-6">
                <a href="{{ route('instructor.courses.create') }}" 
                   class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-700">
                    <svg class="w-5 h-5 mr-2 -ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Nouveau Cours
                </a>
            </div>
        </div>
    @endforelse
</div>

<div class="mt-8">
    {{ $courses->links() }}
</div>
@endsection