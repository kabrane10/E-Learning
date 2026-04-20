@extends('layouts.public')

@section('title', 'Recherche : ' . $query)

@section('content')
<div class="bg-gray-50 min-h-screen py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- En-tête -->
        <div class="mb-8">
            <a href="{{ route('forum.index') }}" class="text-indigo-600 hover:text-indigo-700 mb-4 inline-block">
                <i class="fas fa-arrow-left mr-2"></i>Retour au forum
            </a>
            <h1 class="text-2xl font-bold text-gray-900">Résultats de recherche</h1>
            <p class="text-gray-600 mt-1">
                {{ $topics->total() }} résultat(s) pour "{{ $query }}"
            </p>
        </div>

        <!-- Formulaire de recherche -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6">
            <form action="{{ route('forum.search') }}" method="GET" class="relative">
                <input type="text" 
                       name="q" 
                       value="{{ $query }}"
                       placeholder="Rechercher dans le forum..." 
                       class="w-full pl-12 pr-24 py-3 border border-gray-300 rounded-xl focus:ring-indigo-500 focus:border-indigo-500 text-lg">
                <i class="fas fa-search absolute left-4 top-4 text-gray-400 text-xl"></i>
                <button type="submit" class="absolute right-2 top-2 px-6 py-1.5 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                    Rechercher
                </button>
            </form>
        </div>

        <!-- Résultats -->
        @if($topics->count() > 0)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 divide-y divide-gray-200">
                @foreach($topics as $topic)
                    <div class="p-6 hover:bg-gray-50 transition-colors">
                        <div class="flex items-start space-x-4">
                            <img src="{{ $topic->user->avatar }}" class="w-10 h-10 rounded-full">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center space-x-2 mb-1">
                                    <a href="{{ route('forum.categories.show', $topic->category) }}" 
                                       class="text-xs text-indigo-600 hover:text-indigo-700">
                                        {{ $topic->category->name }}
                                    </a>
                                    <span class="text-gray-300">•</span>
                                    <span class="text-xs text-gray-500">{{ $topic->created_at->diffForHumans() }}</span>
                                </div>
                                
                                <a href="{{ $topic->url }}" class="block group">
                                    <h3 class="text-lg font-semibold text-gray-900 group-hover:text-indigo-600 transition-colors">
                                        {!! preg_replace('/(' . preg_quote($query, '/') . ')/i', '<span class="bg-yellow-200">$1</span>', e($topic->title)) !!}
                                    </h3>
                                </a>
                                
                                <p class="text-gray-600 text-sm mt-1 line-clamp-2">
                                    {!! preg_replace('/(' . preg_quote($query, '/') . ')/i', '<span class="bg-yellow-200">$1</span>', e($topic->excerpt)) !!}
                                </p>
                                
                                <div class="flex items-center text-xs text-gray-500 mt-3 space-x-4">
                                    <span>Par {{ $topic->user->name }}</span>
                                    <span><i class="far fa-comment mr-1"></i>{{ $topic->posts_count }} réponses</span>
                                    <span><i class="far fa-eye mr-1"></i>{{ $topic->views_count }} vues</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-6">
                {{ $topics->links() }}
            </div>
        @else
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
                <i class="fas fa-search text-5xl text-gray-300 mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Aucun résultat trouvé</h3>
                <p class="text-gray-500 mb-6">Essayez avec d'autres mots-clés ou créez un nouveau sujet.</p>
                <a href="{{ route('forum.topics.create') }}" 
                   class="inline-flex items-center px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                    <i class="fas fa-plus mr-2"></i>Créer un sujet
                </a>
            </div>
        @endif
    </div>
</div>
@endsection