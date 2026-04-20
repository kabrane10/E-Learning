@extends('layouts.admin')

@section('title', 'Gestion du chat')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- En-tête -->
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Gestion du chat</h1>
                <p class="text-gray-500 mt-1">Surveillez et gérez les conversations en temps réel</p>
            </div>
            <div class="flex space-x-3">
                <button onclick="openCreateConversationModal()" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                    <i class="fas fa-plus mr-2"></i>Nouvelle conversation
                </button>
                <a href="{{ route('admin.chat.conversations') }}" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                    <i class="fas fa-list mr-2"></i>Toutes les conversations
                </a>
                <a href="{{ route('admin.chat.messages') }}" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                    <i class="fas fa-envelope mr-2"></i>Messages
                </a>
                <a href="{{ route('admin.chat.settings') }}" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                    <i class="fas fa-cog mr-2"></i>Paramètres
                </a>
            </div>
        </div>

        <!-- Statistiques -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-500 uppercase">Conversations</p>
                        <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_conversations'] ?? 0) }}</p>
                    </div>
                    <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-comments text-indigo-600"></i>
                    </div>
                </div>
                <div class="mt-2 text-xs text-gray-500">
                    {{ $stats['private_conversations'] ?? 0 }} privées • {{ $stats['course_conversations'] ?? 0 }} cours
                </div>
            </div>
            
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-500 uppercase">Messages</p>
                        <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_messages'] ?? 0) }}</p>
                    </div>
                    <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-envelope text-green-600"></i>
                    </div>
                </div>
                <div class="mt-2 text-xs text-gray-500">
                    {{ $stats['messages_today'] ?? 0 }} aujourd'hui
                </div>
            </div>
            
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-500 uppercase">Conversations actives</p>
                        <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['active_conversations'] ?? 0) }}</p>
                    </div>
                    <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-fire text-yellow-600"></i>
                    </div>
                </div>
                <div class="mt-2 text-xs text-gray-500">7 derniers jours</div>
            </div>
            
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-500 uppercase">Participants</p>
                        <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_participants'] ?? 0) }}</p>
                    </div>
                    <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-users text-purple-600"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Conversations récentes -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200 flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">Conversations récentes</h3>
                    <a href="{{ route('admin.chat.conversations') }}" class="text-sm text-indigo-600 hover:text-indigo-700">
                        Voir tout
                    </a>
                </div>
                <div class="divide-y divide-gray-200">
                    @forelse($recentConversations ?? [] as $conversation)
                        <a href="{{ route('admin.chat.conversations.show', $conversation) }}" class="block p-4 hover:bg-gray-50 transition-colors">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 bg-indigo-100 rounded-full flex items-center justify-center">
                                        @if($conversation->type === 'private')
                                            <i class="fas fa-user text-indigo-600"></i>
                                        @elseif($conversation->type === 'course')
                                            <i class="fas fa-book text-indigo-600"></i>
                                        @else
                                            <i class="fas fa-users text-indigo-600"></i>
                                        @endif
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900">
                                            @if($conversation->type === 'private')
                                                Conversation privée #{{ $conversation->id }}
                                            @elseif($conversation->type === 'course' && $conversation->course)
                                                {{ $conversation->course->title }}
                                            @else
                                                {{ $conversation->title ?? 'Groupe' }}
                                            @endif
                                        </p>
                                        <p class="text-xs text-gray-500">
                                            {{ $conversation->messages_count ?? 0 }} messages • 
                                            {{ $conversation->participants_count ?? 0 }} participants
                                        </p>
                                    </div>
                                </div>
                                <span class="text-xs text-gray-400">
                                    {{ $conversation->last_message_at ? $conversation->last_message_at->diffForHumans() : '-' }}
                                </span>
                            </div>
                        </a>
                    @empty
                        <div class="p-6 text-center text-gray-500">
                            Aucune conversation récente
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Utilisateurs actifs -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Utilisateurs les plus actifs</h3>
                </div>
                <div class="divide-y divide-gray-200">
                    @forelse($activeUsers ?? [] as $user)
                        <div class="p-4">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-3">
                                    <img src="{{ $user->avatar }}" class="w-10 h-10 rounded-full">
                                    <div>
                                        <p class="font-medium text-gray-900">{{ $user->name }}</p>
                                        <p class="text-xs text-gray-500">{{ $user->email }}</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="font-semibold text-gray-900">{{ $user->messages_count ?? 0 }}</p>
                                    <p class="text-xs text-gray-500">messages (30j)</p>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="p-6 text-center text-gray-500">
                            Aucune activité récente
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Graphique d'activité -->
        <div class="mt-6 bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Activité quotidienne (30 derniers jours)</h3>
            </div>
            <div class="p-6">
                <div style="position: relative; height: 250px;">
                    <canvas id="chatActivityChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Création de conversation -->
