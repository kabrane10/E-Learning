@extends('layouts.public')

@section('title', 'Classement des Apprenants')

@push('styles')
<style>
    .leaderboard-table th {
        background-color: #f9fafb;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }
    
    .leaderboard-row {
        transition: all 0.2s ease;
    }
    
    .leaderboard-row:hover {
        background-color: #f8fafc;
    }
    
    .leaderboard-row.current-user {
        background: linear-gradient(90deg, #e0e7ff 0%, #e0e7ff 5px, #ffffff 5px, #ffffff 100%);
        border-left: 4px solid #4f46e5;
    }
    
    .rank-badge {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
        font-weight: 700;
    }
    
    .rank-1 {
        background: linear-gradient(135deg, #fbbf24, #f59e0b);
        color: white;
        box-shadow: 0 4px 6px -1px rgba(251, 191, 36, 0.3);
    }
    
    .rank-2 {
        background: linear-gradient(135deg, #cbd5e1, #94a3b8);
        color: white;
        box-shadow: 0 4px 6px -1px rgba(148, 163, 184, 0.3);
    }
    
    .rank-3 {
        background: linear-gradient(135deg, #d97706, #b45309);
        color: white;
        box-shadow: 0 4px 6px -1px rgba(217, 119, 6, 0.3);
    }
    
    .rank-other {
        background: #f1f5f9;
        color: #475569;
    }
    
    .filter-tab {
        transition: all 0.2s ease;
    }
    
    .filter-tab.active {
        background-color: #4f46e5;
        color: white;
    }
    
    .filter-tab:not(.active):hover {
        background-color: #e2e8f0;
    }
</style>
@endpush

@section('content')
<div class="bg-gradient-to-br from-slate-50 via-indigo-50/30 to-purple-50/30 min-h-screen py-8 lg:py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- En-tête -->
        <div class="mb-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <div class="flex items-center gap-3 mb-2">
                        <a href="{{ route('gamification.index') }}" class="text-gray-500 hover:text-indigo-600 transition-colors">
                            <i class="fas fa-arrow-left mr-2"></i>Retour
                        </a>
                    </div>
                    <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-3">
                        <span class="w-12 h-12 bg-gradient-to-r from-emerald-500 to-teal-500 rounded-xl flex items-center justify-center shadow-md">
                            <i class="fas fa-trophy text-white text-xl"></i>
                        </span>
                        Classement des Apprenants
                    </h1>
                    <p class="text-gray-600 mt-2 ml-16">Découvrez les apprenants les plus actifs et performants</p>
                </div>
                
                <!-- Filtres -->
                <div class="flex items-center gap-2 bg-white p-1.5 rounded-xl shadow-sm border border-gray-200">
                    <a href="{{ route('gamification.leaderboard', ['type' => 'points']) }}" 
                       class="filter-tab px-4 py-2 rounded-lg text-sm font-medium {{ $type === 'points' ? 'active' : 'text-gray-600' }}">
                        <i class="fas fa-star mr-1.5"></i>Points
                    </a>
                    <a href="{{ route('gamification.leaderboard', ['type' => 'level']) }}" 
                       class="filter-tab px-4 py-2 rounded-lg text-sm font-medium {{ $type === 'level' ? 'active' : 'text-gray-600' }}">
                        <i class="fas fa-chart-line mr-1.5"></i>Niveau
                    </a>
                    <a href="{{ route('gamification.leaderboard', ['type' => 'badges']) }}" 
                       class="filter-tab px-4 py-2 rounded-lg text-sm font-medium {{ $type === 'badges' ? 'active' : 'text-gray-600' }}">
                        <i class="fas fa-medal mr-1.5"></i>Badges
                    </a>
                    <a href="{{ route('gamification.leaderboard', ['type' => 'streak']) }}" 
                       class="filter-tab px-4 py-2 rounded-lg text-sm font-medium {{ $type === 'streak' ? 'active' : 'text-gray-600' }}">
                        <i class="fas fa-fire mr-1.5"></i>Série
                    </a>
                </div>
            </div>
        </div>

        <!-- Votre position -->
        @php
            $currentUserPosition = null;
            foreach($leaderboard as $index => $entry) {
                if (($entry['id'] ?? '') === auth()->id()) {
                    $currentUserPosition = $index + 1;
                    break;
                }
            }
        @endphp
        
        @if($currentUserPosition)
            <div class="bg-white rounded-2xl shadow-md border border-indigo-200 p-5 mb-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-indigo-100 rounded-xl flex items-center justify-center">
                            <i class="fas fa-user text-indigo-600 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Votre position actuelle</p>
                            <p class="text-2xl font-bold text-indigo-600">#{{ $currentUserPosition }}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-sm text-gray-500">sur {{ count($leaderboard) }} apprenants</p>
                        @if($currentUserPosition <= 10)
                            <span class="inline-flex items-center px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-medium">
                                <i class="fas fa-check-circle mr-1"></i>Top 10
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        <!-- Tableau de classement -->
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="leaderboard-table min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider w-20">Rang</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Apprenant</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Niveau</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                @if($type === 'points')
                                    <i class="fas fa-star text-amber-500 mr-1"></i>Points
                                @elseif($type === 'level')
                                    <i class="fas fa-chart-line text-indigo-500 mr-1"></i>Niveau
                                @elseif($type === 'badges')
                                    <i class="fas fa-medal text-amber-500 mr-1"></i>Badges
                                @else
                                    <i class="fas fa-fire text-orange-500 mr-1"></i>Série
                                @endif
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Badges</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Série</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Cours</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($leaderboard as $index => $entry)
                            @php
                                $isCurrentUser = ($entry['id'] ?? '') === auth()->id();
                            @endphp
                            <tr class="leaderboard-row {{ $isCurrentUser ? 'current-user' : '' }}">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($index === 0)
                                        <div class="rank-badge rank-1">
                                            <i class="fas fa-crown text-white"></i>
                                        </div>
                                    @elseif($index === 1)
                                        <div class="rank-badge rank-2">
                                            <span class="text-white font-bold">2</span>
                                        </div>
                                    @elseif($index === 2)
                                        <div class="rank-badge rank-3">
                                            <span class="text-white font-bold">3</span>
                                        </div>
                                    @else
                                        <div class="rank-badge rank-other">
                                            <span class="font-semibold">#{{ $index + 1 }}</span>
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <img src="{{ $entry['avatar'] ?? 'https://ui-avatars.com/api/?name=' . urlencode($entry['name'] ?? 'User') . '&background=6366f1&color=fff&size=40' }}" 
                                             class="w-10 h-10 rounded-full border-2 border-white shadow-sm object-cover">
                                        <div>
                                            <span class="font-medium text-gray-900">{{ $entry['name'] ?? 'Inconnu' }}</span>
                                            @if($isCurrentUser)
                                                <span class="ml-2 text-xs font-medium text-indigo-600 bg-indigo-100 px-2 py-0.5 rounded-full">Vous</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $level = \App\Models\Level::find($entry['current_level'] ?? 1);
                                    @endphp
                                    @php
                                        $levelIcons = [
                                            1 => 'fa-seedling',
                                            2 => 'fa-leaf',
                                            3 => 'fa-tree',
                                            4 => 'fa-bolt',
                                            5 => 'fa-fire',
                                            6 => 'fa-crown',
                                            7 => 'fa-star',
                                            8 => 'fa-gem',
                                            9 => 'fa-dragon',
                                            10 => 'fa-crown',
                                        ];
                                        $icon = $levelIcons[$entry['current_level'] ?? 1] ?? 'fa-chart-line';
                                    @endphp

                                    <div class="flex items-center gap-2">
                                        <div class="w-8 h-8 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-lg flex items-center justify-center shadow-sm">
                                            <i class="fas {{ $icon }} text-white text-sm"></i>
                                        </div>
                                        <div>
                                            <span class="px-2.5 py-1 text-xs font-medium rounded-full bg-indigo-100 text-indigo-700">
                                                Niv. {{ $entry['current_level'] ?? 1 }}
                                            </span>
                                            <p class="text-xs text-gray-500 mt-0.5">{{ $level->name ?? 'Débutant' }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($type === 'points')
                                        <span class="text-xl font-bold text-gray-900">{{ number_format($entry['total_points'] ?? 0) }}</span>
                                        <span class="text-xs text-gray-500 ml-1">XP</span>
                                    @elseif($type === 'level')
                                        <span class="text-xl font-bold text-gray-900">Niv. {{ $entry['current_level'] ?? 1 }}</span>
                                    @elseif($type === 'badges')
                                        <span class="text-xl font-bold text-gray-900">{{ $entry['badges_count'] ?? 0 }}</span>
                                    @else
                                        <span class="text-xl font-bold text-orange-600">{{ $entry['streak_days'] ?? 0 }}</span>
                                        <span class="text-xs text-gray-500 ml-1">jours</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-1.5">
                                        <i class="fas fa-medal text-amber-500"></i>
                                        <span class="font-medium">{{ $entry['badges_count'] ?? 0 }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if(($entry['streak_days'] ?? 0) > 0)
                                        <div class="flex items-center gap-1.5 text-orange-600">
                                            <i class="fas fa-fire"></i>
                                            <span class="font-medium">{{ $entry['streak_days'] }} jours</span>
                                        </div>
                                    @else
                                        <span class="text-gray-400">—</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-gray-600">{{ $entry['courses_completed'] ?? 0 }}</span>
                                    <span class="text-xs text-gray-400 ml-1">terminés</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-16 text-center text-gray-500">
                                    <div class="w-20 h-20 mx-auto mb-4 bg-gray-100 rounded-2xl flex items-center justify-center">
                                        <i class="fas fa-users text-gray-400 text-3xl"></i>
                                    </div>
                                    <p class="text-lg font-medium text-gray-700">Aucune donnée de classement</p>
                                    <p class="text-gray-500 mt-1">Soyez le premier à apparaître dans le classement !</p>
                                    <a href="{{ route('courses.index') }}" class="mt-4 inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                                        <i class="fas fa-book-open mr-2"></i>Explorer les cours
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Pied du tableau -->
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                <p class="text-sm text-gray-500">
                    <i class="fas fa-info-circle mr-1 text-indigo-500"></i>
                    Le classement est mis à jour quotidiennement. Continuez à apprendre pour grimper dans le classement !
                </p>
            </div>
        </div>

        <!-- Légende des récompenses -->
        <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-gradient-to-br from-yellow-50 to-amber-50 rounded-xl p-5 border border-yellow-200">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-yellow-400 rounded-xl flex items-center justify-center">
                        <i class="fas fa-crown text-white"></i>
                    </div>
                    <div>
                        <h4 class="font-semibold text-gray-900">Top 1</h4>
                        <p class="text-sm text-gray-600">Badge exclusif + 500 points bonus</p>
                    </div>
                </div>
            </div>
            <div class="bg-gradient-to-br from-gray-50 to-slate-100 rounded-xl p-5 border border-gray-200">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-gray-400 rounded-xl flex items-center justify-center">
                        <i class="fas fa-medal text-white"></i>
                    </div>
                    <div>
                        <h4 class="font-semibold text-gray-900">Top 3</h4>
                        <p class="text-sm text-gray-600">Badge podium + 250 points bonus</p>
                    </div>
                </div>
            </div>
            <div class="bg-gradient-to-br from-indigo-50 to-purple-50 rounded-xl p-5 border border-indigo-200">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-indigo-500 rounded-xl flex items-center justify-center">
                        <i class="fas fa-chart-line text-white"></i>
                    </div>
                    <div>
                        <h4 class="font-semibold text-gray-900">Top 10</h4>
                        <p class="text-sm text-gray-600">Badge élite + 100 points bonus</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection