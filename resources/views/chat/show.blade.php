@extends('layouts.chat')

@section('title', $conversation->title ?? ($conversation->other_user->name ?? 'Conversation'))

@section('content')
<div class="h-screen flex bg-white" x-data="chatShowManager({{ $conversation->id }})" x-init="init()">
    <!-- Sidebar des conversations (version compacte) -->
    <div class="hidden lg:block w-80 border-r border-gray-200">
        <div class="p-4 border-b border-gray-200">
            <a href="{{ route('chat.index') }}" class="inline-flex items-center text-indigo-600 hover:text-indigo-700 mb-3">
                <i class="fas fa-arrow-left mr-2"></i>Retour aux messages
            </a>
            <h2 class="text-lg font-semibold text-gray-900">Conversations</h2>
        </div>
        <!-- Liste des conversations (similaire à index) -->
    </div>
    
    <!-- Zone de chat principale -->
    <div class="flex-1 flex flex-col h-full">
        <!-- En-tête de la conversation -->
        <div class="px-6 py-4 border-b border-gray-200 bg-white flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <!-- Bouton retour mobile -->
                <a href="{{ route('chat.index') }}" class="lg:hidden text-gray-500 hover:text-gray-700 mr-2">
                    <i class="fas fa-arrow-left"></i>
                </a>
                
                <!-- Avatar / Info -->
                @if($conversation->type === 'private')
                    <img src="{{ $conversation->other_user->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($conversation->other_user->name) }}" 
                         class="w-10 h-10 rounded-full">
                    <div>
                        <h2 class="font-semibold text-gray-900">{{ $conversation->other_user->name }}</h2>
                        <p class="text-xs text-gray-500" id="typing-indicator"></p>
                    </div>
                @elseif($conversation->type === 'course')
                    <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-book text-blue-600"></i>
                    </div>
                    <div>
                        <h2 class="font-semibold text-gray-900">{{ $conversation->course->title ?? 'Cours' }}</h2>
                        <p class="text-xs text-gray-500">{{ $conversation->participants_count }} participants</p>
                    </div>
                @else
                    <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-users text-purple-600"></i>
                    </div>
                    <div>
                        <h2 class="font-semibold text-gray-900">{{ $conversation->title ?? 'Groupe' }}</h2>
                        <p class="text-xs text-gray-500">{{ $conversation->participants_count }} participants</p>
                    </div>
                @endif
            </div>
            
            <!-- Actions -->
            <div class="flex items-center space-x-2" x-data="{ open: false }">
                <button @click="toggleMute()" 
                        class="p-2 rounded-lg hover:bg-gray-100 transition-colors"
                        :class="isMuted ? 'text-gray-400' : 'text-gray-600'">
                    <i class="fas" :class="isMuted ? 'fa-bell-slash' : 'fa-bell'"></i>
                </button>
                <button @click="togglePin()" 
                        class="p-2 rounded-lg hover:bg-gray-100 transition-colors"
                        :class="isPinned ? 'text-indigo-600' : 'text-gray-600'">
                    <i class="fas fa-thumbtack"></i>
                </button>
                <button @click="open = !open" class="p-2 rounded-lg hover:bg-gray-100 transition-colors">
                    <i class="fas fa-ellipsis-v text-gray-600"></i>
                </button>
                
                <!-- Menu déroulant -->
                <div x-show="open" 
                     @click.away="open = false"
                     x-transition
                     class="absolute right-6 top-16 mt-2 w-56 bg-white rounded-xl shadow-xl border border-gray-200 py-1 z-50">
                    <a href="{{ route('conversations.show', $conversation) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                        <i class="fas fa-info-circle mr-2"></i>Détails
                    </a>
                    <button @click="leaveConversation()" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                        <i class="fas fa-sign-out-alt mr-2"></i>Quitter
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Messages -->
        <div class="flex-1 overflow-y-auto p-6 bg-gray-50 messages-container" id="messagesContainer" @scroll="handleScroll">
            <!-- Indicateur de chargement -->
            <div x-show="isLoadingMore" class="text-center py-4">
                <i class="fas fa-spinner fa-spin text-indigo-600"></i>
                <span class="ml-2 text-sm text-gray-500">Chargement...</span>
            </div>
            
            <!-- Liste des messages -->
            <template x-for="message in messages" :key="message.id">
                <div class="flex mb-4" :class="message.user_id === {{ auth()->id() }} ? 'justify-end' : 'justify-start'">
                    <div class="flex items-end space-x-2" :class="message.user_id === {{ auth()->id() }} ? 'flex-row-reverse space-x-reverse' : ''">
                        <!-- Avatar (seulement pour les messages reçus) -->
                        <template x-if="message.user_id !== {{ auth()->id() }}">
                            <img :src="message.user?.avatar || 'https://ui-avatars.com/api/?name=' + encodeURIComponent(message.user?.name || 'User')" 
                                 class="w-8 h-8 rounded-full flex-shrink-0">
                        </template>
                        
                        <!-- Bulle de message -->
                        <div class="message-bubble" :class="message.user_id === {{ auth()->id() }} ? 'sent' : 'received'">
                            <!-- Réponse à -->
                            <template x-if="message.reply_to">
                                <div class="text-xs opacity-75 mb-2 p-2 bg-black/10 rounded-lg">
                                    <p class="font-medium" x-text="message.reply_to.user?.name"></p>
                                    <p class="truncate" x-text="message.reply_to.content"></p>
                                </div>
                            </template>
                            
                            <!-- Image -->
                            <template x-if="message.type === 'image'">
                                <img :src="message.metadata?.file_url" class="max-w-full rounded-lg mb-2">
                            </template>
                            
                            <!-- Fichier -->
                            <template x-if="message.type === 'file'">
                                <a :href="message.metadata?.file_url" target="_blank" class="flex items-center p-2 bg-white/20 rounded-lg mb-2">
                                    <i class="fas fa-file mr-2"></i>
                                    <span x-text="message.metadata?.file_name"></span>
                                </a>
                            </template>
                            
                            <!-- Contenu texte -->
                            <p x-text="message.content" class="whitespace-pre-wrap"></p>
                            
                            <!-- Métadonnées -->
                            <div class="flex items-center justify-end mt-1 space-x-1 text-xs opacity-75">
                                <span x-show="message.is_edited">(modifié)</span>
                                <span x-text="formatMessageTime(message.created_at)"></span>
                                <template x-if="message.user_id === {{ auth()->id() }}">
                                    <span x-show="message.read_at">
                                        <i class="fas fa-check-double" :class="{ 'text-blue-400': message.read_at }"></i>
                                    </span>
                                </template>
                            </div>
                        </div>
                        
                        <!-- Menu message -->
                        <div class="relative" x-data="{ menuOpen: false }">
                            <button @click="menuOpen = !menuOpen" class="p-1 text-gray-400 hover:text-gray-600">
                                <i class="fas fa-ellipsis-v text-xs"></i>
                            </button>
                            <div x-show="menuOpen" @click.away="menuOpen = false" class="absolute bottom-full mb-2 bg-white rounded-lg shadow-lg border border-gray-200 py-1 z-10">
                                <button @click="replyTo(message)" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                    <i class="fas fa-reply mr-2"></i>Répondre
                                </button>
                                <template x-if="message.user_id === {{ auth()->id() }}">
                                    <button @click="deleteMessage(message.id)" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                        <i class="fas fa-trash mr-2"></i>Supprimer
                                    </button>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
            
            <!-- Indicateur "en train d'écrire" -->
            <div x-show="typingUsers.length > 0" class="flex justify-start mb-4">
                <div class="typing-indicator bg-gray-200 rounded-2xl">
                    <span></span>
                    <span></span>
                    <span></span>
                    <span class="ml-2 text-xs text-gray-600" x-text="getTypingText()"></span>
                </div>
            </div>
        </div>
        
        <!-- Zone de réponse -->
        <div class="p-4 border-t border-gray-200 bg-white">
            <!-- Réponse à -->
            <div x-show="replyingTo" class="mb-3 p-3 bg-gray-100 rounded-lg flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500">Réponse à <span x-text="replyingTo?.user?.name"></span></p>
                    <p class="text-sm text-gray-700 truncate" x-text="replyingTo?.content"></p>
                </div>
                <button @click="cancelReply()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <!-- Formulaire d'envoi -->
            <form @submit.prevent="sendMessage" class="flex items-end space-x-3">
                <div class="relative">
                    <input type="file" 
                           id="fileInput" 
                           @change="uploadFile" 
                           accept="image/*,.pdf,.doc,.docx"
                           class="hidden">
                    <button type="button" 
                            @click="document.getElementById('fileInput').click()"
                            class="p-3 text-gray-500 hover:text-indigo-600 hover:bg-gray-100 rounded-full transition-colors">
                        <i class="fas fa-paperclip"></i>
                    </button>
                </div>
                
                <div class="flex-1 relative">
                    <textarea id="messageInput" 
                              x-model="newMessage" 
                              @keydown.enter.prevent="if(!e.shiftKey) sendMessage()"
                              @input="handleTyping"
                              rows="1"
                              placeholder="Écrivez votre message..."
                              class="w-full px-4 py-3 pr-12 border border-gray-300 rounded-xl resize-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                              style="max-height: 120px;"></textarea>
                    <button type="button" 
                            @click="insertEmoji"
                            class="absolute right-3 bottom-3 text-gray-400 hover:text-gray-600">
                        <i class="far fa-smile"></i>
                    </button>
                </div>
                
                <button type="submit" 
                        :disabled="!newMessage.trim()"
                        class="p-3 bg-indigo-600 text-white rounded-full hover:bg-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function chatShowManager(conversationId) {
        return {
            messages: @json($messages->items() ?? []),
            newMessage: '',
            replyingTo: null,
            typingUsers: [],
            typingTimeout: null,
            isMuted: @json($conversation->participants->firstWhere('user_id', auth()->id())?->is_muted ?? false),
            isPinned: @json($conversation->participants->firstWhere('user_id', auth()->id())?->is_pinned ?? false),
            isLoadingMore: false,
            page: 1,
            hasMorePages: @json($messages->hasMorePages() ?? false),
            
            init() {
                this.scrollToBottom();
                
                // Écouter les nouveaux messages
                window.Echo.private(`conversation.${conversationId}`)
                    .listen('.message.sent', (e) => {
                        if (e.user_id !== {{ auth()->id() }}) {
                            this.messages.push(e);
                            this.$nextTick(() => this.scrollToBottom());
                            this.markAsRead([e.id]);
                        }
                    })
                    .listen('.message.read', (e) => {
                        if (e.user_id !== {{ auth()->id() }}) {
                            this.messages.forEach(msg => {
                                if (e.message_ids.includes(msg.id)) {
                                    msg.read_at = new Date().toISOString();
                                }
                            });
                        }
                    })
                    .listen('.user.typing', (e) => {
                        if (e.user_id !== {{ auth()->id() }}) {
                            if (e.is_typing) {
                                if (!this.typingUsers.includes(e.user_name)) {
                                    this.typingUsers.push(e.user_name);
                                }
                            } else {
                                this.typingUsers = this.typingUsers.filter(u => u !== e.user_name);
                            }
                        }
                    });
            },
            
            scrollToBottom() {
                this.$nextTick(() => {
                    const container = document.getElementById('messagesContainer');
                    if (container) {
                        container.scrollTop = container.scrollHeight;
                    }
                });
            },
            
            formatMessageTime(timestamp) {
                const date = new Date(timestamp);
                return date.toLocaleTimeString('fr', { hour: '2-digit', minute: '2-digit' });
            },
            
            getTypingText() {
                if (this.typingUsers.length === 0) return '';
                if (this.typingUsers.length === 1) return `${this.typingUsers[0]} écrit...`;
                if (this.typingUsers.length === 2) return `${this.typingUsers[0]} et ${this.typingUsers[1]} écrivent...`;
                return `${this.typingUsers.length} personnes écrivent...`;
            },
            
            sendMessage() {
                if (!this.newMessage.trim()) return;
                
                const content = this.newMessage;
                const replyToId = this.replyingTo?.id;
                
                this.newMessage = '';
                this.cancelReply();
                
                fetch(`/chat/${conversationId}/send`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        content: content,
                        reply_to_id: replyToId
                    })
                })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        this.messages.push(data.message);
                        this.scrollToBottom();
                    }
                });
            },
            
            handleTyping() {
                if (this.typingTimeout) {
                    clearTimeout(this.typingTimeout);
                }
                
                fetch(`/chat/${conversationId}/typing`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ is_typing: true })
                });
                
                this.typingTimeout = setTimeout(() => {
                    fetch(`/chat/${conversationId}/typing`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({ is_typing: false })
                    });
                }, 2000);
            },
            
            markAsRead(messageIds) {
                fetch(`/chat/${conversationId}/read`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ message_ids: messageIds })
                });
            },
            
            replyTo(message) {
                this.replyingTo = message;
                document.getElementById('messageInput').focus();
            },
            
            cancelReply() {
                this.replyingTo = null;
            },
            
            uploadFile(event) {
                const file = event.target.files[0];
                if (!file) return;
                
                const formData = new FormData();
                formData.append('file', file);
                formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);
                
                fetch(`/chat/${conversationId}/upload`, {
                    method: 'POST',
                    body: formData
                })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        this.messages.push(data.message);
                        this.scrollToBottom();
                    }
                });
                
                event.target.value = '';
            },
            
            deleteMessage(messageId) {
                if (!confirm('Supprimer ce message ?')) return;
                
                fetch(`/chat/messages/${messageId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                })
                .then(() => {
                    this.messages = this.messages.filter(m => m.id !== messageId);
                });
            },
            
            toggleMute() {
                fetch(`/conversations/${conversationId}/mute`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                })
                .then(r => r.json())
                .then(data => {
                    this.isMuted = data.is_muted;
                });
            },
            
            togglePin() {
                fetch(`/conversations/${conversationId}/pin`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                })
                .then(r => r.json())
                .then(data => {
                    this.isPinned = data.is_pinned;
                });
            },
            
            leaveConversation() {
                if (!confirm('Quitter cette conversation ?')) return;
                
                fetch(`/conversations/${conversationId}/leave`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                })
                .then(() => {
                    window.location.href = '/messages';
                });
            },
            
            handleScroll() {
                const container = document.getElementById('messagesContainer');
                if (container.scrollTop === 0 && this.hasMorePages && !this.isLoadingMore) {
                    this.loadMoreMessages();
                }
            },
            
            loadMoreMessages() {
                this.isLoadingMore = true;
                this.page++;
                
                fetch(`/chat/${conversationId}?page=${this.page}`)
                    .then(r => r.json())
                    .then(data => {
                        this.messages = [...data.messages, ...this.messages];
                        this.hasMorePages = data.has_more;
                        this.isLoadingMore = false;
                    });
            }
        }
    }
</script>
@endpush