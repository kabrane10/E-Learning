@extends('layouts.public')

@section('title', 'Modifier - ' . $category->name)

@section('content')
<div class="bg-gray-50 min-h-screen py-8">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-6">
            <a href="{{ route('forum.categories.show', $category) }}" class="text-indigo-600 hover:text-indigo-700">
                <i class="fas fa-arrow-left mr-2"></i>Retour à la catégorie
            </a>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                <h1 class="text-xl font-bold text-gray-900">Modifier la catégorie</h1>
            </div>

            <form action="{{ route('forum.categories.update', $category) }}" method="POST" class="p-6 space-y-6">
                @csrf
                @method('PUT')

                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                        Nom de la catégorie <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           name="name" 
                           id="name" 
                           value="{{ old('name', $category->name) }}" 
                           required
                           class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 @error('name') border-red-300 @enderror">
                    @error('name')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                        Description
                    </label>
                    <textarea name="description" 
                              id="description" 
                              rows="3"
                              class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">{{ old('description', $category->description) }}</textarea>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="icon" class="block text-sm font-medium text-gray-700 mb-2">
                            Icône (Font Awesome)
                        </label>
                        <select name="icon" id="icon" class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="comments" {{ $category->icon === 'comments' ? 'selected' : '' }}>💬 Comments</option>
                            <option value="question-circle" {{ $category->icon === 'question-circle' ? 'selected' : '' }}>❓ Question</option>
                            <option value="bullhorn" {{ $category->icon === 'bullhorn' ? 'selected' : '' }}>📢 Bullhorn</option>
                            <option value="link" {{ $category->icon === 'link' ? 'selected' : '' }}>🔗 Link</option>
                            <option value="lightbulb" {{ $category->icon === 'lightbulb' ? 'selected' : '' }}>💡 Lightbulb</option>
                            <option value="user-plus" {{ $category->icon === 'user-plus' ? 'selected' : '' }}>👤 User Plus</option>
                            <option value="folder" {{ $category->icon === 'folder' ? 'selected' : '' }}>📁 Folder</option>
                        </select>
                    </div>

                    <div>
                        <label for="color" class="block text-sm font-medium text-gray-700 mb-2">
                            Couleur
                        </label>
                        <select name="color" id="color" class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="indigo" {{ $category->color === 'indigo' ? 'selected' : '' }}>Indigo</option>
                            <option value="blue" {{ $category->color === 'blue' ? 'selected' : '' }}>Bleu</option>
                            <option value="green" {{ $category->color === 'green' ? 'selected' : '' }}>Vert</option>
                            <option value="red" {{ $category->color === 'red' ? 'selected' : '' }}>Rouge</option>
                            <option value="yellow" {{ $category->color === 'yellow' ? 'selected' : '' }}>Jaune</option>
                            <option value="purple" {{ $category->color === 'purple' ? 'selected' : '' }}>Violet</option>
                            <option value="pink" {{ $category->color === 'pink' ? 'selected' : '' }}>Rose</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="order" class="block text-sm font-medium text-gray-700 mb-2">
                            Ordre d'affichage
                        </label>
                        <input type="number" 
                               name="order" 
                               id="order" 
                               value="{{ old('order', $category->order) }}" 
                               min="0"
                               class="w-32 rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>

                    <div class="flex items-center">
                        <label class="flex items-center">
                            <input type="checkbox" 
                                   name="is_active" 
                                   value="1" 
                                   {{ $category->is_active ? 'checked' : '' }}
                                   class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                            <span class="ml-2 text-sm text-gray-700">Catégorie active</span>
                        </label>
                    </div>
                </div>

                <div class="flex justify-between pt-4 border-t border-gray-200">
                    <form action="{{ route('forum.categories.destroy', $category) }}" method="POST"
                          onsubmit="return confirm('Supprimer définitivement cette catégorie ?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-4 py-2 text-red-600 hover:text-red-700">
                            <i class="fas fa-trash mr-2"></i>Supprimer
                        </button>
                    </form>

                    <div class="flex space-x-3">
                        <a href="{{ route('forum.categories.show', $category) }}" 
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