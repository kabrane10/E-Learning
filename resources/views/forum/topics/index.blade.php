@extends('layouts.public')

@section('title', $category ? $category->name . ' - Sujets' : 'Tous les sujets')

@section('content')
<div class="bg-gray-50 min-h-screen py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Fil d'Ariane -->
        <nav class="flex items-center space-x-2 text-sm text-gray-500 mb-4">
            <a href="{{ route('forum.index') }}" class="hover:text-indigo-600">Forum</a>
            <i class="fas fa-chevron-right text-xs"></i>
            @if($category)
                <a href="{{ route('forum.categories.index') }}" class="hover:text-indigo-600">Catégories</a>
                <i class="fas fa-chevron-right text-xs"></i>
                <span class="text-gray-900">{{ $category->name }}</span>
            @else
                <span class="text-gray-900">Tous les sujets</span>
            @endif
        </nav>

        <!-- En-tête -->
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">
                    @if($category)
                        {{ $category->name }}
                    @else
                        Tous les sujets
                    @endif
                </h1>
                <p class="text-gray-500 mt-1">
                    @if($category)
                        {{ $category->description }}
                    @else
                        Parcourez tous les sujets de discussion du forum
                    @endif
                </p>
            </div>
            @auth
                <a href="{{ route('forum.topics.create', $category ? ['category_id' => $category->id] : []) }}" 
                   class="inline-flex items-center px-5 py-2.5 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition-colors shadow-sm">
                    <i class="fas fa-plus mr-2"></i>Nouveau sujet
                </a>
            @endauth
        </div>

        <!-- Filtres et recherche -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6">
            <form method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                @if($category)
                    <input type="hidden" name="category_id" value="{{ $category->id }}">
                @endif
                
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Recherche</label>
                    <div class="relative">
                        <input type="text" 
                               name="search" 
                               value="{{ request('search') }}"
                               placeholder="Titre ou contenu..."
                               class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                        <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                    </div>
                </div>
                
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Catégorie</label>
                    <select name="category_id" class="w-full border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Toutes les catégories</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ request('category_id', $category->id ?? '') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Type</label>
                    <select name="type" class="w-full border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Tous les types</option>
                        <option value="general" {{ request('type') == 'general' ? 'selected' : '' }}>Général</option>
                        <option value="question" {{ request('type') == 'question' ? 'selected' : '' }}>Question</option>
                        <option value="announcement" {{ request('type') == 'announcement' ? 'selected' : '' }}>Annonce</option>
                        <option value="resource" {{ request('type') == 'resource' ? 'selected' : '' }}>Ressource</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Statut</label>
                    <select name="status" class="w-full border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Tous les statuts</option>
                        <option value="open" {{ request('status') == 'open' ? 'selected' : '' }}>Ouvert</option>
                        <option value="resolved" {{ request('status') == 'resolved' ? 'selected' : '' }}>Résolu</option>
                        <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>Fermé</option>
                    </select>
                </div>
                
                <div class="sm:col-span-2 lg:col-span-4 flex justify-end space-x-3">
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 text-sm">
                        <i class="fas fa-filter mr-2"></i>Filtrer
                    </button>
                    <a href="{{ route('forum.topics.index', $category ? ['category_id' => $category->id] : []) }}" 
                       class="px-4 py-2 text-gray-600 hover:text-gray-900 border border-gray-300 rounded-lg text-sm">
                        Réinitialiser
                    </a>
                </div>
            </form>
        </div>

        <!-- Liste des sujets -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            @if($topics->count() > 0)
                <div class="divide-y divide-gray-200">
                    @foreach($topics as $topic)
                        <div class="topic-row p-5 hover:bg-gray-50 transition-colors">
                            <div class="flex items-start space-x-4">
                                <!-- Avatar de l'auteur -->
                                <img src="{{ $topic->user->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($topic->user->name) }}" 
                                     class="w-10 h-10 rounded-full flex-shrink-0">
                                
                                <!-- Contenu principal -->
                                <div class="flex-1 min-w-0">
                                    <div class="flex flex-wrap items-center gap-2 mb-1">
                                        <!-- Icônes -->
                                        @if($topic->is_sticky)
                                            <i class="fas fa-thumbtack text-indigo-500 text-xs"></i>
                                        @endif
                                        
                                        @if($topic->is_announcement)
                                            <i class="fas fa-bullhorn text-red-500 text-xs"></i>
                                        @endif
                                        
                                        <!-- Type -->
                                        <span class="px-2 py-0.5 text-xs font-medium rounded-full 
                                            {{ $topic->type === 'question' ? 'bg-yellow-100 text-yellow-700' : 
                                               ($topic->type === 'announcement' ? 'bg-red-100 text-red-700' : 
                                               ($topic->type === 'resource' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700')) }}">
                                            @if($topic->type === 'question')
                                                <i class="fas fa-question-circle mr-1"></i>
                                            @elseif($topic->type === 'announcement')
                                                <i class="fas fa-bullhorn mr-1"></i>
                                            @elseif($topic->type === 'resource')
                                                <i class="fas fa-link mr-1"></i>
                                            @endif
                                            {{ ucfirst($topic->type) }}
                                        </span>
                                        
                                        <!-- Statut -->
                                        @if($topic->status === 'resolved')
                                            <span class="px-2 py-0.5 text-xs font-medium bg-green-100 text-green-700 rounded-full">
                                                <i class="fas fa-check-circle mr-1"></i>Résolu
                                            </span>
                                        @elseif($topic->status === 'closed')
                                            <span class="px-2 py-0.5 text-xs font-medium bg-gray-100 text-gray-700 rounded-full">
                                                <i class="fas fa-lock mr-1"></i>Fermé
                                            </span>
                                        @endif
                                        
                                        <!-- Catégorie -->
                                        <a href="{{ route('forum.categories.show', $topic->category) }}" 
                                           class="text-xs text-indigo-600 hover:text-indigo-700">
                                            <i class="fas fa-folder mr-1"></i>{{ $topic->category->name }}
                                        </a>
                                    </div>
                                    
                                    <!-- Titre -->
                                    <a href="{{ route('forum.topics.show', [$topic->category->slug, $topic->slug]) }}" 
                                       class="block group">
                                        <h3 class="text-lg font-semibold text-gray-900 group-hover:text-indigo-600 transition-colors line-clamp-1">
                                            {{ $topic->title }}
                                        </h3>
                                    </a>
                                    
                                    <!-- Extrait -->
                                    <p class="text-gray-600 text-sm mt-1 line-clamp-2">
                                        {{ $topic->excerpt ?? Str::limit(strip_tags($topic->content), 200) }}
                                    </p>
                                    
                                    <!-- Métadonnées -->
                                    <div class="flex flex-wrap items-center text-xs text-gray-500 mt-3 gap-x-4 gap-y-1">
                                        <span class="flex items-center">
                                            <i class="far fa-user mr-1"></i>
                                            {{ $topic->user->name }}
                                        </span>
                                        <span class="flex items-center">
                                            <i class="far fa-calendar mr-1"></i>
                                            {{ $topic->created_at->format('d/m/Y') }}
                                        </span>
                                        <span class="flex items-center">
                                            <i class="far fa-comment mr-1"></i>
                                            {{ $topic->posts_count }} réponse{{ $topic->posts_count > 1 ? 's' : '' }}
                                        </span>
                                        <span class="flex items-center">
                                            <i class="far fa-eye mr-1"></i>
                                            {{ number_format($topic->views_count) }} vue{{ $topic->views_count > 1 ? 's' : '' }}
                                        </span>
                                        <span class="flex items-center">
                                            <i class="far fa-heart mr-1"></i>
                                            {{ $topic->likes_count }}
                                        </span>
                                        
                                        @if($topic->lastPostUser)
                                            <span class="flex items-center">
                                                <i class="far fa-clock mr-1"></i>
                                                Dernière réponse par 
                                                <span class="font-medium ml-1">{{ $topic->lastPostUser->name }}</span>
                                                <span class="ml-1">{{ $topic->last_post_at->diffForHumans() }}</span>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                
                                <!-- Statistiques rapides -->
                                <div class="flex-shrink-0 text-right">
                                    <div class="text-lg font-semibold text-gray-900">{{ $topic->posts_count }}</div>
                                    <div class="text-xs text-gray-500">réponses</div>
                                    
                                    @if($topic->lastPostUser)
                                        <div class="mt-2 flex items-center justify-end">
                                            <img src="{{ $topic->lastPostUser->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($topic->lastPostUser->name) }}" 
                                                 class="w-5 h-5 rounded-full" 
                                                 title="{{ $topic->lastPostUser->name }}">
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <!-- Pagination -->
                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                    {{ $topics->withQueryString()->links() }}
                </div>
            @else
                <div class="p-12 text-center">
                    <i class="far fa-comments text-5xl text-gray-300 mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Aucun sujet trouvé</h3>
                    <p class="text-gray-500 mb-6">
                        @if(request()->has('search') || request()->has('type') || request()->has('status'))
                            Aucun sujet ne correspond à vos critères de recherche.
                        @else
                            Soyez le premier à créer un sujet dans cette catégorie !
                        @endif
                    </p>
                    @auth
                        <a href="{{ route('forum.topics.create', $category ? ['category_id' => $category->id] : []) }}" 
                           class="inline-flex items-center px-6 py-3 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700">
                            <i class="fas fa-plus mr-2"></i>Créer un sujet
                        </a>
                    @else
                        <a href="{{ route('login') }}" 
                           class="inline-flex items-center px-6 py-3 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700">
                            <i class="fas fa-sign-in-alt mr-2"></i>Se connecter pour participer
                        </a>
                    @endauth
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .topic-row {
        transition: background-color 0.2s ease;
    }
</style>
@endpush