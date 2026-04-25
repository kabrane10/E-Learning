@extends('layouts.admin')

@section('title', 'Créer une catégorie')

@section('breadcrumb')
<nav class="flex" aria-label="Breadcrumb">
    <ol class="flex items-center space-x-2">
        <li><a href="{{ route('admin.dashboard') }}" class="text-gray-400 hover:text-gray-500"><i class="fas fa-home"></i></a></li>
        <li><i class="fas fa-chevron-right text-gray-300 text-xs"></i></li>
        <li><a href="{{ route('admin.forum.categories.index') }}" class="text-gray-400 hover:text-gray-500">Catégories</a></li>
        <li><i class="fas fa-chevron-right text-gray-300 text-xs"></i></li>
        <li class="text-sm font-medium text-gray-700">Créer</li>
    </ol>
</nav>
@endsection

@section('content')
<div class="py-6">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                <h1 class="text-xl font-bold text-gray-900">Créer une nouvelle catégorie</h1>
                <p class="text-sm text-gray-500 mt-1">Ajoutez une catégorie pour organiser les discussions</p>
            </div>
            
            <form action="{{ route('admin.forum.categories.store') }}" method="POST" class="p-6 space-y-6">
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
                           class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 @error('name') border-red-300 @enderror"
                           placeholder="Ex: Développement Web">
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
                              class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 @error('description') border-red-300 @enderror"
                              placeholder="Description de la catégorie...">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="icon" class="block text-sm font-medium text-gray-700 mb-2">
                            Icône
                        </label>
                        <select name="icon" id="icon" class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="comments" {{ old('icon') == 'comments' ? 'selected' : '' }}>💬 Comments</option>
                            <option value="question-circle" {{ old('icon') == 'question-circle' ? 'selected' : '' }}>❓ Question</option>
                            <option value="bullhorn" {{ old('icon') == 'bullhorn' ? 'selected' : '' }}>📢 Bullhorn</option>
                            <option value="link" {{ old('icon') == 'link' ? 'selected' : '' }}>🔗 Link</option>
                            <option value="lightbulb" {{ old('icon') == 'lightbulb' ? 'selected' : '' }}>💡 Lightbulb</option>
                            <option value="user-plus" {{ old('icon') == 'user-plus' ? 'selected' : '' }}>👤 User Plus</option>
                            <option value="folder" {{ old('icon') == 'folder' ? 'selected' : '' }}>📁 Folder</option>
                            <option value="code" {{ old('icon') == 'code' ? 'selected' : '' }}>💻 Code</option>
                            <option value="book" {{ old('icon') == 'book' ? 'selected' : '' }}>📚 Book</option>
                        </select>
                    </div>
                    
                    <div>
                        <label for="color" class="block text-sm font-medium text-gray-700 mb-2">
                            Couleur
                        </label>
                        <select name="color" id="color" class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="indigo" {{ old('color') == 'indigo' ? 'selected' : '' }}>Indigo</option>
                            <option value="blue" {{ old('color') == 'blue' ? 'selected' : '' }}>Bleu</option>
                            <option value="green" {{ old('color') == 'green' ? 'selected' : '' }}>Vert</option>
                            <option value="red" {{ old('color') == 'red' ? 'selected' : '' }}>Rouge</option>
                            <option value="yellow" {{ old('color') == 'yellow' ? 'selected' : '' }}>Jaune</option>
                            <option value="purple" {{ old('color') == 'purple' ? 'selected' : '' }}>Violet</option>
                            <option value="pink" {{ old('color') == 'pink' ? 'selected' : '' }}>Rose</option>
                            <option value="orange" {{ old('color') == 'orange' ? 'selected' : '' }}>Orange</option>
                            <option value="teal" {{ old('color') == 'teal' ? 'selected' : '' }}>Turquoise</option>
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
                    <p class="text-xs text-gray-500 mt-1">Les catégories avec un ordre plus petit apparaissent en premier</p>
                </div>
                
                <div class="flex items-center">
                    <input type="checkbox" 
                           name="is_active" 
                           id="is_active" 
                           value="1" 
                           {{ old('is_active', true) ? 'checked' : '' }}
                           class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                    <label for="is_active" class="ml-2 text-sm text-gray-700">
                        Catégorie active (visible sur le forum)
                    </label>
                </div>
                
                <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
                    <a href="{{ route('admin.forum.categories.index') }}" 
                       class="px-6 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                        Annuler
                    </a>
                    <button type="submit" 
                            class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                        <i class="fas fa-save mr-2"></i>Créer la catégorie
                    </button>
                </div>
            </form>
        </div>
        
    </div>
</div>
@endsection