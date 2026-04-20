@extends('layouts.instructor')

@section('title', 'Quiz du cours')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Quiz du cours</h1>
                <p class="text-gray-600 mt-1">{{ $course->title }}</p>
            </div>
            <a href="{{ route('instructor.courses.show', $course) }}" class="text-gray-600 hover:text-gray-900">
                <i class="fas fa-arrow-left mr-2"></i>Retour au cours
            </a>
        </div>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="p-6">
            @if($course->lessons->where('content_type', 'quiz')->count() > 0)
                <div class="space-y-4">
                    @foreach($course->lessons->where('content_type', 'quiz') as $lesson)
                        <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="font-medium text-gray-900">{{ $lesson->title }}</h3>
                                    <p class="text-sm text-gray-500 mt-1">
                                        @if($lesson->quiz)
                                            {{ $lesson->quiz->questions->count() }} questions • 
                                            Score minimum: {{ $lesson->quiz->passing_score }}%
                                        @else
                                            Aucun quiz configuré
                                        @endif
                                    </p>
                                </div>
                                
                                <div>
                                    @if($lesson->quiz)
                                        <a href="{{ route('instructor.quizzes.edit', $lesson->quiz) }}" 
                                           class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 text-sm">
                                            <i class="fas fa-edit mr-2"></i>Éditer
                                        </a>
                                    @else
                                        <a href="{{ route('instructor.quizzes.create', $lesson) }}" 
                                           class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm">
                                            <i class="fas fa-plus mr-2"></i>Créer un quiz
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12">
                    <i class="fas fa-puzzle-piece text-5xl text-gray-300 mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Aucune leçon de type quiz</h3>
                    <p class="text-gray-500 mb-6">Ajoutez d'abord une leçon de type "quiz" dans le curriculum</p>
                    <a href="{{ route('instructor.courses.show', $course) }}" 
                       class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                        Gérer le curriculum
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection