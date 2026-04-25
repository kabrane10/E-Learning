@extends('layouts.admin')

@section('title', 'Sujet : ' . Str::limit($topic->title, 50))

@section('breadcrumb')
<nav class="flex" aria-label="Breadcrumb">
    <ol class="flex items-center space-x-2">
        <li><a href="{{ route('admin.dashboard') }}" class="text-gray-400 hover:text-gray-500"><i class="fas fa-home"></i></a></li>
        <li><i class="fas fa-chevron-right text-gray-300 text-xs"></i></li>
        <li><a href="{{ route('admin.forum.categories.index') }}" class="text-gray-400 hover:text-gray-500">Forum</a></li>
        <li><i class="fas fa-chevron-right text-gray-300 text-xs"></i></li>
        <li><a href="{{ route('admin.forum.topics.index') }}" class="text-gray-400 hover:text-gray-500">Sujets</a></li>
        <li><i class="fas fa-chevron-right text-gray-300 text-xs"></i></li>
        <li class="text-sm font-medium text-gray-700">{{ Str::limit($topic->title, 40) }}</li>
    </ol>
</nav>
@endsection

@push('styles')
<style>
    .post-content {
        word-break: break-word;
    }
    
    .admin-reply-badge {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
    
    .toast-notification {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 9999;
        animation: slideIn 0.3s ease-out;
    }
    
    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateX(100%);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }
</style>
@endpush

@section('content')
<div class="py-6" x-data="topicAdminManager({{ $topic->id }})">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Toast Notification -->
        <div x-show="toast.show" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform translate-x-full"
             x-transition:enter-end="opacity-100 transform translate-x-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 transform translate-x-0"
             x-transition:leave-end="opacity-0 transform translate-x-full"
             class="toast-notification">
            <div :class="toast.type === 'success' ? 'bg-green-50 border-green-400 text-green-700' : 'bg-red-50 border-red-400 text-red-700'"
                 class="border-l-4 p-4 rounded-r-lg shadow-lg flex items-center">
                <i :class="toast.type === 'success' ? 'fas fa-check-circle text-green-500' : 'fas fa-exclamation-circle text-red-500'" class="mr-3"></i>
                <span x-text="toast.message"></span>
                <button @click="toast.show = false" class="ml-4 text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
        
        <!-- En-tête du sujet -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <div class="flex items-center space-x-2 mb-2">
                        <span x-show="topicData.is_sticky" 
                              class="px-2 py-0.5 text-xs font-medium bg-indigo-100 text-indigo-700 rounded-full">
                            <i class="fas fa-thumbtack mr-1"></i>Épinglé
                        </span>
                        @if($topic->is_announcement)
                            <span class="px-2 py-0.5 text-xs font-medium bg-red-100 text-red-700 rounded-full">
                                <i class="fas fa-bullhorn mr-1"></i>Annonce
                            </span>
                        @endif
                        <span :class="{
                                'bg-green-100 text-green-700': topicData.status === 'open',
                                'bg-blue-100 text-blue-700': topicData.status === 'resolved',
                                'bg-gray-100 text-gray-700': topicData.status === 'closed'
                             }"
                              class="px-2 py-0.5 text-xs font-medium rounded-full">
                            <span x-text="topicData.status === 'open' ? 'Ouvert' : (topicData.status === 'resolved' ? 'Résolu' : 'Fermé')"></span>
                        </span>
                    </div>
                    
                    <h1 class="text-2xl font-bold text-gray-900 mb-3">{{ $topic->title }}</h1>
                    
                    <div class="flex flex-wrap items-center text-sm text-gray-500 gap-x-4 gap-y-1">
                        <span class="flex items-center">
                            <img src="{{ $topic->user->avatar }}" class="w-5 h-5 rounded-full mr-1.5">
                            {{ $topic->user->name }}
                        </span>
                        <span><i class="far fa-folder mr-1"></i>{{ $topic->category->name }}</span>
                        <span><i class="far fa-calendar mr-1"></i>{{ $topic->created_at->format('d/m/Y H:i') }}</span>
                        <span><i class="far fa-eye mr-1"></i>{{ $topic->views_count }} vues</span>
                        <span><i class="far fa-comment mr-1"></i>{{ $topic->posts_count }} réponses</span>
                    </div>
                </div>
                
                <div class="flex items-center space-x-2 ml-4">
                    {{-- Bouton Épingler/Désépingler avec AJAX --}}
                    <button @click="togglePin()" 
                            class="p-2 text-gray-500 hover:text-indigo-600 rounded-lg hover:bg-gray-100 transition-colors"
                            :title="topicData.is_sticky ? 'Désépingler' : 'Épingler'">
                        <i class="fas fa-thumbtack" :class="{ 'text-indigo-600': topicData.is_sticky }"></i>
                    </button>
                    
                    {{-- Bouton Fermer/Ouvrir avec AJAX --}}
                    <button @click="toggleClose()" 
                            class="p-2 text-gray-500 hover:text-yellow-600 rounded-lg hover:bg-gray-100 transition-colors"
                            :title="topicData.status === 'closed' ? 'Rouvrir' : 'Fermer'">
                        <i class="fas" :class="topicData.status === 'closed' ? 'fa-lock-open' : 'fa-lock'"></i>
                    </button>
                    
                    <a href="{{ route('admin.forum.topics.edit', $topic) }}" 
                       class="p-2 text-gray-500 hover:text-blue-600 rounded-lg hover:bg-gray-100 transition-colors"
                       title="Modifier">
                        <i class="fas fa-edit"></i>
                    </a>
                    
                    {{-- Bouton Supprimer avec confirmation --}}
                    <button @click="confirmDelete()" 
                            class="p-2 text-gray-500 hover:text-red-600 rounded-lg hover:bg-gray-100 transition-colors"
                            title="Supprimer">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
            
            <div class="mt-5 prose prose-sm max-w-none post-content">
                {!! nl2br(e($topic->content)) !!}
            </div>
        </div>

        <!-- Messages -->
        <div class="space-y-4 mb-6">
            @forelse($topic->posts as $post)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5" id="post-{{ $post->id }}">
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex items-center space-x-3">
                            <img src="{{ $post->user->avatar }}" class="w-8 h-8 rounded-full">
                            <div>
                                <div class="flex items-center">
                                    <span class="font-medium text-gray-900">{{ $post->user->name }}</span>
                                    @if($post->user->hasRole('admin'))
                                        <span class="ml-2 px-1.5 py-0.5 text-xs admin-reply-badge text-white rounded-full">
                                            <i class="fas fa-shield-alt mr-0.5"></i>Admin
                                        </span>
                                    @endif
                                </div>
                                <span class="text-xs text-gray-500">{{ $post->created_at->format('d/m/Y H:i') }}</span>
                                @if($post->is_edited)
                                    <span class="text-xs text-gray-400 ml-2">
                                        <i class="fas fa-pencil-alt mr-1"></i>Modifié
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="flex items-center space-x-2">
                            @if($topic->type === 'question' && !$post->is_solution)
                                <button @click="markAsSolution({{ $post->id }})" 
                                        class="text-xs text-green-600 hover:text-green-700 px-2 py-1 bg-green-50 rounded-lg"
                                        title="Marquer comme solution">
                                    <i class="fas fa-check mr-1"></i>Solution
                                </button>
                            @endif
                            
                            <a href="{{ route('admin.forum.posts.edit', $post) }}" 
                               class="p-1.5 text-gray-400 hover:text-blue-600 rounded-lg hover:bg-gray-100"
                               title="Modifier">
                                <i class="fas fa-edit text-sm"></i>
                            </a>
                            
                            <button @click="deletePost({{ $post->id }})" 
                                    class="p-1.5 text-gray-400 hover:text-red-600 rounded-lg hover:bg-gray-100"
                                    title="Supprimer">
                                <i class="fas fa-trash text-sm"></i>
                            </button>
                        </div>
                    </div>
                    
                    @if($post->is_solution)
                        <div class="mb-3 p-2 bg-green-50 border border-green-200 rounded-lg">
                            <span class="text-xs text-green-700">
                                <i class="fas fa-check-circle mr-1"></i>Réponse marquée comme solution
                            </span>
                        </div>
                    @endif
                    
                    <div class="prose prose-sm max-w-none post-content">
                        {!! nl2br(e($post->content)) !!}
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8 text-center text-gray-500">
                    <i class="fas fa-comments text-4xl mb-3 opacity-30"></i>
                    <p>Aucune réponse pour le moment</p>
                </div>
            @endforelse
        </div>

        <!-- Formulaire de réponse Admin -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 bg-gradient-to-r from-indigo-50 to-purple-50 border-b border-gray-200">
                <h3 class="font-semibold text-gray-900 flex items-center">
                    <i class="fas fa-shield-alt text-indigo-600 mr-2"></i>
                    Répondre en tant qu'administrateur
                </h3>
            </div>
            
            <form @submit.prevent="submitReply" class="p-6">
                <textarea x-model="replyForm.content" 
                          rows="4" 
                          required
                          class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 mb-4"
                          placeholder="Votre réponse officielle..."></textarea>
                
                <div class="flex justify-between items-center">
                    <p class="text-xs text-gray-500">
                        <i class="fas fa-info-circle mr-1"></i>
                        Votre réponse sera marquée comme provenant d'un administrateur
                    </p>
                    <button type="submit" 
                            :disabled="replyForm.loading"
                            class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors shadow-sm disabled:opacity-50">
                        <i class="fas fa-spinner fa-spin mr-2" x-show="replyForm.loading"></i>
                        <i class="fas fa-paper-plane mr-2" x-show="!replyForm.loading"></i>
                        Publier la réponse
                    </button>
                </div>
            </form>
        </div>
        
        <!-- Modal de confirmation de suppression -->
        <div x-show="deleteModalOpen" 
             class="fixed inset-0 z-50 overflow-y-auto"
             x-transition
             style="display: none;">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 bg-gray-900 bg-opacity-50" @click="deleteModalOpen = false"></div>
                <div class="relative bg-white rounded-2xl max-w-md w-full shadow-2xl">
                    <div class="p-6 text-center">
                        <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-exclamation-triangle text-red-600 text-2xl"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Confirmer la suppression</h3>
                        <p class="text-gray-500 mb-6">
                            Êtes-vous sûr de vouloir supprimer ce sujet ?<br>
                            <span class="text-red-600 font-medium">Cette action est irréversible.</span>
                        </p>
                        <div class="flex justify-center space-x-3">
                            <button @click="deleteModalOpen = false"
                                    class="px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                                Annuler
                            </button>
                            <button @click="deleteTopic()"
                                    class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                                Supprimer
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
    </div>
</div>
@endsection

@push('scripts')
<script>
    function topicAdminManager(topicId) {
        return {
            // État du sujet
            topicData: {
                is_sticky: @json($topic->is_sticky),
                status: @json($topic->status)
            },
            
            // Toast notification
            toast: {
                show: false,
                type: 'success',
                message: ''
            },
            
            // Formulaire de réponse
            replyForm: {
                content: '',
                loading: false
            },
            
            // Modal de suppression
            deleteModalOpen: false,
            
            // Méthodes d'initialisation
            init() {
                console.log('Topic Admin Manager initialisé pour le sujet #' + topicId);
            },
            
            // Afficher une notification
            showToast(type, message) {
                this.toast.show = true;
                this.toast.type = type;
                this.toast.message = message;
                
                setTimeout(() => {
                    this.toast.show = false;
                }, 5000);
            },
            
            // Toggle épingler/désépingler
            async togglePin() {
                try {
                    const response = await fetch(`/admin/forum/topics/${topicId}/pin`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        }
                    });
                    
                    const data = await response.json();
                    
                    if (data.success) {
                        this.topicData.is_sticky = !this.topicData.is_sticky;
                        this.showToast('success', this.topicData.is_sticky ? 'Sujet épinglé !' : 'Sujet désépinglé !');
                    }
                } catch (error) {
                    console.error('Erreur:', error);
                    this.showToast('error', 'Erreur lors de l\'action');
                }
            },
            
            // Toggle fermer/ouvrir
            async toggleClose() {
                try {
                    const response = await fetch(`/admin/forum/topics/${topicId}/close`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        }
                    });
                    
                    const data = await response.json();
                    
                    if (data.success) {
                        this.topicData.status = this.topicData.status === 'closed' ? 'open' : 'closed';
                        this.showToast('success', this.topicData.status === 'closed' ? 'Sujet fermé !' : 'Sujet rouvert !');
                    }
                } catch (error) {
                    console.error('Erreur:', error);
                    this.showToast('error', 'Erreur lors de l\'action');
                }
            },
            
            // Marquer un message comme solution
            async markAsSolution(postId) {
                try {
                    const response = await fetch(`/admin/forum/posts/${postId}/solution`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        }
                    });
                    
                    const data = await response.json();
                    
                    if (data.success) {
                        this.showToast('success', 'Message marqué comme solution !');
                        setTimeout(() => window.location.reload(), 1000);
                    }
                } catch (error) {
                    console.error('Erreur:', error);
                    this.showToast('error', 'Erreur lors du marquage');
                }
            },
            
            // Supprimer un message
            async deletePost(postId) {
                if (!confirm('Supprimer ce message ?')) return;
                
                try {
                    const response = await fetch(`/admin/forum/posts/${postId}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        }
                    });
                    
                    const data = await response.json();
                    
                    if (data.success) {
                        this.showToast('success', 'Message supprimé !');
                        setTimeout(() => window.location.reload(), 1000);
                    }
                } catch (error) {
                    console.error('Erreur:', error);
                    this.showToast('error', 'Erreur lors de la suppression');
                }
            },
            
            // Soumettre une réponse
            async submitReply() {
                if (!this.replyForm.content.trim()) {
                    this.showToast('error', 'Veuillez saisir un message');
                    return;
                }
                
                this.replyForm.loading = true;
                
                try {
                    const response = await fetch(`/admin/forum/topics/${topicId}/posts`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            content: this.replyForm.content
                        })
                    });
                    
                    const data = await response.json();
                    
                    if (data.success) {
                        this.replyForm.content = '';
                        this.showToast('success', 'Réponse publiée avec succès !');
                        setTimeout(() => window.location.reload(), 1000);
                    } else {
                        this.showToast('error', data.message || 'Erreur lors de la publication');
                    }
                } catch (error) {
                    console.error('Erreur:', error);
                    this.showToast('error', 'Erreur lors de la publication');
                } finally {
                    this.replyForm.loading = false;
                }
            },
            
            // Confirmer la suppression du sujet
            confirmDelete() {
                this.deleteModalOpen = true;
            },
            
            // Supprimer le sujet
            async deleteTopic() {
                try {
                    const response = await fetch(`/admin/forum/topics/${topicId}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        }
                    });
                    
                    const data = await response.json();
                    
                    if (data.success) {
                        window.location.href = '{{ route("admin.forum.topics.index") }}';
                    }
                } catch (error) {
                    console.error('Erreur:', error);
                    this.showToast('error', 'Erreur lors de la suppression');
                    this.deleteModalOpen = false;
                }
            }
        }
    }
</script>
@endpush