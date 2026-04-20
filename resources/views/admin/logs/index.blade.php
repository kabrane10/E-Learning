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
        <!-- Header -->
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Logs système</h1>
                <p class="text-gray-500 mt-1">Surveillez l'activité et les erreurs de votre plateforme</p>
            </div>
            <div class="mt-4 sm:mt-0 flex space-x-2">
                <a href="{{ route('admin.logs.activity') }}" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                    <i class="fas fa-history mr-2"></i>Activités
                </a>
                <a href="{{ route('admin.logs.system') }}" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                    <i class="fas fa-server mr-2"></i>Système
                </a>
                <button @click="refreshLogs()" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors shadow-sm">
                    <i class="fas fa-sync-alt mr-2"></i>Actualiser
                </button>
            </div>
        </div>
        
        <!-- Statistiques -->
        <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
                <p class="text-xs text-gray-500 uppercase">Total logs</p>
                <p class="text-2xl font-bold text-gray-900">{{ $stats['total'] ?? 0 }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
                <p class="text-xs text-gray-500 uppercase">Erreurs</p>
                <p class="text-2xl font-bold text-red-600">{{ $stats['error'] ?? 0 }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
                <p class="text-xs text-gray-500 uppercase">Warnings</p>
                <p class="text-2xl font-bold text-yellow-600">{{ $stats['warning'] ?? 0 }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
                <p class="text-xs text-gray-500 uppercase">Info</p>
                <p class="text-2xl font-bold text-blue-600">{{ $stats['info'] ?? 0 }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
                <p class="text-xs text-gray-500 uppercase">Debug</p>
                <p class="text-2xl font-bold text-gray-600">{{ $stats['debug'] ?? 0 }}</p>
            </div>
        </div>
        
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
            <!-- Sidebar - Fichiers de logs -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-4 py-3 bg-gray-50 border-b border-gray-200">
                        <h3 class="font-medium text-gray-900">Fichiers de logs</h3>
                    </div>
                    <div class="divide-y divide-gray-200 max-h-96 overflow-y-auto">
                        @forelse($logFiles ?? [] as $file)
                            <a href="{{ route('admin.logs', ['file' => $file['name']]) }}" 
                               class="block px-4 py-3 hover:bg-gray-50 transition-colors {{ ($logFile ?? '') === $file['name'] ? 'bg-indigo-50 border-l-4 border-indigo-600' : '' }}">
                                <div class="flex items-center justify-between">
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 truncate">{{ $file['name'] }}</p>
                                        <p class="text-xs text-gray-500">{{ $file['size'] }} • {{ $file['modified'] }}</p>
                                    </div>
                                    <div class="ml-2 flex items-center space-x-1">
                                        <a href="{{ route('admin.logs.download', ['file' => $file['name']]) }}" 
                                           class="p-1 text-gray-400 hover:text-indigo-600"
                                           title="Télécharger">
                                            <i class="fas fa-download text-xs"></i>
                                        </a>
                                        <form action="{{ route('admin.logs.destroy', $file['name']) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    onclick="return confirm('Supprimer ce fichier de log ?')"
                                                    class="p-1 text-gray-400 hover:text-red-600"
                                                    title="Supprimer">
                                                <i class="fas fa-trash text-xs"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </a>
                        @empty
                            <div class="px-4 py-8 text-center text-gray-500">
                                <i class="fas fa-file-alt text-2xl mb-2 opacity-50"></i>
                                <p class="text-sm">Aucun fichier de log</p>
                            </div>
                        @endforelse
                    </div>
                    <div class="px-4 py-3 bg-gray-50 border-t border-gray-200">
                        <form action="{{ route('admin.logs.clear') }}" method="POST">
                            @csrf
                            <input type="hidden" name="file" value="{{ $logFile ?? 'laravel.log' }}">
                            <button type="submit" 
                                    onclick="return confirm('Effacer le contenu de ce fichier de log ?')"
                                    class="w-full px-3 py-1.5 bg-red-100 text-red-700 text-sm rounded-lg hover:bg-red-200 transition-colors">
                                <i class="fas fa-eraser mr-1"></i>Effacer ce fichier
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- Contenu principal - Logs -->
            <div class="lg:col-span-3">
                <!-- Filtres -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-4">
                    <form action="{{ route('admin.logs.search') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-3">
                        <input type="hidden" name="file" value="{{ $logFile ?? 'laravel.log' }}">
                        
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Niveau</label>
                            <select name="level" class="w-full text-sm border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">Tous</option>
                                <option value="error" {{ request('level') == 'error' ? 'selected' : '' }}>ERROR</option>
                                <option value="warning" {{ request('level') == 'warning' ? 'selected' : '' }}>WARNING</option>
                                <option value="info" {{ request('level') == 'info' ? 'selected' : '' }}>INFO</option>
                                <option value="debug" {{ request('level') == 'debug' ? 'selected' : '' }}>DEBUG</option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Date</label>
                            <input type="date" name="date" value="{{ request('date') }}"
                                   class="w-full text-sm border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Recherche</label>
                            <input type="text" name="q" value="{{ request('q') }}" placeholder="Rechercher..."
                                   class="w-full text-sm border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        
                        <div class="flex items-end space-x-2">
                            <button type="submit" class="flex-1 px-4 py-2 bg-indigo-600 text-white text-sm rounded-lg hover:bg-indigo-700 transition-colors">
                                <i class="fas fa-filter mr-1"></i>Filtrer
                            </button>
                            <a href="{{ route('admin.logs', ['file' => $logFile ?? '']) }}" class="px-4 py-2 text-gray-600 text-sm border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                                <i class="fas fa-times"></i>
                            </a>
                        </div>
                    </form>
                </div>
                
                <!-- Liste des logs -->
                <div class="bg-gray-900 rounded-xl shadow-lg overflow-hidden">
                    <div class="px-4 py-2 bg-gray-800 border-b border-gray-700 flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            <span class="text-xs text-gray-400 font-mono w-36">Timestamp</span>
                            <span class="text-xs text-gray-400 font-mono w-20">Niveau</span>
                            <span class="text-xs text-gray-400 font-mono">Message</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <a href="{{ route('admin.logs.export', request()->all()) }}" class="text-xs text-gray-400 hover:text-white transition-colors">
                                <i class="fas fa-download mr-1"></i>Exporter
                            </a>
                        </div>
                    </div>
                    
                    <div class="divide-y divide-gray-800 max-h-[600px] overflow-y-auto font-mono text-sm">
                        @forelse($filteredLogs ?? [] as $index => $log)
                            <div class="px-4 py-3 hover:bg-gray-800 transition-colors animate-slide-in" 
                                 style="animation-delay: {{ $index * 20 }}ms"
                                 x-data="{ expanded: false }">
                                <div class="flex items-start cursor-pointer" @click="expanded = !expanded">
                                    <span class="text-gray-500 w-36 flex-shrink-0 text-xs">{{ $log['timestamp'] }}</span>
                                    <span class="w-20 flex-shrink-0 text-xs font-medium
                                               {{ $log['level'] === 'ERROR' ? 'text-red-400' : 
                                                  ($log['level'] === 'WARNING' ? 'text-yellow-400' : 
                                                  ($log['level'] === 'INFO' ? 'text-blue-400' : 'text-gray-400')) }}">
                                        {{ $log['level'] }}
                                    </span>
                                    <span class="text-gray-300 break-all flex-1 text-xs" x-show="!expanded">
                                        {{ \Illuminate\Support\Str::limit($log['message'], 100) }}
                                    </span>
                                    <span class="text-gray-300 break-all flex-1 text-xs" x-show="expanded" style="display: none;">
                                        {{ $log['message'] }}
                                    </span>
                                    <button class="ml-2 text-gray-500 hover:text-gray-300 flex-shrink-0">
                                        <i class="fas text-xs" :class="expanded ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                                    </button>
                                </div>
                                
                                <!-- Détails supplémentaires -->
                                <div x-show="expanded" style="display: none;" class="mt-2 ml-56">
                                    @if(!empty($log['context']))
                                        <div class="bg-gray-800 rounded-lg p-3">
                                            <p class="text-xs text-gray-400 mb-1">Contexte :</p>
                                            <pre class="text-xs text-gray-300 overflow-x-auto">{{ $log['context'] }}</pre>
                                        </div>
                                    @endif
                                    <div class="mt-2 text-xs text-gray-500">
                                        <span>Environnement : {{ $log['environment'] ?? 'production' }}</span>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="px-4 py-16 text-center">
                                <i class="fas fa-check-circle text-4xl text-green-500 mb-3"></i>
                                <p class="text-gray-400">Aucun log trouvé</p>
                                <p class="text-gray-500 text-xs mt-1">Tout fonctionne parfaitement !</p>
                            </div>
                        @endforelse
                    </div>
                    
                    <!-- Footer -->
                    <div class="px-4 py-2 bg-gray-800 border-t border-gray-700 flex items-center justify-between">
                        <p class="text-xs text-gray-400">
                            Affichage de {{ count($filteredLogs ?? []) }} logs
                        </p>
                        <p class="text-xs text-gray-500">
                            Fichier : {{ $logFile ?? 'laravel.log' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function logsManager() {
        return {
            refreshLogs() {
                window.location.reload();
            }
        }
    }
</script>
@endpush