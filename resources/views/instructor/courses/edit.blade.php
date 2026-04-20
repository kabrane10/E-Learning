@extends('layouts.instructor')

@section('title', 'Modifier ' . $course->title)

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="md:flex md:items-center md:justify-between">
        <div class="flex-1 min-w-0">
            <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight">
                Modifier : {{ $course->title }}
            </h2>
        </div>
    </div>

    <form action="{{ route('instructor.courses.update', $course) }}" method="POST" enctype="multipart/form-data" class="mt-8 space-y-8">
        @csrf
        @method('PUT')
        
        <div class="overflow-hidden bg-white shadow sm:rounded-md">
            <div class="px-4 py-5 sm:p-6">
                <div class="grid grid-cols-6 gap-6">
                    <!-- Titre -->
                    <div class="col-span-6">
                        <label for="title" class="block text-sm font-medium text-gray-700">Titre du cours</label>
                        <input type="text" name="title" id="title" 
                               value="{{ old('title', $course->title) }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                               required>
                        @error('title')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Description courte -->
                    <div class="col-span-6">
                        <label for="short_description" class="block text-sm font-medium text-gray-700">Description courte</label>
                        <div class="mt-1">
                            <textarea id="short_description" name="short_description" rows="2" 
                                      class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                      maxlength="500">{{ old('short_description', $course->short_description) }}</textarea>
                        </div>
                        @error('short_description')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Description complète -->
                    <div class="col-span-6">
                        <label for="description" class="block text-sm font-medium text-gray-700">Description détaillée</label>
                        <div class="mt-1">
                            <textarea id="description" name="description" rows="6" 
                                      class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">{{ old('description', $course->description) }}</textarea>
                        </div>
                        @error('description')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Niveau et Catégorie -->
                    <div class="col-span-6 sm:col-span-3">
                        <label for="level" class="block text-sm font-medium text-gray-700">Niveau</label>
                        <select id="level" name="level" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                required>
                            <option value="beginner" {{ old('level', $course->level) == 'beginner' ? 'selected' : '' }}>Débutant</option>
                            <option value="intermediate" {{ old('level', $course->level) == 'intermediate' ? 'selected' : '' }}>Intermédiaire</option>
                            <option value="advanced" {{ old('level', $course->level) == 'advanced' ? 'selected' : '' }}>Avancé</option>
                        </select>
                    </div>

                    <div class="col-span-6 sm:col-span-3">
                        <label for="category" class="block text-sm font-medium text-gray-700">Catégorie</label>
                        <input type="text" name="category" id="category" 
                               value="{{ old('category', $course->category) }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                               required>
                        @error('category')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Thumbnail actuel -->
                    @if($course->getFirstMediaUrl('thumbnail'))
                    <div class="col-span-6">
                        <label class="block text-sm font-medium text-gray-700">Image actuelle</label>
                        <img src="{{ $course->thumbnail_url }}" alt="{{ $course->title }}" class="mt-2 h-32 w-auto rounded">
                    </div>
                    @endif

                    <!-- Nouveau Thumbnail -->
                    <div class="col-span-6">
                        <label class="block text-sm font-medium text-gray-700">Nouvelle image (optionnel)</label>
                        <div class="flex justify-center px-6 pt-5 pb-6 mt-1 border-2 border-gray-300 border-dashed rounded-md">
                            <div class="space-y-1 text-center">
                                <svg class="w-12 h-12 mx-auto text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <div class="flex text-sm text-gray-600">
                                    <label for="thumbnail" class="relative font-medium text-indigo-600 bg-white rounded-md cursor-pointer hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-indigo-500 focus-within:ring-offset-2">
                                        <span>Changer l'image</span>
                                        <input id="thumbnail" name="thumbnail" type="file" class="sr-only" accept="image/*">
                                    </label>
                                </div>
                                <p class="text-xs text-gray-500">PNG, JPG jusqu'à 5MB</p>
                            </div>
                        </div>
                        @error('thumbnail')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
            <div class="px-4 py-3 text-right bg-gray-50 sm:px-6">
                <a href="{{ route('instructor.courses.show', $course) }}" 
                   class="inline-flex justify-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                    Annuler
                </a>
                <button type="submit" 
                        class="inline-flex justify-center px-4 py-2 ml-3 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                    Mettre à jour
                </button>
            </div>
        </div>
    </form>
</div>
@endsection