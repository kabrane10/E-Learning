@extends('layouts.public')

@section('title', $category->name)

@push('styles')
<style>
    .topic-row {
        transition: all 0.2s ease;
    }
    
    .topic-row:hover {
        background-color: #f9fafb;
    }
    
    .filter-btn.active {
        background-color: #4f46e5;
        color: white;
    }
</style>
@endpush

@section('content')
<div class="bg-gray-50 min-h-screen py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Fil d'Ariane -->
        <nav class="flex items-center space-x-2 text-sm text-gray-500 mb-4">
            <a href="{{ route('forum.index') }}" class="hover:text-indigo-600">Forum</a>
            <i class="fas fa-chevron-right text-xs"></i>
            <a href="{{ route('forum.categories.index') }}" class="hover:text-indigo-600">Catégories</a>
            <i class="fas fa-chevron-right text-xs"></i>
            <span class="text-gray-900">{{ $category->name }}</span>
        </nav>

        <!-- En-tête de la catégorie -->
        <div class="bg-gradient-to-r from-{{ $category->color }}-500 to-{{ $category->color }}-600 rounded-2xl shadow-lg p-8 mb-8 text-white">
            <div class="flex items-start justify-between">
                <div class="flex items-start space-x-4">
                    <div class="w-16 h-16 bg-white/20 rounded-xl flex items-center justify-center">
                        <i class="fas fa-{{ $category->icon }} text-3xl"></i>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold">{{ $category->name }}</h1>
                        <p class="text-white/80 mt-2">{{ $category->description }}</p>
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    @auth
                        <button id="subscribe-category-btn" 
                                data-subscribed="{{ $isSubscribed ? 'true' : 'false' }}"
                                class="px-4 py-2 bg-white/20 backdrop-blur-sm rounded-lg hover:bg-white/30 transition-colors">
                            <i class="far fa-bell mr-2"></i>
                            <span id="subscribe-text">{{ $isSubscribed ? 'Abonné' : 'S\'abonner' }}</span>
                        </button>
                    @endauth
                    
                    @can('update', $category)
                        <a href="{{ route('forum.categories.edit', $category) }}" 
                           class="px-4 py-2 bg-white/20 backdrop-blur-sm rounded-lg hover:bg-white/30 transition-colors">
                            <i class="fas fa-edit mr-2"></i>Modifier
                        </a>
                    @endcan
                </div>
            </div>

            <!-- Statistiques -->
            <div class="grid grid-cols-3 gap-6 mt-8">
                <div class="text-center">
                    <div class="text-3xl font-bold">{{ number_format($stats['topics_count']) }}</div>
                    <div class="text-sm text-white/70">Sujets</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold">{{ number_format($stats['posts_count']) }}</div>
                    <div class="text-sm text-white/70">Messages</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold">{{ number_format($stats['resolved_count']) }}</div>
                    <div class="text-sm text-white/70">Résolus</div>
                </div>
            </div>
        </div>

        <!-- Actions et filtres -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex items-center space-x-2">
                    @auth
                        <a href="{{ route('forum.topics.create', ['category_id' => $category->id]) }}" 
                           class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                            <i class="fas fa-plus mr-2"></i>Nouveau sujet
                        </a>
                    @endauth
                    
                    <div class="flex items-center space-x-1 border rounded-lg overflow-hidden">
                        <a href="{{ route('forum.categories.show', ['category' => $category, 'sort' => 'latest']) }}" 
                           class="filter-btn px-3 py-2 text-sm {{ $sort === 'latest' ? 'active text-white' : 'text-gray-600 hover:bg-gray-100' }}">
                            <i class="far fa-clock mr-1"></i>Récents
                        </a>
                        <a href="{{ route('forum.categories.show', ['category' => $category, 'sort' => 'popular']) }}" 
                           class="filter-btn px-3 py-2 text-sm {{ $sort === 'popular' ? 'active text-white' : 'text-gray-600 hover:bg-gray-100' }}">
                            <i class="far fa-eye mr-1"></i>Populaires
                        </a>
                        <a href="{{ route('forum.categories.show', ['category' => $category, 'sort' => 'most_replied']) }}" 
                           class="filter-btn px-3 py-2 text-sm {{ $sort === 'most_replied' ? 'active text-white' : 'text-gray-600 hover:bg-gray-100' }}">
                            <i class="far fa-comments mr-1"></i>Plus répondus
                        </a>
                    </div>
                </div>

                <div class="flex items-center space-x-2">
                    <form action="{{ route('forum.categories.show', $category) }}" method="GET" class="relative">
                        <input type="text" 
                               name="search" 
                               value="{{ request('search') }}"
                               placeholder="Rechercher dans cette catégorie..." 
                               class="w-64 pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                        <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                        <button type="submit" class="hidden">Rechercher</button>
                    </form>
                    
                    <select name="type" onchange="window.location.href=this.value" class="border-gray-300 rounded-lg text-sm">
                        <option value="{{ route('forum.categories.show', $category) }}">Tous les types</option>
                        <option value="{{ route('forum.categories.show', ['category' => $category, 'type' => 'general']) }}" {{ request('type') === 'general' ? 'selected' : '' }}>Général</option>
                        <option value="{{ route('forum.categories.show', ['category' => $category, 'type' => 'question']) }}" {{ request('type') === 'question' ? 'selected' : '' }}>Questions</option>
                        <option value="{{ route('forum.categories.show', ['category' => $category, 'type' => 'announcement']) }}" {{ request('type') === 'announcement' ? 'selected' : '' }}>Annonces</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Liste des sujets -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            @if($topics->count() > 0)
                <div class="divide-y divide-gray-200">
                    @foreach($topics as $topic)
                        <div class="topic-row p-6">
                            <div class="flex items-start space-x-4">
                                <!-- Avatar de l'auteur -->
                                <img src="{{ $topic->user->avatar }}" class="w-10 h-10 rounded-full flex-shrink-0">
                                
                                <!-- Contenu principal -->
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center space-x-2 mb-1">
                                        @if($topic->is_sticky)
                                            <i class="fas fa-thumbtack text-indigo-500 text-xs"></i>
                                        @endif
                                        
                                        @if($topic->type === 'question')
                                            <span class="px-2 py-0.5 text-xs font-medium bg-yellow-100 text-yellow-700 rounded-full">Question</span>
                                        @elseif($topic->type === 'announcement')
                                            <span class="px-2 py-0.5 text-xs font-medium bg-red-100 text-red-700 rounded-full">Annonce</span>
                                        @elseif($topic->type === 'resource')
                                            <span class="px-2 py-0.5 text-xs font-medium bg-green-100 text-green-700 rounded-full">Ressource</span>
                                        @endif
                                        
                                        @if($topic->status === 'resolved')
                                            <span class="px-2 py-0.5 text-xs font-medium bg-green-100 text-green-700 rounded-full">
                                                <i class="fas fa-check-circle mr-1"></i>Résolu
                                            </span>
                                        @elseif($topic->status === 'closed')
                                            <span class="px-2 py-0.5 text-xs font-medium bg-gray-100 text-gray-700 rounded-full">
                                                <i class="fas fa-lock mr-1"></i>Fermé
                                            </span>
                                        @endif
                                    </div>
                                    
                                    <a href="{{ $topic->url }}" class="block group">
                                        <h3 class="text-lg font-semibold text-gray-900 group-hover:text-indigo-600 transition-colors line-clamp-1">
                                            {{ $topic->title }}
                                        </h3>
                                    </a>
                                    
                                    <p class="text-gray-600 text-sm mt-1 line-clamp-2">{{ $topic->excerpt }}</p>
                                    
                                    <div class="flex items-center text-xs text-gray-500 mt-3 space-x-4">
                                        <span class="flex items-center">
                                            <i class="far fa-user mr-1"></i>
                                            {{ $topic->user->name }}
                                        </span>
                                        <span class="flex items-center">
                                            <i class="far fa-calendar mr-1"></i>
                                            {{ $topic->created_at->format('d/m/Y') }}
                                        </span>
                                        <span class="flex items-center">
                                            <i class="far fa-eye mr-1"></i>
                                            {{ $topic->views_count }}
                                        </span>
                                    </div>
                                </div>
                                
                                <!-- Statistiques -->
                                <div class="flex-shrink-0 text-right">
                                    <div class="text-lg font-semibold text-gray-900">{{ $topic->posts_count }}</div>
                                    <div class="text-xs text-gray-500">réponses</div>
                                    
                                    @if($topic->lastPostUser)
                                        <div class="flex items-center justify-end mt-2 text-xs text-gray-500">
                                            <img src="{{ $topic->lastPostUser->avatar }}" class="w-5 h-5 rounded-full mr-1">
                                            <span>{{ $topic->last_post_at->diffForHumans() }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <!-- Pagination -->
                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                    {{ $topics->links() }}
                </div>
            @else
                <div class="p-12 text-center">
                    <i class="far fa-comments text-5xl text-gray-300 mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Aucun sujet dans cette catégorie</h3>
                    <p class="text-gray-500 mb-6">Soyez le premier à créer un sujet !</p>
                    @auth
                        <a href="{{ route('forum.topics.create', ['category_id' => $category->id]) }}" 
                           class="inline-flex items-center px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                            <i class="fas fa-plus mr-2"></i>Créer un sujet
                        </a>
                    @endauth
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const subscribeBtn = document.getElementById('subscribe-category-btn');
        
        if (subscribeBtn) {
            subscribeBtn.addEventListener('click', async function() {
                try {
                    const response = await fetch('{{ route("forum.categories.subscribe", $category) }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        }
                    });
                    
                    const data = await response.json();
                    
                    if (data.success) {
                        const subscribeText = document.getElementById('subscribe-text');
                        subscribeText.textContent = data.subscribed ? 'Abonné' : 'S\'abonner';
                        this.dataset.subscribed = data.subscribed ? 'true' : 'false';
                    }
                } catch (error) {
                    console.error('Erreur:', error);
                }
            });
        }
    });
</script>
@endpush