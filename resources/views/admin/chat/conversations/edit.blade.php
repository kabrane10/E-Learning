@extends('layouts.admin')

@section('title', 'Modifier la conversation')

@section('breadcrumb')
<nav class="flex" aria-label="Breadcrumb">
    <ol class="flex items-center space-x-2">
        <li><a href="{{ route('admin.dashboard') }}" class="text-gray-400 hover:text-gray-500"><i class="fas fa-home"></i></a></li>
        <li><i class="fas fa-chevron-right text-gray-300 text-xs"></i></li>
        <li><a href="{{ route('admin.chat.index') }}" class="text-gray-400 hover:text-gray-500">Chat</a></li>
        <li><i class="fas fa-chevron-right text-gray-300 text-xs"></i></li>
        <li><a href="{{ route('admin.chat.conversations.index') }}" class="text-gray-400 hover:text-gray-500">Conversations</a></li>
        <li><i class="fas fa-chevron-right text-gray-300 text-xs"></i></li>
        <li><a href="{{ route('admin.chat.conversations.show', $conversation) }}" class="text-gray-400 hover:text-gray-500">{{ Str::limit($conversation->title ?? 'Conversation #' . $conversation->id, 30) }}</a></li>
        <li><i class="fas fa-chevron-right text-gray-300 text-xs"></i></li>
        <li class="text-sm font-medium text-gray-700">Modifier</li>
    </ol>
</nav>
@endsection

@section('content')
<div class="py-6">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Modifier la conversation</h2>
            </div>
            
            <form action="{{ route('admin.chat.conversations.update', $conversation) }}" method="POST" class="p-6 space-y-6">
                @csrf
                @method('PUT')
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Type</label>
                    <input type="text" value="{{ ucfirst($conversation->type) }}" disabled 
                           class="w-full rounded-lg border-gray-300 bg-gray-50">
                </div>
                
                @if($conversation->type === 'group')
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nom du groupe</label>
                        <input type="text" name="title" value="{{ old('title', $conversation->title) }}" 
                               class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                        @error('title')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                @endif
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Statut</label>
                    <select name="status" class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>Actif</option>
                        <option value="archived" {{ old('status') == 'archived' ? 'selected' : '' }}>Archivé</option>
                    </select>
                </div>
                
                <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
                    <a href="{{ route('admin.chat.conversations.show', $conversation) }}" 
                       class="px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                        Annuler
                    </a>
                    <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                        <i class="fas fa-save mr-2"></i>Enregistrer
                    </button>
                </div>
            </form>
        </div>
        
    </div>
</div>
@endsection