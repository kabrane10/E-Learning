@extends('layouts.public')

@section('title', 'Gamification')

@section('content')
<div class="bg-gradient-to-br from-indigo-50 via-purple-50 to-pink-50 min-h-screen py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- En-tête utilisateur -->
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden mb-8">
            <div class="bg-gradient-to-r from-indigo-600 to-purple-600 px-8 py-12">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
                    <div class="flex items-center space-x-6 mb-4 lg:mb-0">
                        <img src="{{ auth()->user()->avatar }}" class="w-24 h-24 rounded-full border-4 border-white shadow-lg">
                        <div class="text-white">
                            <h1 class="text-3xl font-bold">{{ auth()->user()->name }}</h1>
                            <div class="flex flex-wrap items-center mt-2 gap-x-4 gap-y-1">
                                <span class="flex items-center">
                                    <span class="text-2xl mr-1">{{ $stats['current_level']['icon'] ?? '🌱' }}</span>
                                    <span>Niveau {{ $stats['current_level']['number'] ?? 1 }} - {{ $stats['current_level']['name'] ?? 'Débutant' }}</span>
                                </span>
                                <span class="hidden sm:inline w-1 h-1 bg-white/50 rounded-full"></span>
                                <span>🔥 {{ $stats['streak_days'] ?? 0 }} jours de série</span>
                                <span class="hidden sm:inline w-1 h-1 bg-white/50 rounded-full"></span>
                                <span>🏆 Rang #{{ $stats['rank'] ?? 1 }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="text-right text-white">
                        <div class="text-5xl font-bold">{{ number_format($stats['total_points'] ?? 0) }}</div>
                        <div class="text-white/80">points totaux</div>
                    </div>
                </div>
            </div>
            
            <!-- Barre de progression niveau -->
            @if(isset($stats['next_level']) && $stats['next_level'])
            <div class="px-8 py-6 bg-gray-50 border-t border-gray-200">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium text-gray-700">
                        Progression vers le niveau {{ $stats['next_level']['number'] }} - {{ $stats['next_level']['name'] }}
                    </span>
                    <span class="text-sm text-gray-500">
                        {{ number_format($stats['total_points'] ?? 0) }} / {{ number_format($stats['next_level']['points_required']) }} points
                    </span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-4">
                    <div class="bg-gradient-to-r from-indigo-600 to-purple-600 h-4 rounded-full transition-all duration-500"
                         style="width: {{ $stats['next_level']['progress'] ?? 0 }}%"></div>
                </div>
                @php
                    $remaining = ($stats['next_level']['points_required'] ?? 0) - ($stats['total_points'] ?? 0);
                @endphp
                <p class="text-xs text-gray-500 mt-2">
                    Plus que {{ number_format(max(0, $remaining)) }} points !
                </p>
            </div>
            @endif
        </div>
        
        <!-- Statistiques rapides -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
            <div class="bg-white rounded-xl shadow-sm p-6 text-center">
                <div class="text-4xl mb-2">🏆</div>
                <div class="text-3xl font-bold text-gray-900">{{ $stats['badges']['earned'] ?? 0 }}</div>
                <div class="text-sm text-gray-500">Badges</div>
                @php
                    $badgePercentage = ($stats['badges']['total'] ?? 1) > 0 
                        ? round((($stats['badges']['earned'] ?? 0) / ($stats['badges']['total'] ?? 1)) * 100) 
                        : 0;
                @endphp
                <div class="text-xs text-gray-400 mt-1">{{ $badgePercentage }}% complété</div>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-6 text-center">
                <div class="text-4xl mb-2">🎯</div>
                <div class="text-3xl font-bold text-gray-900">{{ $stats['achievements']['completed'] ?? 0 }}</div>
                <div class="text-sm text-gray-500">Succès</div>
                @php
                    $achievementPercentage = ($stats['achievements']['total'] ?? 1) > 0 
                        ? round((($stats['achievements']['completed'] ?? 0) / ($stats['achievements']['total'] ?? 1)) * 100) 
                        : 0;
                @endphp
                <div class="text-xs text-gray-400 mt-1">{{ $achievementPercentage }}% complété</div>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-6 text-center">
                <div class="text-4xl mb-2">📚</div>
                <div class="text-3xl font-bold text-gray-900">
                    {{ auth()->user()->enrolledCourses()->whereNotNull('enrollments.completed_at')->count() }}
                </div>
                <div class="text-sm text-gray-500">Cours terminés</div>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-6 text-center">
                <div class="text-4xl mb-2">❓</div>
                <div class="text-3xl font-bold text-gray-900">
                    {{ auth()->user()->quizAttempts()->where('is_passed', true)->count() }}
                </div>
                <div class="text-sm text-gray-500">Quiz réussis</div>
            </div>
        </div>
        
        <!-- Badges -->
        <div class="mb-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                <span class="w-8 h-8 bg-gradient-to-r from-indigo-600 to-purple-600 rounded-lg flex items-center justify-center mr-3">
                    <i class="fas fa-medal text-white text-sm"></i>
                </span>
                Mes Badges
            </h2>
            
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                @forelse($badges as $badge)
                    <div class="bg-white rounded-xl shadow-sm p-4 text-center transition-all hover:shadow-lg {{ $badge->is_earned ? '' : 'opacity-60' }}">
                        <div class="w-16 h-16 mx-auto mb-3 rounded-full bg-{{ $badge->color }}-100 flex items-center justify-center">
                            <span class="text-3xl">{{ $badge->icon }}</span>
                        </div>
                        <h3 class="font-medium text-gray-900 text-sm mb-1">{{ $badge->name }}</h3>
                        <p class="text-xs text-gray-500 line-clamp-2">{{ $badge->description }}</p>
                        
                        @if($badge->is_earned)
                            <div class="mt-2 text-xs text-green-600">
                                <i class="fas fa-check-circle mr-1"></i>Obtenu
                            </div>
                        @else
                            <div class="mt-2">
                                <div class="w-full bg-gray-200 rounded-full h-1.5">
                                    <div class="bg-indigo-600 h-1.5 rounded-full" 
                                         style="width: {{ $badge->progress['percentage'] ?? 0 }}%"></div>
                                </div>
                                <span class="text-xs text-gray-400">
                                    {{ $badge->progress['current'] ?? 0 }}/{{ $badge->progress['target'] ?? 1 }}
                                </span>
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="col-span-full py-8 text-center text-gray-500">
                        <i class="fas fa-medal text-4xl mb-3 opacity-30"></i>
                        <p>Aucun badge disponible</p>
                    </div>
                @endforelse
            </div>
        </div>
        
        <!-- Achievements -->
        <div class="mb-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                <span class="w-8 h-8 bg-gradient-to-r from-yellow-500 to-orange-500 rounded-lg flex items-center justify-center mr-3">
                    <i class="fas fa-star text-white text-sm"></i>
                </span>
                Succès
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @forelse($achievements as $achievement)
                    @php
                        $tierColor = $achievement->tier_color ?? '#CD7F32';
                        $tierName = $achievement->tier_name ?? 'Bronze';
                        
                        if (method_exists($achievement, 'getTierColor')) {
                            $tierColor = $achievement->getTierColor();
                        }
                        if (method_exists($achievement, 'getTierName')) {
                            $tierName = $achievement->getTierName();
                        }
                    @endphp
                    <div class="bg-white rounded-xl shadow-sm p-5 flex items-start space-x-4 {{ $achievement->is_completed ? '' : 'opacity-60' }}">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 rounded-xl flex items-center justify-center"
                                 style="background-color: {{ $tierColor }}20">
                                <span class="text-2xl">{{ $achievement->icon }}</span>
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between mb-1">
                                <h3 class="font-medium text-gray-900">{{ $achievement->name }}</h3>
                                <span class="text-xs px-2 py-1 rounded-full text-white"
                                      style="background-color: {{ $tierColor }}">
                                    {{ $tierName }}
                                </span>
                            </div>
                            <p class="text-sm text-gray-500 mb-2">{{ $achievement->description }}</p>
                            
                            @if($achievement->is_completed)
                                @if($achievement->is_claimed)
                                    <span class="text-xs text-green-600">
                                        <i class="fas fa-check-circle mr-1"></i>Récompense réclamée
                                    </span>
                                @else
                                    <form action="{{ route('gamification.claim', $achievement) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="text-xs text-indigo-600 hover:text-indigo-700 font-medium">
                                            <i class="fas fa-gift mr-1"></i>Réclamer {{ $achievement->points_reward }} points
                                        </button>
                                    </form>
                                @endif
                            @else
                                <div class="w-full bg-gray-200 rounded-full h-1.5">
                                    <div class="bg-yellow-500 h-1.5 rounded-full" 
                                         style="width: {{ $achievement->progress['percentage'] ?? 0 }}%"></div>
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-8 text-center text-gray-500">
                        <i class="fas fa-star text-4xl mb-3 opacity-30"></i>
                        <p>Aucun succès disponible</p>
                    </div>
                @endforelse
            </div>
        </div>
        
        <!-- Classement -->
        <div>
            <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                <span class="w-8 h-8 bg-gradient-to-r from-green-500 to-teal-500 rounded-lg flex items-center justify-center mr-3">
                    <i class="fas fa-trophy text-white text-sm"></i>
                </span>
                Classement
            </h2>
            
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Rang</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Utilisateur</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Niveau</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Points</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Badges</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Série</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($leaderboard as $index => $entry)
                                <tr class="{{ ($entry['id'] ?? '') === auth()->id() ? 'bg-indigo-50' : '' }}">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($index === 0)
                                            <span class="text-2xl">🥇</span>
                                        @elseif($index === 1)
                                            <span class="text-2xl">🥈</span>
                                        @elseif($index === 2)
                                            <span class="text-2xl">🥉</span>
                                        @else
                                            <span class="text-gray-500">#{{ $index + 1 }}</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <img src="{{ $entry['avatar'] ?? 'https://ui-avatars.com/api/?name=' . urlencode($entry['name'] ?? 'User') }}" 
                                                 class="w-8 h-8 rounded-full mr-3">
                                            <span class="font-medium text-gray-900">{{ $entry['name'] ?? 'Inconnu' }}</span>
                                            @if(($entry['id'] ?? '') === auth()->id())
                                                <span class="ml-2 text-xs text-indigo-600">(Vous)</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 py-1 text-xs rounded-full bg-indigo-100 text-indigo-700">
                                            Niv. {{ $entry['current_level'] ?? 1 }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-900">
                                        {{ number_format($entry['total_points'] ?? 0) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-gray-600">
                                        {{ $entry['badges_count'] ?? 0 }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if(($entry['streak_days'] ?? 0) > 0)
                                            <span class="text-orange-600">
                                                <i class="fas fa-fire mr-1"></i>{{ $entry['streak_days'] }}
                                            </span>
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                        <i class="fas fa-trophy text-4xl mb-3 opacity-30"></i>
                                        <p>Aucune donnée de classement disponible</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection