@extends('layouts.admin')

@section('title', 'Logs système')

@section('breadcrumb')
<nav class="flex" aria-label="Breadcrumb">
    <ol class="flex items-center space-x-2">
        <li><a href="{{ route('admin.dashboard') }}" class="text-gray-400 hover:text-gray-500"><i class="fas fa-home"></i></a></li>
        <li><i class="fas fa-chevron-right text-gray-300 text-xs"></i></li>
        <li class="text-sm font-medium text-gray-700">Logs système</li>
    </ol>
</nav>
@endsection

@section('content')
<div class="py-6" x-data="logsManager()">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Logs système</h1>
                <p class="text-gray-500 mt-1">Surveillez l'activité de votre plateforme</p>
            </div>
            <div class="flex space-x-2">
                <button @click="refreshLogs()" class="px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                    <i class="fas fa-sync-alt mr-2"></i>Actualiser
                </button>
                <button @click="clearLogs()" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                    <i class="fas fa-trash mr-2"></i>Effacer les logs
                </button>
            </div>
        </div>
        
        <!-- Filtres -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Niveau</label>
                    <select x-model="filters.level" class="w-full border-gray-300 rounded-lg">
                        <option value="">Tous</option>
                        <option value="info">INFO</option>
                        <option value="warning">WARNING</option>
                        <option value="error">ERROR</option>
                        <option value="debug">DEBUG</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Type</label>
                    <select x-model="filters.type" class="w-full border-gray-300 rounded-lg">
                        <option value="">Tous</option>
                        <option value="auth">Authentification</option>
                        <option value="course">Cours</option>
                        <option value="quiz">Quiz</option>
                        <option value="system">Système</option>
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs font-medium text-gray-500 mb-1">Recherche</label>
                    <div class="relative">
                        <input type="text" x-model="filters.search" placeholder="Rechercher dans les logs..."
                               class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg">
                        <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Liste des logs -->
        <div class="bg-gray-900 rounded-xl shadow-lg overflow-hidden">
            <div class="px-4 py-2 bg-gray-800 border-b border-gray-700 flex items-center">
                <span class="text-xs text-gray-400 font-mono">Timestamp</span>
                <span class="ml-4 text-xs text-gray-400 font-mono">Niveau</span>
                <span class="ml-20 text-xs text-gray-400 font-mono">Message</span>
            </div>
            
            <div class="divide-y divide-gray-800 max-h-[600px] overflow-y-auto font-mono text-sm">
                @php
                    $logs = [
                        ['timestamp' => '2024-01-15 10:23:45', 'level' => 'INFO', 'message' => 'Utilisateur connecté: sophie.martin@email.com', 'type' => 'auth'],
                        ['timestamp' => '2024-01-15 10:25:12', 'level' => 'INFO', 'message' => 'Cours créé: "Introduction à Laravel" par Thomas Dubois', 'type' => 'course'],
                        ['timestamp' => '2024-01-15 10:30:33', 'level' => 'WARNING', 'message' => 'Tentative de connexion échouée pour admin@test.com', 'type' => 'auth'],
                        ['timestamp' => '2024-01-15 10:35:18', 'level' => 'ERROR', 'message' => 'Erreur lors du téléchargement de la vidéo: fichier trop volumineux', 'type' => 'course'],
                        ['timestamp' => '2024-01-15 10:40:05', 'level' => 'INFO', 'message' => 'Quiz complété avec succès: "JavaScript Avancé" - Score: 85%', 'type' => 'quiz'],
                        ['timestamp' => '2024-01-15 10:42:30', 'level' => 'DEBUG', 'message' => 'Cache nettoyé avec succès', 'type' => 'system'],
                        ['timestamp' => '2024-01-15 10:45:00', 'level' => 'INFO', 'message' => 'Nouvelle inscription: marie.lambert@email.com', 'type' => 'auth'],
                        ['timestamp' => '2024-01-15 10:50:22', 'level' => 'WARNING', 'message' => 'Mémoire serveur élevée: 82% utilisée', 'type' => 'system'],
                        ['timestamp' => '2024-01-15 10:55:47', 'level' => 'INFO', 'message' => 'Certificat généré pour le cours "UI/UX Design"', 'type' => 'course'],
                        ['timestamp' => '2024-01-15 11:00:13', 'level' => 'ERROR', 'message' => 'Échec de l\'envoi d\'email: SMTP connection timeout', 'type' => 'system'],
                    ];
                @endphp
                
                <template x-for="log in filteredLogs" :key="log.timestamp">
                    <div class="px-4 py-2 hover:bg-gray-800 transition-colors">
                        <div class="flex items-start">
                            <span class="text-gray-500 w-36 flex-shrink-0" x-text="log.timestamp"></span>
                            <span class="w-20 flex-shrink-0" :class="{
                                'text-blue-400': log.level === 'INFO',
                                'text-yellow-400': log.level === 'WARNING',
                                'text-red-400': log.level === 'ERROR',
                                'text-gray-400': log.level === 'DEBUG'
                            }" x-text="log.level"></span>
                            <span class="text-gray-300 break-all" x-text="log.message"></span>
                        </div>
                    </div>
                </template>
            </div>
        </div>
        
        <!-- Pagination -->
        <div class="mt-4 flex items-center justify-between">
            <p class="text-sm text-gray-500">
                Affichage de <span x-text="filteredLogs.length"></span> logs
            </p>
            <div class="flex space-x-2">
                <button class="px-3 py-1 border border-gray-300 rounded-lg text-sm hover:bg-gray-50 disabled:opacity-50">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <button class="px-3 py-1 bg-indigo-600 text-white rounded-lg text-sm">1</button>
                <button class="px-3 py-1 border border-gray-300 rounded-lg text-sm hover:bg-gray-50">2</button>
                <button class="px-3 py-1 border border-gray-300 rounded-lg text-sm hover:bg-gray-50">3</button>
                <button class="px-3 py-1 border border-gray-300 rounded-lg text-sm hover:bg-gray-50">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function logsManager() {
        return {
            filters: {
                level: '',
                type: '',
                search: ''
            },
            logs: @json($logs ?? []),
            
            get filteredLogs() {
                return this.logs.filter(log => {
                    if (this.filters.level && log.level !== this.filters.level) return false;
                    if (this.filters.type && log.type !== this.filters.type) return false;
                    if (this.filters.search && !log.message.toLowerCase().includes(this.filters.search.toLowerCase())) return false;
                    return true;
                });
            },
            
            refreshLogs() {
                alert('Logs actualisés');
            },
            
            clearLogs() {
                if (confirm('Êtes-vous sûr de vouloir effacer tous les logs ?')) {
                    this.logs = [];
                    alert('Logs effacés');
                }
            }
        }
    }
</script>
@endpush