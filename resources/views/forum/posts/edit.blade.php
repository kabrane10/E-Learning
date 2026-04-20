@extends('layouts.public')

@section('title', 'Modifier la réponse')

@section('content')
<div class="bg-gray-50 min-h-screen py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-6">
            <a href="{{ route('forum.topics.show', [$category, $topic]) }}" class="text-indigo-600 hover:text-indigo-700">
                <i class="fas fa-arrow-left mr-2"></i>Retour au sujet
            </a>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                <h1 class="text-xl font-bold text-gray-900">Modifier la réponse</h1>
                <p class="text-sm text-gray-500 mt-1">Sujet : {{ $topic->title }}</p>
            </div>

            <form action="{{ route('forum.posts.update', [$category, $topic, $post]) }}" method="POST" class="p-6 space-y-6">
                @csrf
                @method('PUT')

                <div>
                    <label for="content" class="block text-sm font-medium text-gray-700 mb-2">
                        Votre réponse <span class="text-red-500">*</span>
                    </label>
                    <textarea name="content" 
                              id="content" 
                              rows="8"
                              required
                              class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 @error('content') border-red-300 @enderror">{{ old('content', $post->content) }}</textarea>
                    @error('content')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-between pt-4 border-t border-gray-200">
                    <form action="{{ route('forum.posts.destroy', [$category, $topic, $post]) }}" method="POST"
                          onsubmit="return confirm('Supprimer définitivement cette réponse ?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-4 py-2 text-red-600 hover:text-red-700">
                            <i class="fas fa-trash mr-2"></i>Supprimer
                        </button>
                    </form>

                    <div class="flex space-x-3">
                        <a href="{{ route('forum.topics.show', [$category, $topic]) }}" 
                           class="px-6 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                            Annuler
                        </a>
                        <button type="submit" 
                                class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                            Enregistrer
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection