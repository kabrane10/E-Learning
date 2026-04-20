@extends('layouts.public')

@section('title', 'Forum')

@push('styles')
<style>
    .forum-category-card {
        transition: all 0.3s ease;
    }
    
    .forum-category-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
    }
    
    .topic-row {
        transition: background-color 0.2s ease;
    }
    
    .topic-row:hover {
        background-color: #f9fafb;
    }
</style>
@endpush

@section('content')
<div class="bg-gradient-to-br from-indigo-50 to-purple-50 min-h-screen py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- En-tête -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Forum de discussion</h1>
                    <p class="text-gray-600 mt-2">Échangez avec la communauté, posez vos questions et partagez vos connaissances</p>
                </div>
                @auth
                    <a href="{{ route('forum.topics.create') }}" 
                       class="px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl hover:from-indigo-700 hover:to-purple-700 shadow-lg transition-all transform hover:scale-105">
                        <i class="fas fa-plus-circle mr-2"></i>Nouveau sujet
                    </a>
                @endauth
            </div>
        </div>
        
        <!-- Statistiques -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
            <div class="bg-white rounded-xl shadow-sm p-5 text-center">
                <div class="text-3xl mb-2">💬</div>
                <div class="text-2xl font-bold text-gray-900">{{ number_format($stats['topics_count']) }}</div>
                <div class="text-sm text-gray-500">Sujets</div>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-5 text-center">
                <div class="text-3xl mb-2">💭</div>
                <div class="text-2xl font-bold text-gray-900">{{ number_format($stats['posts_count']) }}</div>
                <div class="text-sm text-gray-500">Messages</div>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-5 text-center">
                <div class="text-3xl mb-2">👥</div>
                <div class="text-2xl font-bold text-gray-900">{{ number_format($stats['users_count']) }}</div>
                <div class="text-sm text-gray-500">Membres</div>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-5 text-center">
                <div class="text-3xl mb-2">🟢</div>
                <div class="text-2xl font-bold text-gray-900">{{ number_format($stats['online_users']) }}</div>
                <div class="text-sm text-gray-500">En ligne</div>
            </div>
        </div>
        
        <!-- Barre de recherche -->
        <div class="mb-8">
            <form action="{{ route('forum.search') }}" method="GET" class="relative max-w-2xl mx-auto">
                <input type="text" 
                       name="q" 
                       placeholder="Rechercher dans le forum..." 
                       class="w-full pl-12 pr-4 py-4 border-0 rounded-2xl shadow-lg focus:ring-2 focus:ring-indigo-500 text-lg">
                <i class="fas fa-search absolute left-4 top-5 text-gray-400 text-xl"></i>
                <button type="submit" class="absolute right-3 top-3 px-6 py-2 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition-colors">
                    Rechercher
                </button>
            </form>
        </div>
        
        <!-- Catégories et sujets récents -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Catégories -->
            <div class="lg:col-span-2 space-y-6">
                @foreach($categories as $category)
                    <div class="bg-white rounded-xl shadow-sm overflow-hidden forum-category-card">
                        <div class="px-6 py-4 bg-gradient-to-r from-{{ $category->color }}-500 to-{{ $category->color }}-600">
                            <div class="flex items-center justify-between">
                                <h2 class="text-xl font-bold text-white">
                                    <i class="fas fa-{{ $category->icon }} mr-2"></i>{{ $category->name }}
                                </h2>
                                <a href="{{ route('forum.categories.show', $category) }}" 
                                   class="text-white/80 hover:text-white text-sm">
                                    Voir tout <i class="fas fa-arrow-right ml-1"></i>
                                </a>
                            </div>
                            <p class="text-white/70 text-sm mt-1">{{ $category->description }}</p>
                        </div>
                        
                        <div class="divide-y divide-gray-100">
                            @forelse($category->topics as $topic)
                                <div class="topic-row px-6 py-4">
                                    <div class="flex items-start space-x-3">
                                        <img src="{{ $topic->user->avatar }}" class="w-10 h-10 rounded-full flex-shrink-0">
                                        <div class="flex-1 min-w-0">
                                            <a href="{{ $topic->url }}" class="block">
                                                <h3 class="font-medium text-gray-900 hover:text-indigo-600 transition-colors line-clamp-1">
                                                    @if($topic->is_sticky)
                                                        <i class="fas fa-thumbtack text-indigo-500 mr-1"></i>
                                                    @endif
                                                    @if($topic->type === 'question')
                                                        <span class="text-xs px-2 py-0.5 bg-yellow-100 text-yellow-700 rounded-full mr-2">Question</span>
                                                    @elseif($topic->type === 'announcement')
                                                        <span class="text-xs px-2 py-0.5 bg-red-100 text-red-700 rounded-full mr-2">Annonce</span>
                                                    @endif
                                                    {{ $topic->title }}
                                                </h3>
                                            </a>
                                            <div class="flex items-center text-xs text-gray-500 mt-1 space-x-3">
                                                <span>Par {{ $topic->user->name }}</span>
                                                <span>{{ $topic->created_at->diffForHumans() }}</span>
                                            </div>
                                        </div>
                                        <div class="text-right flex-shrink-0">
                                            <div class="text-sm font-medium text-gray-900">{{ $topic->posts_count }}</div>
                                            <div class="text-xs text-gray-500">réponses</div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="px-6 py-8 text-center text-gray-500">
                                    <i class="fas fa-comments text-3xl mb-2 opacity-50"></i>
                                    <p>Aucun sujet dans cette catégorie</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                @endforeach
            </div>
            
            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Sujets récents -->
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h3 class="font-semibold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-clock text-indigo-600 mr-2"></i>Sujets récents
                    </h3>
                    <div class="space-y-3">
                        @foreach($recentTopics as $topic)
                            <a href="{{ $topic->url }}" class="block p-3 rounded-lg hover:bg-gray-50 transition-colors">
                                <h4 class="font-medium text-gray-900 text-sm line-clamp-1">{{ $topic->title }}</h4>
                                <div class="flex items-center justify-between mt-1">
                                    <span class="text-xs text-gray-500">{{ $topic->user->name }}</span>
                                    <span class="text-xs text-gray-400">{{ $topic->created_at->diffForHumans() }}</span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
                
                <!-- Sujets populaires -->
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h3 class="font-semibold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-fire text-orange-500 mr-2"></i>Sujets populaires
                    </h3>
                    <div class="space-y-3">
                        @foreach($popularTopics as $topic)
                            <a href="{{ $topic->url }}" class="block p-3 rounded-lg hover:bg-gray-50 transition-colors">
                                <h4 class="font-medium text-gray-900 text-sm line-clamp-1">{{ $topic->title }}</h4>
                                <div class="flex items-center space-x-3 mt-1 text-xs text-gray-500">
                                    <span><i class="fas fa-eye mr-1"></i>{{ $topic->views_count }}</span>
                                    <span><i class="fas fa-comment mr-1"></i>{{ $topic->posts_count }}</span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
                
                <!-- Statistiques rapides -->
                <div class="bg-gradient-to-br from-indigo-600 to-purple-600 rounded-xl shadow-lg p-6 text-white">
                    <h3 class="font-semibold mb-4">Rejoignez la discussion !</h3>
                    <p class="text-white/80 text-sm mb-4">
                        Partagez vos questions, vos réponses et vos expériences avec la communauté.
                    </p>
                    @guest
                        <a href="{{ route('register') }}" class="block w-full py-2 bg-white text-indigo-600 rounded-lg text-center font-medium hover:bg-gray-100 transition-colors">
                            S'inscrire
                        </a>
                    @endguest
                </div>
            </div>
        </div>
    </div>
</div>
@endsection