<div id="createConversationModal" class="fixed inset-0 z-50 overflow-y-auto hidden">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-50" onclick="closeCreateConversationModal()"></div>
        <div class="relative bg-white rounded-2xl max-w-md w-full shadow-2xl">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Nouvelle conversation</h3>
            </div>
            
            <form id="createConversationForm" onsubmit="createConversation(event)">
                @csrf
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Type de conversation</label>
                        <select name="type" id="conversationType" class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500" onchange="toggleConversationFields()">
                            <option value="private">Message privé</option>
                            <option value="course">Discussion de cours</option>
                            <option value="group">Groupe</option>
                        </select>
                    </div>
                    
                    <div id="userField">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Destinataire</label>
                        <select name="user_id" class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Sélectionner un utilisateur</option>
                            @foreach(\App\Models\User::limit(50)->get() as $u)
                                <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->email }})</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div id="courseField" style="display: none;">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Cours</label>
                        <select name="course_id" class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Sélectionner un cours</option>
                            @foreach(\App\Models\Course::limit(50)->get() as $c)
                                <option value="{{ $c->id }}">{{ $c->title }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div id="titleField" style="display: none;">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nom du groupe</label>
                        <input type="text" name="title" class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Ex: Groupe de travail">
                    </div>
                </div>
                
                <div class="px-6 py-4 bg-gray-50 rounded-b-2xl flex justify-end space-x-3">
                    <button type="button" onclick="closeCreateConversationModal()" class="px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                        Annuler
                    </button>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                        Créer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    // Modal
    function openCreateConversationModal() {
        document.getElementById('createConversationModal').classList.remove('hidden');
    }
    
    function closeCreateConversationModal() {
        document.getElementById('createConversationModal').classList.add('hidden');
    }
    
    function toggleConversationFields() {
        const type = document.getElementById('conversationType').value;
        document.getElementById('userField').style.display = type === 'private' ? 'block' : 'none';
        document.getElementById('courseField').style.display = type === 'course' ? 'block' : 'none';
        document.getElementById('titleField').style.display = type === 'group' ? 'block' : 'none';
    }
    
    function createConversation(event) {
        event.preventDefault();
        
        const form = event.target;
        const formData = new FormData(form);
        
        fetch('{{ route("admin.chat.conversations.store") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                closeCreateConversationModal();
                if (data.redirect) {
                    window.location.href = data.redirect;
                } else {
                    window.location.reload();
                }
            } else {
                alert(data.message || 'Erreur lors de la création');
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            alert('Erreur lors de la création de la conversation');
        });
    }
    
    // Fermer avec Escape
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            closeCreateConversationModal();
        }
    });
    
    // Graphique
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('chatActivityChart')?.getContext('2d');
        if (ctx) {
            const dailyData = @json($dailyActivity ?? ['labels' => [], 'data' => []]);
            
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: dailyData.labels.length > 0 ? dailyData.labels : ['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim'],
                    datasets: [{
                        label: 'Messages',
                        data: dailyData.data.length > 0 ? dailyData.data : [0, 0, 0, 0, 0, 0, 0],
                        backgroundColor: 'rgba(79, 70, 229, 0.8)',
                        borderRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: '#e5e7eb' },
                            ticks: { precision: 0 }
                        },
                        x: {
                            grid: { display: false }
                        }
                    }
                }
            });
        }
    });
</script>
@endpush