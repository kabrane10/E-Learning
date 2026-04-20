@extends('layouts.public')

@section('title', 'Nouveau sujet')

@section('content')
<div class="bg-gray-50 min-h-screen py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-6">
            <a href="{{ url()->previous() }}" class="text-indigo-600 hover:text-indigo-700">
                <i class="fas fa-arrow-left mr-2"></i>Retour
            </a>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                <h1 class="text-xl font-bold text-gray-900">Créer un nouveau sujet</h1>
            </div>

            <form action="{{ route('forum.topics.store') }}" method="POST" class="p-6 space-y-6">
                @csrf

                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                        Titre <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           name="title" 
                           id="title" 
                           value="{{ old('title') }}" 
                           required
                           class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 @error('title') border-red-300 @enderror"
                           placeholder="Titre de votre sujet...">
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
                            <option value="">Sélectionner une catégorie</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('category_id', request('category_id')) == $cat->id ? 'selected' : '' }}>
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
                            <option value="general" {{ old('type') == 'general' ? 'selected' : '' }}>Discussion générale</option>
                            <option value="question" {{ old('type') == 'question' ? 'selected' : '' }}>Question</option>
                            <option value="announcement" {{ old('type') == 'announcement' ? 'selected' : '' }}>Annonce</option>
                            <option value="resource" {{ old('type') == 'resource' ? 'selected' : '' }}>Ressource</option>
                        </select>
                        @error('type')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                @if(isset($course))
                    <input type="hidden" name="course_id" value="{{ $course->id }}">
                    <div class="p-3 bg-indigo-50 rounded-lg">
                        <i class="fas fa-book text-indigo-600 mr-2"></i>
                        <span class="text-sm">Cours associé :</span>
                        <span class="font-medium">{{ $course->title }}</span>
                    </div>
                @endif

                <div>
                    <label for="content" class="block text-sm font-medium text-gray-700 mb-2">
                        Contenu <span class="text-red-500">*</span>
                    </label>
                    <textarea name="content" 
                              id="content" 
                              rows="10"
                              required
                              class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 @error('content') border-red-300 @enderror"
                              placeholder="Écrivez votre message ici...">{{ old('content') }}</textarea>
                    @error('content')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                    
                    <div class="mt-2 text-xs text-gray-500">
                        <i class="fas fa-info-circle mr-1"></i>
                        Vous pouvez utiliser le formatage basique (liens, sauts de ligne).
                    </div>
                </div>

                <div class="flex items-center">
                    <input type="checkbox" 
                           name="subscribe" 
                           id="subscribe" 
                           value="1" 
                           checked
                           class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                    <label for="subscribe" class="ml-2 text-sm text-gray-700">
                        M'abonner à ce sujet (recevoir des notifications)
                    </label>
                </div>

                <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
                    <a href="{{ url()->previous() }}" 
                       class="px-6 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                        Annuler
                    </a>
                    <button type="submit" 
                            class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                        <i class="fas fa-paper-plane mr-2"></i>Publier le sujet
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection