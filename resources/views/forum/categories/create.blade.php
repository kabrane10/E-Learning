@extends('layouts.public')

@section('title', 'Nouvelle catégorie')

@section('content')
<div class="bg-gray-50 min-h-screen py-8">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-6">
            <a href="{{ route('forum.categories.index') }}" class="text-indigo-600 hover:text-indigo-700">
                <i class="fas fa-arrow-left mr-2"></i>Retour aux catégories
            </a>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                <h1 class="text-xl font-bold text-gray-900">Nouvelle catégorie</h1>
            </div>

            <form action="{{ route('forum.categories.store') }}" method="POST" class="p-6 space-y-6">
                @csrf

                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                        Nom de la catégorie <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           name="name" 
                           id="name" 
                           value="{{ old('name') }}" 
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
                              class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">{{ old('description') }}</textarea>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="icon" class="block text-sm font-medium text-gray-700 mb-2">
                            Icône (Font Awesome)
                        </label>
                        <select name="icon" id="icon" class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="comments">💬 Comments</option>
                            <option value="question-circle">❓ Question</option>
                            <option value="bullhorn">📢 Bullhorn</option>
                            <option value="link">🔗 Link</option>
                            <option value="lightbulb">💡 Lightbulb</option>
                            <option value="user-plus">👤 User Plus</option>
                            <option value="folder">📁 Folder</option>
                        </select>
                    </div>

                    <div>
                        <label for="color" class="block text-sm font-medium text-gray-700 mb-2">
                            Couleur
                        </label>
                        <select name="color" id="color" class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="indigo">Indigo</option>
                            <option value="blue">Bleu</option>
                            <option value="green">Vert</option>
                            <option value="red">Rouge</option>
                            <option value="yellow">Jaune</option>
                            <option value="purple">Violet</option>
                            <option value="pink">Rose</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label for="order" class="block text-sm font-medium text-gray-700 mb-2">
                        Ordre d'affichage
                    </label>
                    <input type="number" 
                           name="order" 
                           id="order" 
                           value="{{ old('order', 0) }}" 
                           min="0"
                           class="w-32 rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
                    <a href="{{ route('forum.categories.index') }}" 
                       class="px-6 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                        Annuler
                    </a>
                    <button type="submit" 
                            class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                        Créer la catégorie
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection