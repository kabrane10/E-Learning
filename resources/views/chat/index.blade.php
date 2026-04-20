@extends('layouts.chat')

@section('title', 'Messages')

@section('content')
<div class="h-screen flex bg-white" x-data="chatIndexManager()" x-init="init()">
    <!-- Sidebar des conversations -->
    <div class="w-80 lg:w-96 border-r border-gray-200 flex flex-col h-full">
        <!-- En-tête -->
        <div class="p-4 border-b border-gray-200">
            <div class="flex items-center justify-between mb-3">
                <h1 class="text-xl font-bold text-gray-900">Messages</h1>
                <button @click="openNewConversationModal = true" 
                        class="w-8 h-8 bg-indigo-600 text-white rounded-full flex items-center justify-center hover:bg-indigo-700 transition-colors">
                    <i class="fas fa-plus"></i>
                </button>
            </div>
            <div class="relative">
                <input type="text" 
                       placeholder="Rechercher..." 
                       x-model="searchQuery"
                       class="w-full pl-10 pr-4 py-2.5 bg-gray-100 border-0 rounded-xl focus:ring-2 focus:ring-indigo-500 text-sm">
                <i class="fas fa-search absolute left-3 top-3.5 text-gray-400 text-sm"></i>
            </div>
        </div>
        
        <!-- Liste des conversations -->
        <div class="flex-1 overflow-y-auto">
            <template x-for="conversation in filteredConversations" :key="conversation.id">
                <a :href="'/chat/' + conversation.id" 
                   class="conversation-item flex items-start p-4 border-b border-gray-100 cursor-pointer transition-colors"
                   :class="{ 'active': activeConversationId == conversation.id }">
                    <!-- Avatar -->
                    <div class="relative flex-shrink-0">
                        <template x-if="conversation.type === 'private'">
                            <img :src="conversation.other_user?.avatar || 'https://ui-avatars.com/api/?name=' + encodeURIComponent(conversation.other_user?.name || 'User')" 
                                 class="w-12 h-12 rounded-full">
                        </template>
                        <template x-if="conversation.type === 'course'">
                            <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-book text-blue-600"></i>
                            </div>
                        </template>
                        <template x-if="conversation.type === 'group'">
                            <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-users text-purple-600"></i>
                            </div>
                        </template>
                        
                        <!-- Badge messages non lus -->
                        <span x-show="conversation.unread_count > 0" 
                              class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 text-white text-xs rounded-full flex items-center justify-center font-medium"
                              x-text="conversation.unread_count > 9 ? '9+' : conversation.unread_count"></span>
                    </div>
                    
                    <!-- Infos -->
                    <div class="ml-3 flex-1 min-w-0">
                        <div class="flex items-center justify-between">
                            <h3 class="font-medium text-gray-900 truncate" 
                                x-text="conversation.type === 'private' ? conversation.other_user?.name : (conversation.title || 'Conversation')"></h3>
                            <span class="text-xs text-gray-400 flex-shrink-0 ml-2" x-text="formatTime(conversation.last_message_at)"></span>
                        </div>
                        <div class="flex items-center mt-1">
                            <p class="text-sm text-gray-500 truncate flex-1" x-text="getLastMessagePreview(conversation)"></p>
                            <span x-show="conversation.is_pinned" class="ml-2 text-indigo-400">
                                <i class="fas fa-thumbtack text-xs"></i>
                            </span>
                            <span x-show="conversation.is_muted" class="ml-2 text-gray-400">
                                <i class="fas fa-bell-slash text-xs"></i>
                            </span>
                        </div>
                    </div>
                </a>
            </template>
            
            <!-- Message si aucune conversation -->
            <div x-show="filteredConversations.length === 0" class="p-8 text-center text-gray-500">
                <i class="fas fa-comments text-4xl mb-3 opacity-30"></i>
                <p class="text-sm">Aucune conversation</p>
                <button @click="openNewConversationModal = true" class="mt-4 text-indigo-600 hover:text-indigo-700 text-sm font-medium">
                    <i class="fas fa-plus mr-1"></i>Nouvelle conversation
                </button>
            </div>
        </div>
    </div>
    
    <!-- Zone principale - Message de bienvenue -->
    <div class="flex-1 flex items-center justify-center bg-gray-50">
        <div class="text-center max-w-md px-4">
            <div class="w-20 h-20 bg-indigo-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-comments text-indigo-600 text-3xl"></i>
            </div>
            <h2 class="text-xl font-semibold text-gray-900 mb-2">Vos messages</h2>
            <p class="text-gray-500 mb-6">
                Sélectionnez une conversation pour voir les messages ou démarrez une nouvelle discussion.
            </p>
            <button @click="openNewConversationModal = true" 
                    class="inline-flex items-center px-6 py-3 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition-colors shadow-md">
                <i class="fas fa-plus mr-2"></i>Nouvelle conversation
            </button>
        </div>
    </div>
    
    <!-- Modal Nouvelle conversation -->
    <div x-show="openNewConversationModal" 
         class="fixed inset-0 z-50 overflow-y-auto"
         x-transition
         style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-gray-900 bg-opacity-50" @click="openNewConversationModal = false"></div>
            <div class="relative bg-white rounded-2xl max-w-md w-full shadow-2xl">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Nouvelle conversation</h3>
                </div>
                
                <form @submit.prevent="createConversation">
                    <div class="p-6 space-y-4">
                        <!-- Type de conversation -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Type</label>
                            <div class="grid grid-cols-3 gap-2">
                                <button type="button"
                                        @click="newConversation.type = 'private'"
                                        class="py-2 px-3 rounded-lg text-sm font-medium transition-colors"
                                        :class="newConversation.type === 'private' ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'">
                                    <i class="fas fa-user mr-1"></i>Privé
                                </button>
                                <button type="button"
                                        @click="newConversation.type = 'course'"
                                        class="py-2 px-3 rounded-lg text-sm font-medium transition-colors"
                                        :class="newConversation.type === 'course' ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'">
                                    <i class="fas fa-book mr-1"></i>Cours
                                </button>
                                <button type="button"
                                        @click="newConversation.type = 'group'"
                                        class="py-2 px-3 rounded-lg text-sm font-medium transition-colors"
                                        :class="newConversation.type === 'group' ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'">
                                    <i class="fas fa-users mr-1"></i>Groupe
                                </button>
                            </div>
                        </div>
                        
                        <!-- Destinataire (privé) -->
                        <div x-show="newConversation.type === 'private'">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Destinataire</label>
                            <select x-model="newConversation.user_id" required
                                    class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">Sélectionner un utilisateur</option>
                                @foreach($availableUsers ?? [] as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <!-- Cours (cours) -->
                        <div x-show="newConversation.type === 'course'">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Cours</label>
                            <select x-model="newConversation.course_id" required
                                    class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">Sélectionner un cours</option>
                                @foreach($availableCourses ?? [] as $course)
                                    <option value="{{ $course->id }}">{{ $course->title }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <!-- Nom du groupe -->
                        <div x-show="newConversation.type === 'group'">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Nom du groupe</label>
                            <input type="text" 
                                   x-model="newConversation.title" 
                                   required
                                   class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500"
                                   placeholder="Ex: Groupe de projet">
                        </div>
                    </div>
                    
                    <div class="px-6 py-4 bg-gray-50 rounded-b-2xl flex justify-end space-x-3">
                        <button type="button" @click="openNewConversationModal = false" 
                                class="px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                            Annuler
                        </button>
                        <button type="submit" 
                                class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                            Créer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function chatIndexManager() {
        return {
            conversations: @json($conversations ?? []),
            activeConversationId: null,
            searchQuery: '',
            openNewConversationModal: false,
            newConversation: {
                type: 'private',
                user_id: '',
                course_id: '',
                title: ''
            },
            
            get filteredConversations() {
                if (!this.searchQuery) return this.conversations;
                const query = this.searchQuery.toLowerCase();
                return this.conversations.filter(c => {
                    const name = c.type === 'private' ? (c.other_user?.name || '') : (c.title || '');
                    return name.toLowerCase().includes(query);
                });
            },
            
            init() {
                // Écouter les nouveaux messages via Echo
                this.conversations.forEach(conversation => {
                    window.Echo.private(`conversation.${conversation.id}`)
                        .listen('.message.sent', (e) => {
                            this.handleNewMessage(e);
                        });
                });
            },
            
            handleNewMessage(event) {
                const conversation = this.conversations.find(c => c.id === event.conversation_id);
                if (conversation) {
                    conversation.last_message = {
                        content: event.content,
                        user: event.user
                    };
                    conversation.last_message_at = event.created_at;
                    
                    if (conversation.id !== this.activeConversationId) {
                        conversation.unread_count = (conversation.unread_count || 0) + 1;
                    }
                }
            },
            
            formatTime(timestamp) {
                if (!timestamp) return '';
                const date = new Date(timestamp);
                const now = new Date();
                const diff = now - date;
                
                if (diff < 60000) return 'À l\'instant';
                if (diff < 3600000) return Math.floor(diff / 60000) + ' min';
                if (diff < 86400000) return date.toLocaleTimeString('fr', { hour: '2-digit', minute: '2-digit' });
                return date.toLocaleDateString('fr', { day: '2-digit', month: '2-digit' });
            },
            
            getLastMessagePreview(conversation) {
                if (!conversation.last_message) return 'Aucun message';
                const content = conversation.last_message.content;
                const isMine = conversation.last_message.user?.id === {{ auth()->id() }};
                const prefix = isMine ? 'Vous : ' : '';
                return prefix + (content.length > 30 ? content.substring(0, 30) + '...' : content);
            },
            
            createConversation() {
                const data = {
                    type: this.newConversation.type,
                    user_id: this.newConversation.type === 'private' ? this.newConversation.user_id : null,
                    course_id: this.newConversation.type === 'course' ? this.newConversation.course_id : null,
                    title: this.newConversation.type === 'group' ? this.newConversation.title : null,
                    _token: document.querySelector('meta[name="csrf-token"]').content
                };
                
                fetch('{{ route("conversations.store") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(data)
                })
                .then(response => {
                    if (response.ok) {
                        return response.json();
                    }
                    throw new Error('Erreur lors de la création');
                })
                .then(data => {
                    if (data.redirect) {
                        window.location.href = data.redirect;
                    } else if (data.conversation) {
                        window.location.href = '/chat/' + data.conversation.id;
                    } else {
                        window.location.reload();
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    alert('Erreur lors de la création de la conversation');
                });
            }
        }
    }
</script>
@endpush