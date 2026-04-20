@extends('layouts.public')

@section('title', 'Catégories du forum')

@section('content')
<div class="bg-gray-50 min-h-screen py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- En-tête -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Catégories du forum</h1>
                    <p class="text-gray-600 mt-2">Parcourez toutes les catégories de discussion</p>
                </div>
                <a href="{{ route('forum.index') }}" class="text-indigo-600 hover:text-indigo-700">
                    <i class="fas fa-arrow-left mr-2"></i>Retour au forum
                </a>
            </div>
        </div>

        <!-- Liste des catégories -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($categories as $category)
                <a href="{{ route('forum.categories.show', $category) }}" 
                   class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-lg transition-all duration-300 group">
                    <div class="flex items-start space-x-4">
                        <div class="w-12 h-12 bg-{{ $category->color }}-100 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                            <i class="fas fa-{{ $category->icon }} text-{{ $category->color }}-600 text-xl"></i>
                        </div>
                        <div class="flex-1">
                            <h3 class="font-semibold text-gray-900 group-hover:text-indigo-600 transition-colors">
                                {{ $category->name }}
                            </h3>
                            <p class="text-sm text-gray-500 mt-1 line-clamp-2">{{ $category->description }}</p>
                            <div class="flex items-center space-x-4 mt-3 text-xs text-gray-500">
                                <span><i class="far fa-comments mr-1"></i>{{ $category->topics_count }} sujets</span>
                                @if($category->lastTopic)
                                    <span><i class="far fa-clock mr-1"></i>{{ $category->lastTopic->last_post_at->diffForHumans() }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>

        @can('create', App\Models\ForumCategory::class)
            <div class="mt-8 text-center">
                <a href="{{ route('forum.categories.create') }}" 
                   class="inline-flex items-center px-6 py-3 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition-colors">
                    <i class="fas fa-plus mr-2"></i>Nouvelle catégorie
                </a>
            </div>
        @endcan
    </div>
</div>
@endsection