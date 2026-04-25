@extends('layouts.admin')

@section('title', 'Modifier le sujet')

@section('breadcrumb')
<nav class="flex" aria-label="Breadcrumb">
    <ol class="flex items-center space-x-2">
        <li><a href="{{ route('admin.dashboard') }}" class="text-gray-400 hover:text-gray-500"><i class="fas fa-home"></i></a></li>
        <li><i class="fas fa-chevron-right text-gray-300 text-xs"></i></li>
        <li><a href="{{ route('admin.forum.topics.index') }}" class="text-gray-400 hover:text-gray-500">Sujets</a></li>
        <li><i class="fas fa-chevron-right text-gray-300 text-xs"></i></li>
        <li><a href="{{ route('admin.forum.topics.show', $topic) }}" class="text-gray-400 hover:text-gray-500">{{ Str::limit($topic->title, 30) }}</a></li>
        <li><i class="fas fa-chevron-right text-gray-300 text-xs"></i></li>
        <li class="text-sm font-medium text-gray-700">Modifier</li>
    </ol>
</nav>
@endsection

@section('content')
<div class="py-6">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h1 class="text-xl font-bold text-gray-900 mb-6">Modifier le sujet</h1>
            
            <form action="{{ route('admin.forum.topics.update', $topic) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Titre</label>
                        <input type="text" name="title" value="{{ old('title', $topic->title) }}" required
                               class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Contenu</label>
                        <textarea name="content" rows="10" required
                                  class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">{{ old('content', $topic->content) }}</textarea>
                    </div>
                    
                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Catégorie</label>
                            <select name="category_id" required class="w-full rounded-lg border-gray-300">
                                @foreach(\App\Models\ForumCategory::all() as $cat)
                                    <option value="{{ $cat->id }}" {{ $topic->category_id == $cat->id ? 'selected' : '' }}>
                                        {{ $cat->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Type</label>
                            <select name="type" required class="w-full rounded-lg border-gray-300">
                                <option value="general" {{ $topic->type == 'general' ? 'selected' : '' }}>Général</option>
                                <option value="question" {{ $topic->type == 'question' ? 'selected' : '' }}>Question</option>
                                <option value="announcement" {{ $topic->type == 'announcement' ? 'selected' : '' }}>Annonce</option>
                                <option value="resource" {{ $topic->type == 'resource' ? 'selected' : '' }}>Ressource</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Statut</label>
                            <select name="1status" required class="w-full rounded-lg border-gray-300">
                                <option value="open" {{ $topic->status == 'open' ? 'selected' : '' }}>Ouvert</option>
                                <option value="closed" {{ $topic->status == 'closed' ? 'selected' : '' }}>Fermé</option>
                                <option value="resolved" {{ $topic->status == 'resolved' ? 'selected' : '' }}>Résolu</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="flex items-center">
                        <input type="checkbox" name="is_sticky" value="1" {{ $topic->is_sticky ? 'checked' : '' }}
                               class="rounded border-gray-300 text-indigo-600">
                        <label class="ml-2 text-sm text-gray-700">Épingler ce sujet</label>
                    </div>
                </div>
                
                <div class="flex justify-end space-x-3 mt-6 pt-6 border-t border-gray-200">
                    <a href="{{ route('admin.forum.topics.show', $topic) }}" class="px-4 py-2 text-gray-700 hover:text-gray-900">
                        Annuler
                    </a>
                    <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                        Enregistrer
                    </button>
                </div>
            </form>
        </div>
        
    </div>
</div>
@endsection