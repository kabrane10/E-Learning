@extends('layouts.public')

@section('title', 'Modifier - ' . $topic->title)

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
                <h1 class="text-xl font-bold text-gray-900">Modifier le sujet</h1>
            </div>

            <form action="{{ route('forum.topics.update', [$category, $topic]) }}" method="POST" class="p-6 space-y-6">
                @csrf
                @method('PUT')

                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                        Titre <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           name="title" 
                           id="title" 
                           value="{{ old('title', $topic->title) }}" 
                           required
                           class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 @error('title') border-red-300 @enderror">
                    @error('title')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="category_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Catégorie <span class="text-red-500">*</span>
                        </label>
                        <select name="category_id" 
                                id="category_id" 
                                required
                                class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 @error('category_id') border-red-300 @enderror">
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('category_id', $topic->category_id) == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="type" class="block text-sm font-medium text-gray-700 mb-2">
                            Type <span class="text-red-500">*</span>
                        </label>
                        <select name="type" 
                                id="type" 
                                required
                                class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 @error('type') border-red-300 @enderror">
                            <option value="general" {{ old('type', $topic->type) == 'general' ? 'selected' : '' }}>Discussion générale</option>
                            <option value="question" {{ old('type', $topic->type) == 'question' ? 'selected' : '' }}>Question</option>
                            <option value="announcement" {{ old('type', $topic->type) == 'announcement' ? 'selected' : '' }}>Annonce</option>
                            <option value="resource" {{ old('type', $topic->type) == 'resource' ? 'selected' : '' }}>Ressource</option>
                        </select>
                        @error('type')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="content" class="block text-sm font-medium text-gray-700 mb-2">
                        Contenu <span class="text-red-500">*</span>
                    </label>
                    <textarea name="content" 
                              id="content" 
                              rows="10"
                              required
                              class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 @error('content') border-red-300 @enderror">{{ old('content', $topic->content) }}</textarea>
                    @error('content')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-between pt-4 border-t border-gray-200">
                    <form action="{{ route('forum.topics.destroy', [$category, $topic]) }}" method="POST"
                          onsubmit="return confirm('Supprimer définitivement ce sujet ?')">
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
                            Enregistrer les modifications
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection