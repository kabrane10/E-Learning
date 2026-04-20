@extends('layouts.instructor')

@section('title', 'Créer un quiz')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Créer un quiz</h1>
        <p class="text-gray-600 mt-1">Leçon : {{ $lesson->title }}</p>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <form action="{{ route('instructor.quizzes.store', $lesson) }}" method="POST">
            @csrf
            
            <div class="p-6 space-y-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Titre du quiz</label>
                    <input type="text" 
                           name="title" 
                           value="{{ old('title', $lesson->title) }}" 
                           required
                           class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                    <textarea name="description" 
                              rows="3"
                              class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">{{ old('description') }}</textarea>
                </div>
                
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Score minimum (%)</label>
                        <input type="number" 
                               name="passing_score" 
                               value="{{ old('passing_score', 70) }}" 
                               min="0" 
                               max="100" 
                               required
                               class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Temps limite (minutes)</label>
                        <input type="number" 
                               name="time_limit" 
                               value="{{ old('time_limit') }}" 
                               min="1"
                               placeholder="Optionnel"
                               class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                </div>
                
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tentatives maximum</label>
                        <input type="number" 
                               name="max_attempts" 
                               value="{{ old('max_attempts') }}" 
                               min="1"
                               placeholder="Illimité si vide"
                               class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                </div>
                
                <div class="flex items-center">
                    <input type="checkbox" 
                           name="shuffle_questions" 
                           value="1" 
                           {{ old('shuffle_questions', true) ? 'checked' : '' }}
                           class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                    <label class="ml-2 text-sm text-gray-700">Mélanger les questions</label>
                </div>
            </div>
            
            <div class="px-6 py-4 bg-gray-50 flex justify-end space-x-3">
                <a href="{{ route('instructor.courses.show', $lesson->course) }}" 
                   class="px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                    Annuler
                </a>
                <button type="submit" 
                        class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                    Créer le quiz
                </button>
            </div>
        </form>
    </div>
</div>
@endsection