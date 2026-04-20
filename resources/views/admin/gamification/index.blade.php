@extends('layouts.admin')

@section('title', 'Gamification')

@section('breadcrumb')
<nav class="flex" aria-label="Breadcrumb">
    <ol class="flex items-center space-x-2">
        <li><a href="{{ route('admin.dashboard') }}" class="text-gray-400 hover:text-gray-500"><i class="fas fa-home"></i></a></li>
        <li><i class="fas fa-chevron-right text-gray-300 text-xs"></i></li>
        <li class="text-sm font-medium text-gray-700">Gamification</li>
    </ol>
</nav>
@endsection

@section('content')
<div class="py-6" x-data="gamificationAdminManager()">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- En-tête -->
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Gamification</h1>
            <p class="text-gray-500 mt-1">Gérez les badges, succès, niveaux et récompenses</p>
        </div>

        <!-- Onglets -->
        <div class="mb-6 border-b border-gray-200">
            <nav class="-mb-px flex space-x-8">
                <a href="{{ route('admin.gamification.index') }}" 
                   class="border-indigo-600 text-indigo-600 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    <i class="fas fa-chart-pie mr-2"></i>Vue d'ensemble
                </a>
                <a href="{{ route('admin.gamification.badges') }}" 
                   class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    <i class="fas fa-medal mr-2"></i>Badges
                </a>
                <a href="{{ route('admin.gamification.achievements') }}" 
                   class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    <i class="fas fa-trophy mr-2"></i>Succès
                </a>
                <a href="{{ route('admin.gamification.levels') }}" 
                   class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    <i class="fas fa-chart-line mr-2"></i>Niveaux
                </a>
            </nav>
        </div>

        <!-- KPIs -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-xl shadow-lg p-5 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-indigo-100 text-sm">Total points distribués</p>
                        <p class="text-3xl font-bold">{{ number_format($stats['total_points'] ?? 0) }}</p>
                        <p class="text-indigo-200 text-xs mt-1">+12.5% ce mois</p>
                    </div>
                    <div class="w-12 h-12 bg-white/20 rounded-lg flex items-center justify-center">
                        <i class="fas fa-star text-white text-xl"></i>
                    </div>
                </div>
            </div>
            <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg p-5 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-green-100 text-sm">Niveau moyen</p>
                        <p class="text-3xl font-bold">{{ $stats['average_level'] ?? 0 }}</p>
                        <p class="text-green-200 text-xs mt-1">sur 10 niveaux</p>
                    </div>
                    <div class="w-12 h-12 bg-white/20 rounded-lg flex items-center justify-center">
                        <i class="fas fa-arrow-up text-white text-xl"></i>
                    </div>
                </div>
            </div>
            <div class="bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-xl shadow-lg p-5 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-yellow-100 text-sm">Badges obtenus</p>
                        <p class="text-3xl font-bold">{{ number_format($stats['badges_earned'] ?? 0) }}</p>
                        <p class="text-yellow-200 text-xs mt-1">{{ \App\Models\UserBadge::whereNotNull('earned_at')->whereDate('earned_at', today())->count() }} aujourd'hui</p>
                    </div>
                    <div class="w-12 h-12 bg-white/20 rounded-lg flex items-center justify-center">
                        <i class="fas fa-medal text-white text-xl"></i>
                    </div>
                </div>
            </div>
            <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg p-5 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-purple-100 text-sm">Succès complétés</p>
                        <p class="text-3xl font-bold">{{ number_format($stats['achievements_completed'] ?? 0) }}</p>
                        <p class="text-purple-200 text-xs mt-1">{{ \App\Models\UserAchievement::whereNotNull('completed_at')->whereDate('completed_at', today())->count() }} aujourd'hui</p>
                    </div>
                    <div class="w-12 h-12 bg-white/20 rounded-lg flex items-center justify-center">
                        <i class="fas fa-trophy text-white text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Graphiques -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <!-- Distribution des niveaux -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Distribution des niveaux</h3>
                </div>
                <div class="p-6">
                    {{-- Conteneur avec hauteur fixe pour le canvas --}}
                    <div style="position: relative; height: 250px; width: 100%;">
                        <canvas id="levelsDistributionChart"></canvas>
                    </div>
                </div>
            </div>
            
            <!-- Points gagnés par jour -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Points gagnés (7 derniers jours)</h3>
                </div>
                <div class="p-6">
                    {{-- Conteneur avec hauteur fixe pour le canvas --}}
                    <div style="position: relative; height: 250px; width: 100%;">
                        <canvas id="pointsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top utilisateurs -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-6">
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200 flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900">🏆 Top 10 utilisateurs</h2>
                <div class="flex space-x-2">
                    <select id="leaderboardFilter" class="text-sm border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500" onchange="filterLeaderboard(this.value)">
                        <option value="points">Par points</option>
                        <option value="level">Par niveau</option>
                        <option value="badges">Par badges</option>
                        <option value="streak">Par série</option>
                    </select>
                    <button class="px-3 py-2 bg-white border border-gray-300 rounded-lg text-sm hover:bg-gray-50" onclick="exportLeaderboard()">
                        <i class="fas fa-download"></i>
                    </button>
                </div>
            </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Rang</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Utilisateur</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Niveau</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Points</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Badges</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Succès</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Série</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Dernière activité</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($topUsers as $index => $user)
                            <tr class="table-row-hover animate-slide-in" style="animation-delay: {{ $index * 30 }}ms">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($index === 0)
                                        <span class="text-2xl">🥇</span>
                                    @elseif($index === 1)
                                        <span class="text-2xl">🥈</span>
                                    @elseif($index === 2)
                                        <span class="text-2xl">🥉</span>
                                    @else
                                        <span class="text-gray-500 font-medium">#{{ $index + 1 }}</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <img src="{{ $user->avatar }}" class="w-10 h-10 rounded-full mr-3">
                                        <div>
                                            <p class="font-medium text-gray-900">{{ $user->name }}</p>
                                            <p class="text-xs text-gray-500">{{ $user->email }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $level = \App\Models\Level::find($user->current_level);
                                    @endphp
                                    <div class="flex items-center">
                                        <span class="text-2xl mr-2">{{ $level->icon ?? '📊' }}</span>
                                        <div>
                                            <p class="font-medium">{{ $level->name ?? 'Niveau ' . $user->current_level }}</p>
                                            <p class="text-xs text-gray-500">Niv. {{ $user->current_level }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <p class="font-semibold text-gray-900">{{ number_format($user->total_points) }}</p>
                                    <p class="text-xs text-gray-500">{{ number_format($user->experience_points) }} XP</p>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <span class="px-2 py-1 text-xs font-medium bg-indigo-100 text-indigo-700 rounded-full">
                                        {{ $user->badges()->whereNotNull('earned_at')->count() }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <span class="px-2 py-1 text-xs font-medium bg-purple-100 text-purple-700 rounded-full">
                                        {{ $user->achievements()->whereNotNull('completed_at')->count() }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($user->streak_days > 0)
                                        <div class="flex items-center">
                                            <i class="fas fa-fire text-orange-500 mr-1"></i>
                                            <span class="font-medium">{{ $user->streak_days }} jours</span>
                                        </div>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $user->last_activity_at ? $user->last_activity_at->diffForHumans() : 'Jamais' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <div class="flex items-center justify-end space-x-2">
                                        <a href="{{ route('admin.users.show', $user) }}" class="text-indigo-600 hover:text-indigo-900">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <button @click="awardPoints({{ $user->id }})" class="text-green-600 hover:text-green-900" title="Attribuer des points">
                                            <i class="fas fa-plus-circle"></i>
                                        </button>
                                        <button @click="awardBadge({{ $user->id }})" class="text-yellow-600 hover:text-yellow-900" title="Attribuer un badge">
                                            <i class="fas fa-medal"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-6 py-12 text-center text-gray-500">
                                    <i class="fas fa-users text-4xl mb-3 opacity-30"></i>
                                    <p>Aucun utilisateur trouvé</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Badges populaires et récents -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Badges les plus obtenus -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                    <h3 class="font-semibold text-gray-900">Badges les plus obtenus</h3>
                </div>
                <div class="p-6">
                    @php
                        $popularBadges = \App\Models\Badge::withCount('users')->orderBy('users_count', 'desc')->limit(5)->get();
                    @endphp
                    @forelse($popularBadges as $badge)
                        <div class="flex items-center justify-between py-3 border-b border-gray-100 last:border-0">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-{{ $badge->color }}-100 rounded-lg flex items-center justify-center">
                                    <span class="text-xl">{{ $badge->icon }}</span>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900">{{ $badge->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $badge->category }}</p>
                                </div>
                            </div>
                            <span class="text-lg font-semibold text-gray-900">{{ $badge->users_count }}</span>
                        </div>
                    @empty
                        <p class="text-gray-500 text-center py-4">Aucun badge</p>
                    @endforelse
                </div>
            </div>
            
            <!-- Badges récemment obtenus -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                    <h3 class="font-semibold text-gray-900">Badges récemment obtenus</h3>
                </div>
                <div class="p-6">
                    @php
                        $recentBadges = \App\Models\UserBadge::with(['user', 'badge'])
                            ->whereNotNull('earned_at')
                            ->latest('earned_at')
                            ->limit(5)
                            ->get();
                    @endphp
                    @forelse($recentBadges as $userBadge)
                        <div class="flex items-center justify-between py-3 border-b border-gray-100 last:border-0">
                            <div class="flex items-center space-x-3">
                                <img src="{{ $userBadge->user->avatar }}" class="w-8 h-8 rounded-full">
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ $userBadge->user->name }}</p>
                                    <p class="text-xs text-gray-500">
                                        a obtenu <span class="text-indigo-600">{{ $userBadge->badge->name }}</span>
                                    </p>
                                </div>
                            </div>
                            <span class="text-xs text-gray-400">{{ $userBadge->earned_at->diffForHumans() }}</span>
                        </div>
                    @empty
                        <p class="text-gray-500 text-center py-4">Aucun badge récent</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Attribution de points -->
    <div x-show="pointsModalOpen" 
         class="fixed inset-0 z-50 overflow-y-auto"
         x-transition
         style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-gray-900 bg-opacity-50" @click="pointsModalOpen = false"></div>
            <div class="relative bg-white rounded-2xl max-w-md w-full shadow-2xl">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Attribuer des points</h3>
                </div>
                <form @submit.prevent="submitAwardPoints">
                    <div class="p-6 space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Montant</label>
                            <input type="number" x-model="pointsForm.amount" min="1" required
                                   class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Raison</label>
                            <input type="text" x-model="pointsForm.reason" required
                                   class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500"
                                   placeholder="Ex: Participation exceptionnelle">
                        </div>
                    </div>
                    <div class="px-6 py-4 bg-gray-50 rounded-b-2xl flex justify-end space-x-3">
                        <button type="button" @click="pointsModalOpen = false" class="px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                            Annuler
                        </button>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                            Attribuer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
{{-- Charger Chart.js depuis CDN --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<script>
    // Variables globales pour les graphiques
    let levelsChartInstance = null;
    let pointsChartInstance = null;
    
    function gamificationAdminManager() {
        return {
            pointsModalOpen: false,
            selectedUserId: null,
            pointsForm: {
                amount: 100,
                reason: ''
            },
            
            awardPoints(userId) {
                this.selectedUserId = userId;
                this.pointsForm = { amount: 100, reason: '' };
                this.pointsModalOpen = true;
            },
            
            submitAwardPoints() {
                fetch(`/api/admin/users/${this.selectedUserId}/award-points`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(this.pointsForm)
                })
                .then(r => r.json())
                .then(data => {
                    this.pointsModalOpen = false;
                    alert('Points attribués avec succès !');
                    setTimeout(() => window.location.reload(), 1000);
                })
                .catch(err => {
                    console.error('Erreur:', err);
                    alert('Erreur lors de l\'attribution des points');
                });
            },
            
            awardBadge(userId) {
                window.location.href = `/admin/users/${userId}/badges`;
            }
        }
    }
    
    // Fonctions globales pour les boutons
    function filterLeaderboard(type) {
        window.location.href = `{{ route('admin.gamification.index') }}?filter=${type}`;
    }
    
    function exportLeaderboard() {
        // 
    }
    
    document.addEventListener('DOMContentLoaded', function() {
        // Vérifier que Chart.js est chargé
        if (typeof Chart === 'undefined') {
            console.error('Chart.js non chargé !');
            return;
        }
        
        // Données des niveaux
        @php
            $defaultLevelLabels = ['Niv.1', 'Niv.2', 'Niv.3', 'Niv.4', 'Niv.5', 'Niv.6', 'Niv.7', 'Niv.8', 'Niv.9', 'Niv.10'];
            $defaultLevelData = [245, 189, 156, 98, 67, 34, 18, 8, 3, 1];
    
            $levelLabels = isset($levelDistribution) && isset($levelDistribution['labels']) && !empty($levelDistribution['labels']) 
                ? $levelDistribution['labels'] 
                : $defaultLevelLabels;
    
            $levelData = isset($levelDistribution) && isset($levelDistribution['data']) && !empty($levelDistribution['data'])
                ? $levelDistribution['data']
                : $defaultLevelData;
        @endphp

        const levelLabels = @json($levelLabels);
        const levelData = @json($levelData);
        
        // Graphique de distribution des niveaux
        const levelsCanvas = document.getElementById('levelsDistributionChart');
        if (levelsCanvas) {
            const ctx = levelsCanvas.getContext('2d');
            
            // Détruire l'instance précédente si elle existe
            if (levelsChartInstance) {
                levelsChartInstance.destroy();
            }
            
            levelsChartInstance = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: levelLabels,
                    datasets: [{
                        label: 'Nombre d\'utilisateurs',
                        data: levelData,
                        backgroundColor: 'rgba(79, 70, 229, 0.8)',
                        borderRadius: 8,
                        borderSkipped: false
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#1f2937',
                            titleColor: '#f3f4f6',
                            bodyColor: '#d1d5db',
                            padding: 12,
                            cornerRadius: 8
                        }
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
            
            console.log('✅ Graphique de distribution des niveaux créé');
        }
        
        // Données des points
        @php
            $defaultPointsLabels = ['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim'];
            $defaultPointsData = [1250, 1420, 1680, 1350, 1890, 2100, 1950];
    
            $pointsLabels = isset($pointsChart) && isset($pointsChart['labels']) && !empty($pointsChart['labels'])
                ? $pointsChart['labels']
                : $defaultPointsLabels;
    
            $pointsData = isset($pointsChart) && isset($pointsChart['data']) && !empty($pointsChart['data'])
                ? $pointsChart['data']
                : $defaultPointsData;
        @endphp

        const pointsLabels = @json($pointsLabels);
        const pointsData = @json($pointsData);
        // Graphique des points
        const pointsCanvas = document.getElementById('pointsChart');
        if (pointsCanvas) {
            const ctx = pointsCanvas.getContext('2d');
            
            // Détruire l'instance précédente si elle existe
            if (pointsChartInstance) {
                pointsChartInstance.destroy();
            }
            
            pointsChartInstance = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: pointsLabels,
                    datasets: [{
                        label: 'Points gagnés',
                        data: pointsData,
                        borderColor: 'rgb(16, 185, 129)',
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        borderWidth: 2,
                        tension: 0.4,
                        fill: true,
                        pointBackgroundColor: 'rgb(16, 185, 129)',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#1f2937',
                            titleColor: '#f3f4f6',
                            bodyColor: '#d1d5db',
                            padding: 12,
                            cornerRadius: 8,
                            callbacks: {
                                label: (context) => `${context.parsed.y} points`
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: '#e5e7eb' }
                        },
                        x: {
                            grid: { display: false }
                        }
                    }
                }
            });
            
            console.log('✅ Graphique des points créé');
        }
    });
    
    // Nettoyer les graphiques avant de quitter la page
    window.addEventListener('beforeunload', function() {
        if (levelsChartInstance) {
            levelsChartInstance.destroy();
        }
        if (pointsChartInstance) {
            pointsChartInstance.destroy();
        }
    });
</script>
@endpush