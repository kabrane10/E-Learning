@extends('layouts.admin')

@section('title', 'Logs d\'activité')

@section('breadcrumb')
<nav class="flex" aria-label="Breadcrumb">
    <ol class="flex items-center space-x-2">
        <li><a href="{{ route('admin.dashboard') }}" class="text-gray-400 hover:text-gray-500"><i class="fas fa-home"></i></a></li>
        <li><i class="fas fa-chevron-right text-gray-300 text-xs"></i></li>
        <li><a href="{{ route('admin.logs') }}" class="text-gray-400 hover:text-gray-500">Logs</a></li>
        <li><i class="fas fa-chevron-right text-gray-300 text-xs"></i></li>
        <li class="text-sm font-medium text-gray-700">Activités</li>
    </ol>
</nav>
@endsection

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Logs d'activité</h1>
                <p class="text-gray-500 mt-1">Historique des actions des utilisateurs</p>
            </div>
            <div class="mt-4 sm:mt-0">
                <a href="{{ route('admin.logs') }}" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                    <i class="fas fa-arrow-left mr-2"></i>Retour aux logs
                </a>
            </div>
        </div>
        
        <!-- Statistiques -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
                <p class="text-xs text-gray-500 uppercase">Total activités</p>
                <p class="text-2xl font-bold text-gray-900">{{ $stats['total_activities'] ?? 0 }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
                <p class="text-xs text-gray-500 uppercase">Aujourd'hui</p>
                <p class="text-2xl font-bold text-green-600">{{ $stats['today_activities'] ?? 0 }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
                <p class="text-xs text-gray-500 uppercase">Utilisateurs actifs</p>
                <p class="text-2xl font-bold text-indigo-600">{{ $stats['unique_users'] ?? 0 }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
                <p class="text-xs text-gray-500 uppercase">Actions distinctes</p>
                <p class="text-2xl font-bold text-purple-600">{{ count($stats['actions'] ?? []) }}</p>
            </div>
        </div>
        
        <!-- Filtres -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-3">
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Action</label>
                    <select name="action" class="w-full text-sm border-gray-300 rounded-lg">
                        <option value="">Toutes</option>
                        @foreach($stats['actions'] ?? [] as $action)
                            <option value="{{ $action }}" {{ request('action') == $action ? 'selected' : '' }}>
                                {{ ucfirst($action) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Utilisateur</label>
                    <select name="user_id" class="w-full text-sm border-gray-300 rounded-lg">
                        <option value="">Tous</option>
                        {{-- Ajouter les utilisateurs ici --}}
                    </select>
                </div>
                
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Date début</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}"
                           class="w-full text-sm border-gray-300 rounded-lg">
                </div>
                
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Date fin</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}"
                           class="w-full text-sm border-gray-300 rounded-lg">
                </div>
                
                <div class="flex items-end">
                    <button type="submit" class="w-full px-4 py-2 bg-indigo-600 text-white text-sm rounded-lg hover:bg-indigo-700">
                        <i class="fas fa-filter mr-1"></i>Filtrer
                    </button>
                </div>
            </form>
        </div>
        
        <!-- Tableau des activités -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Utilisateur</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Action</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Modèle</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Détails</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">IP</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($activities ?? [] as $activity)
                            <tr class="table-row-hover">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <img src="{{ $activity->user->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($activity->user->name ?? 'System') }}" 
                                             class="w-8 h-8 rounded-full mr-3">
                                        <span class="text-sm text-gray-900">{{ $activity->user->name ?? 'Système' }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs font-medium rounded-full 
                                               {{ $activity->action === 'created' ? 'bg-green-100 text-green-700' : 
                                                  ($activity->action === 'updated' ? 'bg-blue-100 text-blue-700' : 
                                                  ($activity->action === 'deleted' ? 'bg-red-100 text-red-700' : 'bg-gray-100 text-gray-700')) }}">
                                        {{ ucfirst($activity->action) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ class_basename($activity->model_type) }} #{{ $activity->model_id }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500 max-w-xs truncate">
                                    {{ $activity->details }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $activity->ip_address }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $activity->created_at->diffForHumans() }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                    <i class="fas fa-history text-4xl mb-3 opacity-30"></i>
                                    <p>Aucune activité enregistrée</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if(isset($activities) && $activities->hasPages())
                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                    {{ $activities->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection