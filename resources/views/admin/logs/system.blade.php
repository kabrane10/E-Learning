@extends('layouts.admin')

@section('title', 'Informations système')

@section('breadcrumb')
<nav class="flex" aria-label="Breadcrumb">
    <ol class="flex items-center space-x-2">
        <li><a href="{{ route('admin.dashboard') }}" class="text-gray-400 hover:text-gray-500"><i class="fas fa-home"></i></a></li>
        <li><i class="fas fa-chevron-right text-gray-300 text-xs"></i></li>
        <li><a href="{{ route('admin.logs') }}" class="text-gray-400 hover:text-gray-500">Logs</a></li>
        <li><i class="fas fa-chevron-right text-gray-300 text-xs"></i></li>
        <li class="text-sm font-medium text-gray-700">Système</li>
    </ol>
</nav>
@endsection

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Informations système</h1>
                <p class="text-gray-500 mt-1">État et configuration du serveur</p>
            </div>
            <a href="{{ route('admin.logs') }}" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                <i class="fas fa-arrow-left mr-2"></i>Retour aux logs
            </a>
        </div>
        
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- PHP Info -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                    <h3 class="font-semibold text-gray-900"><i class="fab fa-php text-indigo-600 mr-2"></i>PHP</h3>
                </div>
                <div class="p-6">
                    <dl class="grid grid-cols-1 gap-4">
                        <div class="flex justify-between py-2 border-b border-gray-100">
                            <dt class="text-sm text-gray-500">Version PHP</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $systemInfo['php_version'] ?? 'N/A' }}</dd>
                        </div>
                        <div class="flex justify-between py-2 border-b border-gray-100">
                            <dt class="text-sm text-gray-500">Memory Limit</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $systemInfo['memory_limit'] ?? 'N/A' }}</dd>
                        </div>
                        <div class="flex justify-between py-2 border-b border-gray-100">
                            <dt class="text-sm text-gray-500">Max Execution Time</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $systemInfo['max_execution_time'] ?? 'N/A' }}s</dd>
                        </div>
                        <div class="flex justify-between py-2 border-b border-gray-100">
                            <dt class="text-sm text-gray-500">Upload Max Filesize</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $systemInfo['upload_max_filesize'] ?? 'N/A' }}</dd>
                        </div>
                        <div class="flex justify-between py-2 border-b border-gray-100">
                            <dt class="text-sm text-gray-500">Post Max Size</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $systemInfo['post_max_size'] ?? 'N/A' }}</dd>
                        </div>
                    </dl>
                </div>
            </div>
            
            <!-- Laravel Info -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                    <h3 class="font-semibold text-gray-900"><i class="fab fa-laravel text-red-600 mr-2"></i>Laravel</h3>
                </div>
                <div class="p-6">
                    <dl class="grid grid-cols-1 gap-4">
                        <div class="flex justify-between py-2 border-b border-gray-100">
                            <dt class="text-sm text-gray-500">Version Laravel</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $systemInfo['laravel_version'] ?? 'N/A' }}</dd>
                        </div>
                        <div class="flex justify-between py-2 border-b border-gray-100">
                            <dt class="text-sm text-gray-500">Environnement</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ app()->environment() }}</dd>
                        </div>
                        <div class="flex justify-between py-2 border-b border-gray-100">
                            <dt class="text-sm text-gray-500">Mode debug</dt>
                            <dd class="text-sm font-medium {{ config('app.debug') ? 'text-red-600' : 'text-green-600' }}">
                                {{ config('app.debug') ? 'Activé' : 'Désactivé' }}
                            </dd>
                        </div>
                        <div class="flex justify-between py-2 border-b border-gray-100">
                            <dt class="text-sm text-gray-500">Mode maintenance</dt>
                            <dd class="text-sm font-medium {{ app()->isDownForMaintenance() ? 'text-yellow-600' : 'text-green-600' }}">
                                {{ app()->isDownForMaintenance() ? 'Activé' : 'Désactivé' }}
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>
            
            <!-- Base de données -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                    <h3 class="font-semibold text-gray-900"><i class="fas fa-database text-blue-600 mr-2"></i>Base de données</h3>
                </div>
                <div class="p-6">
                    <dl class="grid grid-cols-1 gap-4">
                        <div class="flex justify-between py-2 border-b border-gray-100">
                            <dt class="text-sm text-gray-500">Driver</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $systemInfo['database'] ?? 'N/A' }}</dd>
                        </div>
                        <div class="flex justify-between py-2 border-b border-gray-100">
                            <dt class="text-sm text-gray-500">Version</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $systemInfo['database_version'] ?? 'N/A' }}</dd>
                        </div>
                    </dl>
                </div>
            </div>
            
            <!-- Serveur -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                    <h3 class="font-semibold text-gray-900"><i class="fas fa-server text-purple-600 mr-2"></i>Serveur</h3>
                </div>
                <div class="p-6">
                    <dl class="grid grid-cols-1 gap-4">
                        <div class="flex justify-between py-2 border-b border-gray-100">
                            <dt class="text-sm text-gray-500">Software</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $systemInfo['server_software'] ?? 'N/A' }}</dd>
                        </div>
                        <div class="flex justify-between py-2 border-b border-gray-100">
                            <dt class="text-sm text-gray-500">Espace disque libre</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $systemInfo['disk_free_space'] ?? 'N/A' }}</dd>
                        </div>
                        <div class="flex justify-between py-2 border-b border-gray-100">
                            <dt class="text-sm text-gray-500">Espace disque total</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $systemInfo['disk_total_space'] ?? 'N/A' }}</dd>
                        </div>
                    </dl>
                    
                    <!-- Barre de progression espace disque -->
                    @if(isset($systemInfo['disk_free_space']) && isset($systemInfo['disk_total_space']))
                        @php
                            $freePercent = 0;
                            if (is_numeric(disk_free_space(storage_path())) && is_numeric(disk_total_space(storage_path()))) {
                                $freePercent = (disk_free_space(storage_path()) / disk_total_space(storage_path())) * 100;
                            }
                        @endphp
                        <div class="mt-4">
                            <div class="flex justify-between text-xs text-gray-500 mb-1">
                                <span>Utilisation disque</span>
                                <span>{{ round(100 - $freePercent, 1) }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="h-2 rounded-full {{ $freePercent < 10 ? 'bg-red-600' : ($freePercent < 20 ? 'bg-yellow-600' : 'bg-green-600') }}"
                                     style="width: {{ 100 - $freePercent }}%"></div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection