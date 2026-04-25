@extends('layouts.admin')

@section('title', 'Modifier le message')

@section('breadcrumb')
<nav class="flex" aria-label="Breadcrumb">
    <ol class="flex items-center space-x-2">
        <li><a href="{{ route('admin.dashboard') }}" class="text-gray-400 hover:text-gray-500"><i class="fas fa-home"></i></a></li>
        <li><i class="fas fa-chevron-right text-gray-300 text-xs"></i></li>
        <li><a href="{{ route('admin.forum.posts.index') }}" class="text-gray-400 hover:text-gray-500">Messages</a></li>
        <li><i class="fas fa-chevron-right text-gray-300 text-xs"></i></li>
        <li class="text-sm font-medium text-gray-700">Modifier</li>
    </ol>
</nav>
@endsection

@section('content')
<div class="py-6">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h1 class="text-xl font-bold text-gray-900 mb-4">Modifier le message</h1>
            
            <div class="mb-4 p-4 bg-gray-50 rounded-lg">
                <p class="text-sm text-gray-500">Sujet : 
                    <a href="{{ route('admin.forum.topics.show', $post->topic) }}" class="text-indigo-600 hover:text-indigo-700">
                        {{ $post->topic->title }}
                    </a>
                </p>
            </div>
            
            <form action="{{ route('admin.forum.posts.update', $post) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div>
                    <textarea name="content" rows="8" required
                              class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">{{ old('content', $post->content) }}</textarea>
                </div>
                
                <div class="flex justify-end space-x-3 mt-6">
                    <a href="{{ route('admin.forum.topics.show', $post->topic) }}" class="px-4 py-2 text-gray-700 hover:text-gray-900">
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