@extends('layouts.public')

@section('title', $topic->title)

@section('content')
<div class="bg-gray-50 min-h-screen py-8">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Fil d'Ariane -->
        <nav class="flex items-center space-x-2 text-sm text-gray-500 mb-4">
            <a href="{{ route('forum.index') }}" class="hover:text-indigo-600">Forum</a>
            <i class="fas fa-chevron-right text-xs"></i>
            <a href="{{ route('forum.categories.show', $category) }}" class="hover:text-indigo-600">{{ $category->name }}</a>
            <i class="fas fa-chevron-right text-xs"></i>
            <span class="text-gray-900">{{ $topic->title }}</span>
        </nav>
        
        <!-- En-tête du sujet -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 mb-6">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <div class="flex items-center space-x-2 mb-2">
                        @if($topic->type === 'question')
                            <span class="px-2 py-1 text-xs font-medium bg-yellow-100 text-yellow-700 rounded-full">Question</span>
                        @elseif($topic->type === 'announcement')
                            <span class="px-2 py-1 text-xs font-medium bg-red-100 text-red-700 rounded-full">Annonce</span>
                        @endif
                        
                        @if($topic->status === 'resolved')
                            <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-700 rounded-full">
                                <i class="fas fa-check-circle mr-1"></i>Résolu
                            </span>
                        @elseif($topic->status === 'closed')
                            <span class="px-2 py-1 text-xs font-medium bg-gray-100 text-gray-700 rounded-full">
                                <i class="fas fa-lock mr-1"></i>Fermé
                            </span>
                        @endif
                        
                        @if($topic->is_sticky)
                            <span class="px-2 py-1 text-xs font-medium bg-indigo-100 text-indigo-700 rounded-full">
                                <i class="fas fa-thumbtack mr-1"></i>Épinglé
                            </span>
                        @endif
                    </div>
                    
                    <h1 class="text-2xl font-bold text-gray-900 mb-3">{{ $topic->title }}</h1>
                    
                    <div class="flex items-center text-sm text-gray-500 space-x-4">
                        <div class="flex items-center">
                            <img src="{{ $topic->user->avatar }}" class="w-6 h-6 rounded-full mr-2">
                            <span>{{ $topic->user->name }}</span>
                        </div>
                        <span><i class="far fa-calendar mr-1"></i>{{ $topic->created_at->format('d/m/Y H:i') }}</span>
                        <span><i class="far fa-eye mr-1"></i>{{ $topic->views_count }} vues</span>
                        <span><i class="far fa-comment mr-1"></i>{{ $topic->posts_count }} réponses</span>
                    </div>
                </div>
                
                <div class="flex items-center space-x-2">
                    @auth
                        <button id="subscribe-btn" 
                                data-subscribed="{{ $isSubscribed ? 'true' : 'false' }}"
                                class="p-2 rounded-lg transition-colors {{ $isSubscribed ? 'text-indigo-600 bg-indigo-50' : 'text-gray-400 hover:text-indigo-600 hover:bg-gray-100' }}">
                            <i class="far fa-bell text-xl"></i>
                        </button>
                    @endauth
                    
                    @can('update', $topic)
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="p-2 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>
                            <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 py-1 z-50">
                                <a href="{{ route('forum.topics.edit', [$category, $topic]) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                    <i class="fas fa-edit mr-2"></i>Modifier
                                </a>
                                <form action="{{ route('forum.topics.pin', [$category, $topic]) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                        <i class="fas fa-thumbtack mr-2"></i>{{ $topic->is_sticky ? 'Désépingler' : 'Épingler' }}
                                    </button>
                                </form>
                                <form action="{{ route('forum.topics.close', [$category, $topic]) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                        <i class="fas fa-lock mr-2"></i>{{ $topic->status === 'closed' ? 'Rouvrir' : 'Fermer' }}
                                    </button>
                                </form>
                                <hr class="my-1">
                                <form action="{{ route('forum.topics.destroy', [$category, $topic]) }}" method="POST"
                                      onsubmit="return confirm('Supprimer définitivement ce sujet ?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                        <i class="fas fa-trash mr-2"></i>Supprimer
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endcan
                </div>
            </div>
            
            @if($topic->course)
                <div class="mt-4 p-3 bg-indigo-50 rounded-lg">
                    <i class="fas fa-book text-indigo-600 mr-2"></i>
                    <span class="text-sm">Cours associé :</span>
                    <a href="{{ route('courses.show', $topic->course) }}" class="text-indigo-600 hover:text-indigo-700 font-medium">
                        {{ $topic->course->title }}
                    </a>
                </div>
            @endif
        </div>
        
        <!-- Messages -->
        <div class="space-y-6">
            <!-- Premier message (sujet) -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6" id="post-{{ $topic->id }}">
                <div class="flex space-x-4">
                    <div class="flex-shrink-0">
                        <img src="{{ $topic->user->avatar }}" class="w-12 h-12 rounded-full">
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between mb-2">
                            <div>
                                <span class="font-medium text-gray-900">{{ $topic->user->name }}</span>
                                <span class="text-xs text-gray-500 ml-2">{{ $topic->created_at->diffForHumans() }}</span>
                            </div>
                            <span class="px-2 py-0.5 text-xs bg-blue-100 text-blue-700 rounded-full">Auteur</span>
                        </div>
                        <div class="prose prose-sm max-w-none">
                            {!! nl2br(e($topic->content)) !!}
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Réponses -->
            @foreach($posts as $post)
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 {{ $post->is_solution ? 'border-green-300 bg-green-50/30' : '' }}"
                     id="post-{{ $post->id }}">
                    
                    @if($post->is_solution)
                        <div class="mb-4 text-green-700">
                            <i class="fas fa-check-circle mr-2"></i>
                            <span class="font-medium">Réponse marquée comme solution</span>
                        </div>
                    @endif
                    
                    <div class="flex space-x-4">
                        <div class="flex-shrink-0">
                            <img src="{{ $post->user->avatar }}" class="w-12 h-12 rounded-full">
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between mb-2">
                                <div>
                                    <span class="font-medium text-gray-900">{{ $post->user->name }}</span>
                                    <span class="text-xs text-gray-500 ml-2">{{ $post->created_at->diffForHumans() }}</span>
                                    @if($post->is_edited)
                                        <span class="text-xs text-gray-400 ml-2" title="Modifié le {{ $post->edited_at->format('d/m/Y H:i') }}">
                                            <i class="fas fa-pencil-alt mr-1"></i>modifié
                                        </span>
                                    @endif
                                </div>
                                <div class="flex items-center space-x-2">
                                    @if($topic->type === 'question' && !$topic->status === 'resolved' && Auth::id() === $topic->user_id)
                                        <form action="{{ route('forum.posts.solution', [$category, $topic, $post]) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="text-xs text-green-600 hover:text-green-700">
                                                <i class="fas fa-check mr-1"></i>Marquer comme solution
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                            <div class="prose prose-sm max-w-none">
                                {!! nl2br(e($post->content)) !!}
                            </div>
                            
                            <!-- Actions -->
                            <div class="flex items-center space-x-4 mt-4 pt-4 border-t border-gray-100">
                                <button class="like-btn flex items-center space-x-1 text-sm transition-colors {{ $post->isLikedBy(Auth::user()) ? 'text-indigo-600' : 'text-gray-500 hover:text-indigo-600' }}"
                                        data-post-id="{{ $post->id }}">
                                    <i class="far fa-heart"></i>
                                    <span class="likes-count">{{ $post->likes_count }}</span>
                                </button>
                                
                                <button class="reply-btn flex items-center space-x-1 text-sm text-gray-500 hover:text-indigo-600"
                                        data-post-id="{{ $post->id }}">
                                    <i class="far fa-comment"></i>
                                    <span>Répondre</span>
                                </button>
                                
                                @can('update', $post)
                                    <a href="{{ route('forum.posts.edit', [$category, $topic, $post]) }}" 
                                       class="text-sm text-gray-500 hover:text-indigo-600">
                                        <i class="far fa-edit mr-1"></i>Modifier
                                    </a>
                                @endcan
                                
                                @can('delete', $post)
                                    <form action="{{ route('forum.posts.destroy', [$category, $topic, $post]) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-sm text-red-500 hover:text-red-600"
                                                onclick="return confirm('Supprimer cette réponse ?')">
                                            <i class="far fa-trash-alt mr-1"></i>Supprimer
                                        </button>
                                    </form>
                                @endcan
                            </div>
                            
                            <!-- Formulaire de réponse rapide -->
                            <div class="reply-form hidden mt-4" id="reply-form-{{ $post->id }}">
                                <form action="{{ route('forum.posts.store', [$category, $topic]) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="parent_id" value="{{ $post->id }}">
                                    <textarea name="content" rows="3" 
                                              class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500"
                                              placeholder="Votre réponse..."></textarea>
                                    <div class="flex justify-end space-x-2 mt-2">
                                        <button type="button" class="cancel-reply px-4 py-2 text-gray-600 hover:text-gray-900">
                                            Annuler
                                        </button>
                                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                                            Répondre
                                        </button>
                                    </div>
                                </form>
                            </div>
                            
                            <!-- Sous-réponses -->
                            @if($post->replies->count() > 0)
                                <div class="mt-4 space-y-4 pl-4 border-l-2 border-gray-200">
                                    @foreach($post->replies as $reply)
                                        <div class="bg-gray-50 rounded-lg p-4">
                                            <div class="flex items-start space-x-3">
                                                <img src="{{ $reply->user->avatar }}" class="w-8 h-8 rounded-full">
                                                <div>
                                                    <div class="flex items-center space-x-2">
                                                        <span class="font-medium text-gray-900">{{ $reply->user->name }}</span>
                                                        <span class="text-xs text-gray-500">{{ $reply->created_at->diffForHumans() }}</span>
                                                    </div>
                                                    <p class="text-sm text-gray-700 mt-1">{{ $reply->content }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        
        <!-- Formulaire de réponse -->
        @auth
            @if($topic->status !== 'closed')
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 mt-6">
                    <h3 class="font-semibold text-gray-900 mb-4">Votre réponse</h3>
                    <form action="{{ route('forum.posts.store', [$category, $topic]) }}" method="POST">
                        @csrf
                        <textarea name="content" rows="5" 
                                  class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500"
                                  placeholder="Écrivez votre réponse ici..."></textarea>
                        <div class="flex justify-end mt-4">
                            <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                                <i class="fas fa-paper-plane mr-2"></i>Publier
                            </button>
                        </div>
                    </form>
                </div>
            @else
                <div class="bg-yellow-50 border border-yellow-200 rounded-2xl p-6 mt-6 text-center">
                    <i class="fas fa-lock text-yellow-600 text-2xl mb-2"></i>
                    <p class="text-yellow-800">Ce sujet est fermé. Vous ne pouvez plus répondre.</p>
                </div>
            @endif
        @else
            <div class="bg-indigo-50 border border-indigo-200 rounded-2xl p-6 mt-6 text-center">
                <p class="text-indigo-800 mb-3">Connectez-vous pour participer à la discussion</p>
                <a href="{{ route('login') }}" class="inline-block px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                    Se connecter
                </a>
            </div>
        @endauth
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Gestion des likes
        document.querySelectorAll('.like-btn').forEach(btn => {
            btn.addEventListener('click', async function() {
                const postId = this.dataset.postId;
                const icon = this.querySelector('i');
                const countSpan = this.querySelector('.likes-count');
                
                try {
                    const response = await fetch(`/forum/posts/${postId}/like`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        }
                    });
                    
                    const data = await response.json();
                    
                    if (data.success) {
                        countSpan.textContent = data.likes_count;
                        this.classList.toggle('text-indigo-600', data.is_liked);
                        icon.classList.toggle('fas', data.is_liked);
                        icon.classList.toggle('far', !data.is_liked);
                    }
                } catch (error) {
                    console.error('Erreur:', error);
                }
            });
        });
        
        // Gestion des réponses
        document.querySelectorAll('.reply-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const postId = this.dataset.postId;
                const form = document.getElementById(`reply-form-${postId}`);
                form.classList.toggle('hidden');
                form.querySelector('textarea').focus();
            });
        });
        
        document.querySelectorAll('.cancel-reply').forEach(btn => {
            btn.addEventListener('click', function() {
                this.closest('.reply-form').classList.add('hidden');
            });
        });
        
        // Gestion de l'abonnement
        const subscribeBtn = document.getElementById('subscribe-btn');
        if (subscribeBtn) {
            subscribeBtn.addEventListener('click', async function() {
                try {
                    const response = await fetch(`/forum/topics/{{ $topic->id }}/subscribe`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        }
                    });
                    
                    const data = await response.json();
                    
                    if (data.success) {
                        const isSubscribed = this.dataset.subscribed === 'true';
                        this.dataset.subscribed = isSubscribed ? 'false' : 'true';
                        this.classList.toggle('text-indigo-600', !isSubscribed);
                        this.classList.toggle('bg-indigo-50', !isSubscribed);
                        
                        // Notification toast
                        alert(data.message);
                    }
                } catch (error) {
                    console.error('Erreur:', error);
                }
            });
        }
    });
</script>
@endpush