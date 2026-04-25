@extends('layouts.public')

@section('title', 'Progression & Récompenses')

@push('styles')
<style>
    .stat-card {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .stat-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 25px 30px -12px rgba(0, 0, 0, 0.15);
    }
    
    .badge-card {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .badge-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 20px -8px rgba(0, 0, 0, 0.12);
    }
    .badge-card.earned {
        position: relative;
        overflow: hidden;
    }
    .badge-card.earned::after {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 0;
        height: 0;
        border-style: solid;
        border-width: 0 30px 30px 0;
        border-color: transparent #10b981 transparent transparent;
    }
    .badge-card.earned::before {
        content: '✓';
        position: absolute;
        top: 4px;
        right: 4px;
        color: white;
        font-size: 12px;
        font-weight: bold;
        z-index: 1;
    }
    
    .achievement-card {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .achievement-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 16px -6px rgba(0, 0, 0, 0.1);
    }
    .achievement-card.completed {
        border-left: 4px solid #10b981;
    }
    
    .progress-bar-animated {
        transition: width 0.8s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    .section-icon {
        width: 44px;
        height: 44px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 8px 16px -4px rgba(0, 0, 0, 0.15);
    }
    
    .leaderboard-row {
        transition: all 0.2s ease;
    }
    .leaderboard-row:hover {
        background-color: #f8fafc;
    }
    .leaderboard-row.current-user {
        background: linear-gradient(90deg, #eef2ff 0%, #eef2ff 5px, #ffffff 5px, #ffffff 100%);
    }
    
    .level-pill {
        background: rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.3);
    }
</style>
@endpush

@section('content')
<div class="bg-gradient-to-br from-slate-50 via-indigo-50/20 to-purple-50/20 min-h-screen py-8 lg:py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- ============================================ --}}
        {{-- HEADER : PROFIL UTILISATEUR                  --}}
        {{-- ============================================ --}}
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden mb-8 border border-gray-100">
            <div class="bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-500 px-6 lg:px-10 py-8 lg:py-10">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                    
                    {{-- Avatar + Infos --}}
                    <div class="flex items-center gap-5">
                        <div class="relative">
                            <img src="{{ auth()->user()->avatar }}" 
                                 class="w-20 h-20 lg:w-24 lg:h-24 rounded-full border-4 border-white/80 shadow-xl object-cover ring-4 ring-white/20">
                            <span class="absolute -bottom-2 -right-2 w-9 h-9 bg-gradient-to-br from-yellow-400 to-amber-500 rounded-full flex items-center justify-center shadow-lg border-2 border-white">
                                <i class="fas fa-crown text-white text-sm"></i>
                            </span>
                        </div>
                        <div class="text-white">
                            <h1 class="text-2xl lg:text-3xl font-bold tracking-tight">{{ auth()->user()->name }}</h1>
                            <div class="flex flex-wrap items-center gap-x-3 gap-y-1.5 mt-2 text-sm">
                                <span class="level-pill flex items-center gap-1.5 px-3 py-1.5 rounded-full text-sm">
                                    <i class="fas fa-chart-line"></i>
                                    <span>Niv. {{ $stats['current_level']['number'] ?? 1 }} • {{ $stats['current_level']['name'] ?? 'Débutant' }}</span>
                                </span>
                                <span class="level-pill flex items-center gap-1.5 px-3 py-1.5 rounded-full text-sm">
                                    <i class="fas fa-fire text-orange-300"></i>
                                    <span>{{ $stats['streak_days'] ?? 0 }} jours de série</span>
                                </span>
                                <span class="level-pill flex items-center gap-1.5 px-3 py-1.5 rounded-full text-sm">
                                    <i class="fas fa-ranking-star"></i>
                                    <span>Rang #{{ $stats['rank'] ?? 1 }}</span>
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    {{-- Points Totaux --}}
                    <div class="text-right text-white">
                        <p class="text-white/70 text-xs uppercase tracking-widest font-semibold mb-1">Points Totaux</p>
                        <p class="text-5xl lg:text-6xl font-black tracking-tighter">{{ number_format($stats['total_points'] ?? 0) }}</p>
                        <p class="text-white/50 text-xs mt-1">XP accumulés</p>
                    </div>
                </div>
            </div>
            
            {{-- Barre de progression --}}
            @if(isset($stats['next_level']) && $stats['next_level'])
            <div class="px-6 lg:px-10 py-5 bg-gray-50/80">
                <div class="flex items-center justify-between mb-2 text-sm">
                    <span class="font-semibold text-gray-700 flex items-center gap-2">
                        <span class="w-6 h-6 bg-indigo-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-arrow-trend-up text-indigo-600 text-xs"></i>
                        </span>
                        Prochain niveau : <span class="text-indigo-600 font-bold">{{ $stats['next_level']['name'] }}</span>
                    </span>
                    <span class="text-gray-600">
                        <span class="font-bold">{{ number_format($stats['total_points'] ?? 0) }}</span> 
                        <span class="text-gray-400">/</span> 
                        <span>{{ number_format($stats['next_level']['points_required']) }} XP</span>
                    </span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-3 shadow-inner overflow-hidden">
                    <div class="progress-bar-animated bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500 h-3 rounded-full shadow-md" 
                         style="width: {{ $stats['next_level']['progress'] ?? 0 }}%"></div>
                </div>
                @php $remaining = ($stats['next_level']['points_required'] ?? 0) - ($stats['total_points'] ?? 0); @endphp
                <p class="text-xs text-gray-500 mt-2 flex items-center gap-1.5">
                    <i class="fas fa-hourglass-half text-amber-500"></i>
                    Plus que <span class="font-bold text-indigo-600">{{ number_format(max(0, $remaining)) }} points</span> pour passer au niveau supérieur !
                </p>
            </div>
            @endif
        </div>

        {{-- ============================================ --}}
        {{-- STATISTIQUES RAPIDES                        --}}
        {{-- ============================================ --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-5 mb-10">
            {{-- Badges --}}
            <div class="stat-card bg-white rounded-2xl shadow-md border border-gray-100 p-6 text-center group">
                <div class="w-14 h-14 mx-auto mb-4 bg-gradient-to-br from-amber-400 to-orange-500 rounded-2xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform">
                    <i class="fas fa-medal text-white text-2xl"></i>
                </div>
                <div class="text-3xl font-black text-gray-900">{{ $stats['badges']['earned'] ?? 0 }}</div>
                <p class="text-sm text-gray-500 font-semibold mt-1">Badges</p>
                @php $badgePct = ($stats['badges']['total'] ?? 1) > 0 ? round((($stats['badges']['earned'] ?? 0) / ($stats['badges']['total'] ?? 1)) * 100) : 0; @endphp
                <div class="mt-2 w-full bg-gray-100 rounded-full h-1.5 overflow-hidden">
                    <div class="bg-gradient-to-r from-amber-400 to-orange-500 h-1.5 rounded-full" style="width: {{ $badgePct }}%"></div>
                </div>
                <div class="text-xs text-gray-400 mt-1.5">{{ $badgePct }}% complété</div>
            </div>
            
            {{-- Succès --}}
            <div class="stat-card bg-white rounded-2xl shadow-md border border-gray-100 p-6 text-center group">
                <div class="w-14 h-14 mx-auto mb-4 bg-gradient-to-br from-blue-400 to-cyan-500 rounded-2xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform">
                    <i class="fas fa-trophy text-white text-2xl"></i>
                </div>
                <div class="text-3xl font-black text-gray-900">{{ $stats['achievements']['completed'] ?? 0 }}</div>
                <p class="text-sm text-gray-500 font-semibold mt-1">Succès</p>
                @php $achPct = ($stats['achievements']['total'] ?? 1) > 0 ? round((($stats['achievements']['completed'] ?? 0) / ($stats['achievements']['total'] ?? 1)) * 100) : 0; @endphp
                <div class="mt-2 w-full bg-gray-100 rounded-full h-1.5 overflow-hidden">
                    <div class="bg-gradient-to-r from-blue-400 to-cyan-500 h-1.5 rounded-full" style="width: {{ $achPct }}%"></div>
                </div>
                <div class="text-xs text-gray-400 mt-1.5">{{ $achPct }}% complété</div>
            </div>
            
            {{-- Cours terminés --}}
            <div class="stat-card bg-white rounded-2xl shadow-md border border-gray-100 p-6 text-center group">
                <div class="w-14 h-14 mx-auto mb-4 bg-gradient-to-br from-emerald-400 to-green-500 rounded-2xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform">
                    <i class="fas fa-graduation-cap text-white text-2xl"></i>
                </div>
                <div class="text-3xl font-black text-gray-900">
                    {{ auth()->user()->enrolledCourses()->whereNotNull('enrollments.completed_at')->count() }}
                </div>
                <p class="text-sm text-gray-500 font-semibold mt-1">Cours terminés</p>
            </div>
            
            {{-- Quiz réussis --}}
            <div class="stat-card bg-white rounded-2xl shadow-md border border-gray-100 p-6 text-center group">
                <div class="w-14 h-14 mx-auto mb-4 bg-gradient-to-br from-violet-400 to-purple-500 rounded-2xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform">
                    <i class="fas fa-circle-check text-white text-2xl"></i>
                </div>
                <div class="text-3xl font-black text-gray-900">
                    {{ auth()->user()->quizAttempts()->where('is_passed', true)->count() }}
                </div>
                <p class="text-sm text-gray-500 font-semibold mt-1">Quiz réussis</p>
            </div>
        </div>

        {{-- ============================================ --}}
        {{-- COLLECTION DE BADGES                         --}}
        {{-- ============================================ --}}
        <div class="mb-10">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl lg:text-2xl font-bold text-gray-900 flex items-center gap-3">
                    <span class="section-icon bg-gradient-to-br from-amber-400 to-orange-500">
                        <i class="fas fa-award text-white text-lg"></i>
                    </span>
                    Collection de Badges
                </h2>
                <a href="{{ route('gamification.badges') }}" class="text-sm text-indigo-600 hover:text-indigo-700 font-semibold transition-colors">
                    Voir tout <i class="fas fa-arrow-right ml-1.5 text-xs"></i>
                </a>
            </div>
            
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                @forelse($badges->take(6) as $badge)
                    <div class="badge-card bg-white rounded-2xl shadow-sm border border-gray-200 p-5 text-center {{ $badge->is_earned ? 'earned' : 'opacity-60' }}">
                        <div class="w-16 h-16 mx-auto mb-3 rounded-2xl bg-gradient-to-br from-{{ $badge->color }}-100 to-{{ $badge->color }}-200 flex items-center justify-center shadow-sm">
                            <span class="text-3xl">{{ $badge->icon }}</span>
                        </div>
                        <h3 class="font-bold text-gray-900 text-sm mb-1">{{ $badge->name }}</h3>
                        <p class="text-xs text-gray-500 line-clamp-2 leading-relaxed">{{ $badge->description }}</p>
                        
                        @if($badge->is_earned)
                            <div class="mt-3 text-xs font-bold text-emerald-600 bg-emerald-50 py-1.5 px-3 rounded-full inline-flex items-center">
                                <i class="fas fa-check-circle mr-1"></i>Obtenu
                            </div>
                        @else
                            <div class="mt-3">
                                <div class="w-full bg-gray-100 rounded-full h-2 overflow-hidden">
                                    <div class="bg-gradient-to-r from-indigo-500 to-purple-500 h-2 rounded-full" 
                                         style="width: {{ $badge->progress['percentage'] ?? 0 }}%"></div>
                                </div>
                                <span class="text-xs text-gray-400 mt-1.5 block font-medium">
                                    {{ $badge->progress['current'] ?? 0 }}/{{ $badge->progress['target'] ?? 1 }}
                                </span>
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="col-span-full py-16 text-center bg-white rounded-2xl border border-gray-100">
                        <div class="w-20 h-20 mx-auto mb-5 bg-gray-100 rounded-2xl flex items-center justify-center">
                            <i class="fas fa-medal text-gray-300 text-3xl"></i>
                        </div>
                        <p class="text-gray-500 font-medium text-lg">Aucun badge disponible</p>
                        <p class="text-sm text-gray-400 mt-1">Commencez à apprendre pour débloquer des badges !</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- ============================================ --}}
        {{-- SUCCÈS DÉBLOQUÉS                             --}}
        {{-- ============================================ --}}
        <div class="mb-10">
            <div class="flex items-center mb-6">
                <h2 class="text-xl lg:text-2xl font-bold text-gray-900 flex items-center gap-3">
                    <span class="section-icon bg-gradient-to-br from-blue-400 to-cyan-500">
                        <i class="fas fa-star text-white text-lg"></i>
                    </span>
                    Succès Débloqués
                </h2>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @forelse($achievements->take(6) as $achievement)
                    @php
                        $tierColors = [1 => '#CD7F32', 2 => '#C0C0C0', 3 => '#FFD700', 4 => '#E5E4E2', 5 => '#B9F2FF'];
                        $tierNames = [1 => 'Bronze', 2 => 'Argent', 3 => 'Or', 4 => 'Platine', 5 => 'Diamant'];
                        $tierColor = $tierColors[$achievement->tier] ?? '#CD7F32';
                        $tierName = $tierNames[$achievement->tier] ?? 'Bronze';
                        if (method_exists($achievement, 'getTierColor')) $tierColor = $achievement->getTierColor();
                        if (method_exists($achievement, 'getTierName')) $tierName = $achievement->getTierName();
                    @endphp
                    <div class="achievement-card bg-white rounded-2xl shadow-sm border border-gray-200 p-5 flex items-start gap-4 {{ $achievement->is_completed ? 'completed' : 'opacity-60' }}">
                        <div class="flex-shrink-0">
                            <div class="w-14 h-14 rounded-2xl flex items-center justify-center shadow-md"
                                 style="background: linear-gradient(135deg, {{ $tierColor }}20, {{ $tierColor }}40); border: 2px solid {{ $tierColor }}40">
                                <span class="text-2xl">{{ $achievement->icon }}</span>
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between mb-1.5">
                                <h3 class="font-bold text-gray-900 text-sm">{{ $achievement->name }}</h3>
                                <span class="text-xs px-2.5 py-1 rounded-full text-white font-bold shadow-sm"
                                      style="background: {{ $tierColor }}">
                                    {{ $tierName }}
                                </span>
                            </div>
                            <p class="text-xs text-gray-500 mb-3 leading-relaxed">{{ $achievement->description }}</p>
                            
                            @if($achievement->is_completed)
                                @if($achievement->is_claimed)
                                    <span class="text-xs font-bold text-emerald-600 bg-emerald-50 py-1.5 px-3 rounded-full inline-flex items-center">
                                        <i class="fas fa-check-circle mr-1.5"></i>Récompense réclamée
                                    </span>
                                @else
                                    <form action="{{ route('gamification.claim', $achievement) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="text-xs font-bold text-indigo-600 bg-indigo-50 hover:bg-indigo-100 py-1.5 px-4 rounded-full transition-all inline-flex items-center shadow-sm">
                                            <i class="fas fa-gift mr-1.5"></i>Réclamer +{{ $achievement->points_reward }} pts
                                        </button>
                                    </form>
                                @endif
                            @else
                                <div class="w-full bg-gray-100 rounded-full h-2 overflow-hidden">
                                    <div class="bg-gradient-to-r from-yellow-400 to-amber-500 h-2 rounded-full" 
                                         style="width: {{ $achievement->progress['percentage'] ?? 0 }}%"></div>
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-16 text-center bg-white rounded-2xl border border-gray-100">
                        <div class="w-20 h-20 mx-auto mb-5 bg-gray-100 rounded-2xl flex items-center justify-center">
                            <i class="fas fa-trophy text-gray-300 text-3xl"></i>
                        </div>
                        <p class="text-gray-500 font-medium text-lg">Aucun succès disponible</p>
                        <p class="text-sm text-gray-400 mt-1">Revenez plus tard pour découvrir de nouveaux succès !</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- ============================================ --}}
        {{-- CLASSEMENT                                  --}}
        {{-- ============================================ --}}
        <div>
            <div class="flex items-center mb-6">
                <h2 class="text-xl lg:text-2xl font-bold text-gray-900 flex items-center gap-3">
                    <span class="section-icon bg-gradient-to-br from-emerald-400 to-teal-500">
                        <i class="fas fa-trophy text-white text-lg"></i>
                    </span>
                    Classement des Apprenants
                </h2>
            </div>
            
            <div class="bg-white rounded-2xl shadow-md border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100">
                        <thead>
                            <tr class="bg-gray-50/80">
                                <th class="px-5 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Rang</th>
                                <th class="px-5 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Apprenant</th>
                                <th class="px-5 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Niveau</th>
                                <th class="px-5 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Points</th>
                                <th class="px-5 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Badges</th>
                                <th class="px-5 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Série</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @php
                                $displayLeaderboard = is_array($leaderboard) ? array_slice($leaderboard, 0, 10) : $leaderboard->take(10);
                                $totalCount = is_array($leaderboard) ? count($leaderboard) : $leaderboard->count();
                            @endphp
                            
                            @forelse($displayLeaderboard as $index => $entry)
                                <tr class="leaderboard-row transition-colors {{ ($entry['id'] ?? '') === auth()->id() ? 'current-user font-medium' : 'hover:bg-gray-50' }}">
                                    <td class="px-5 py-4 whitespace-nowrap">
                                        @if($index === 0)
                                            <span class="w-9 h-9 bg-gradient-to-br from-yellow-400 to-amber-500 rounded-xl flex items-center justify-center shadow-md">
                                                <i class="fas fa-crown text-white text-sm"></i>
                                            </span>
                                        @elseif($index === 1)
                                            <span class="w-9 h-9 bg-gradient-to-br from-gray-300 to-gray-400 rounded-xl flex items-center justify-center shadow-md">
                                                <span class="text-white font-bold text-sm">2</span>
                                            </span>
                                        @elseif($index === 2)
                                            <span class="w-9 h-9 bg-gradient-to-br from-amber-600 to-orange-700 rounded-xl flex items-center justify-center shadow-md">
                                                <span class="text-white font-bold text-sm">3</span>
                                            </span>
                                        @else
                                            <span class="w-9 h-9 bg-gray-100 rounded-xl flex items-center justify-center text-gray-500 font-bold text-sm">
                                                #{{ $index + 1 }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-3">
                                            <img src="{{ $entry['avatar'] ?? 'https://ui-avatars.com/api/?name=' . urlencode($entry['name'] ?? 'User') . '&background=6366f1&color=fff' }}" 
                                                 class="w-9 h-9 rounded-full border-2 border-white shadow-sm ring-2 ring-gray-100">
                                            <div>
                                                <span class="font-semibold text-gray-900">{{ $entry['name'] ?? 'Inconnu' }}</span>
                                                @if(($entry['id'] ?? '') === auth()->id())
                                                    <span class="ml-2 text-xs font-bold text-indigo-600 bg-indigo-100 px-2 py-0.5 rounded-full">Vous</span>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-5 py-4 whitespace-nowrap">
                                        <span class="px-3 py-1.5 text-xs font-bold rounded-full bg-indigo-100 text-indigo-700">
                                            Niv. {{ $entry['current_level'] ?? 1 }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-4 whitespace-nowrap">
                                        <span class="font-bold text-gray-900">{{ number_format($entry['total_points'] ?? 0) }}</span>
                                        <span class="text-xs text-gray-400 ml-1">XP</span>
                                    </td>
                                    <td class="px-5 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-1.5">
                                            <i class="fas fa-medal text-amber-500"></i>
                                            <span class="font-semibold">{{ $entry['badges_count'] ?? 0 }}</span>
                                        </div>
                                    </td>
                                    <td class="px-5 py-4 whitespace-nowrap">
                                        @if(($entry['streak_days'] ?? 0) > 0)
                                            <div class="flex items-center gap-1.5 text-orange-600 font-semibold">
                                                <i class="fas fa-fire"></i>
                                                <span>{{ $entry['streak_days'] }} jours</span>
                                            </div>
                                        @else
                                            <span class="text-gray-300">—</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-16 text-center text-gray-500">
                                        <div class="w-20 h-20 mx-auto mb-5 bg-gray-100 rounded-2xl flex items-center justify-center">
                                            <i class="fas fa-users text-gray-300 text-3xl"></i>
                                        </div>
                                        <p class="font-semibold text-lg text-gray-700">Aucune donnée de classement</p>
                                        <p class="text-sm text-gray-400 mt-1">Soyez le premier à apparaître dans le classement !</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                @if($totalCount > 10)
                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 text-center">
                        <a href="{{ route('gamification.leaderboard') }}" class="text-indigo-600 hover:text-indigo-700 font-bold text-sm transition-colors">
                            Voir le classement complet ({{ $totalCount }} apprenants) <i class="fas fa-arrow-right ml-1.5"></i>
                        </a>
                    </div>
                @endif
            </div>
        </div>
        
    </div>
</div>
@endsection