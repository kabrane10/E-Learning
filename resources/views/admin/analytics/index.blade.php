@extends('layouts.admin')

@section('title', 'Analytique avancée')

@section('breadcrumb')
<nav class="flex" aria-label="Breadcrumb">
    <ol class="flex items-center space-x-2">
        <li><a href="{{ route('admin.dashboard') }}" class="text-gray-400 hover:text-gray-500"><i class="fas fa-home"></i></a></li>
        <li><i class="fas fa-chevron-right text-gray-300 text-xs"></i></li>
        <li class="text-sm font-medium text-gray-700">Analytique avancée</li>
    </ol>
</nav>
@endsection

@section('content')
<div class="py-6" x-data="analyticsManager()">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Analytique avancée</h1>
                <p class="text-gray-500 mt-1">Données détaillées sur l'engagement et la rétention</p>
            </div>
            <div class="mt-4 sm:mt-0 flex items-center space-x-3">
                <select x-model="dateRange" @change="updateAllCharts()" 
                        class="text-sm border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="7">7 derniers jours</option>
                    <option value="30" selected>30 derniers jours</option>
                    <option value="90">90 derniers jours</option>
                    <option value="365">12 derniers mois</option>
                </select>
                <button @click="exportData()" 
                        class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors text-sm">
                    <i class="fas fa-download mr-2"></i>Exporter
                </button>
            </div>
        </div>
        
        <!-- Métriques principales -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 stat-card">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-500 uppercase">Utilisateurs actifs</p>
                        <p class="text-2xl font-bold text-gray-900">1,847</p>
                        <p class="text-xs text-green-600"><i class="fas fa-arrow-up"></i> +8.2%</p>
                    </div>
                    <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-users text-indigo-600"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 stat-card">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-500 uppercase">Taux d'engagement</p>
                        <p class="text-2xl font-bold text-gray-900">64%</p>
                        <p class="text-xs text-green-600"><i class="fas fa-arrow-up"></i> +5.3%</p>
                    </div>
                    <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-chart-line text-green-600"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 stat-card">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-500 uppercase">Sessions/jour</p>
                        <p class="text-2xl font-bold text-gray-900">342</p>
                        <p class="text-xs text-green-600"><i class="fas fa-arrow-up"></i> +12.1%</p>
                    </div>
                    <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-calendar-day text-yellow-600"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 stat-card">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-500 uppercase">Pages vues</p>
                        <p class="text-2xl font-bold text-gray-900">12.4k</p>
                        <p class="text-xs text-green-600"><i class="fas fa-arrow-up"></i> +18.7%</p>
                    </div>
                    <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-eye text-purple-600"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Métriques d'engagement -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
            <!-- Taux de rétention -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Taux de rétention</h3>
                <div style="position: relative; height: 200px;">
                    <canvas id="retentionChart"></canvas>
                </div>
                <div class="mt-4 text-center">
                    <p class="text-3xl font-bold text-gray-900">68%</p>
                    <p class="text-sm text-gray-500">après 30 jours</p>
                </div>
            </div>
            
            <!-- Temps passé par jour -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Temps passé par jour</h3>
                <div style="position: relative; height: 200px;">
                    <canvas id="dailyTimeChart"></canvas>
                </div>
                <div class="mt-4 text-center">
                    <p class="text-3xl font-bold text-gray-900">47 min</p>
                    <p class="text-sm text-gray-500">moyenne par utilisateur</p>
                </div>
            </div>
            
            <!-- Appareils utilisés -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Appareils utilisés</h3>
                <div style="position: relative; height: 200px;">
                    <canvas id="devicesChart"></canvas>
                </div>
            </div>
        </div>
        
        <!-- Heatmap d'activité -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Activité par heure (30 derniers jours)</h3>
                <div class="flex items-center space-x-4">
                    <div class="flex items-center"><span class="w-3 h-3 bg-indigo-200 rounded mr-1"></span><span class="text-xs text-gray-500">Faible</span></div>
                    <div class="flex items-center"><span class="w-3 h-3 bg-indigo-400 rounded mr-1"></span><span class="text-xs text-gray-500">Moyen</span></div>
                    <div class="flex items-center"><span class="w-3 h-3 bg-indigo-600 rounded mr-1"></span><span class="text-xs text-gray-500">Élevé</span></div>
                </div>
            </div>
            <div class="overflow-x-auto">
                <div class="min-w-[800px]">
                    <div class="grid grid-cols-8 gap-1">
                        <div class="text-xs text-gray-500 py-2"></div>
                        @foreach(['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim'] as $day)
                            <div class="text-xs text-gray-500 text-center py-2 font-medium">{{ $day }}</div>
                        @endforeach
                        
                        @foreach(range(0, 23) as $hour)
                            <div class="text-xs text-gray-500 py-1 font-medium">{{ sprintf('%02d:00', $hour) }}</div>
                            @foreach(range(0, 6) as $dayIndex)
                                @php
                                    // Simuler une intensité d'activité
                                    $seed = ($hour * 7 + $dayIndex) * 7;
                                    $intensity = (sin($seed) * cos($seed * 0.5) + 1) * 5;
                                    $intensity = max(1, min(10, round($intensity)));
                                    
                                    $bgClass = $intensity > 7 ? 'bg-indigo-600' : ($intensity > 4 ? 'bg-indigo-400' : 'bg-indigo-200');
                                    $opacity = $intensity / 10;
                                @endphp
                                <div class="h-8 rounded {{ $bgClass }} transition-all hover:scale-105 cursor-pointer tooltip"
                                     style="opacity: {{ $opacity }}"
                                     title="{{ sprintf('%02d:00', $hour) }} - {{ $intensity * 10 }} activités"
                                     data-hour="{{ $hour }}"
                                     data-day="{{ $dayIndex }}"
                                     data-intensity="{{ $intensity }}"
                                     @click="showHeatmapDetail($event)"></div>
                            @endforeach
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Top utilisateurs -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">Top 10 utilisateurs les plus actifs</h3>
                <a href="{{ route('admin.users.index') }}" class="text-sm text-indigo-600 hover:text-indigo-700">
                    Voir tous les utilisateurs <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Utilisateur</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cours suivis</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Heures</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Quiz réussis</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Score moyen</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Série de jours</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Engagement</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @php
                            $topUsers = [
                                ['name' => 'Sophie Martin', 'avatar' => 'SM', 'courses' => 12, 'hours' => 87, 'quizzes' => 24, 'avg_score' => 92, 'streak' => 15, 'engagement' => 98],
                                ['name' => 'Thomas Dubois', 'avatar' => 'TD', 'courses' => 10, 'hours' => 76, 'quizzes' => 20, 'avg_score' => 88, 'streak' => 12, 'engagement' => 94],
                                ['name' => 'Marie Lambert', 'avatar' => 'ML', 'courses' => 9, 'hours' => 68, 'quizzes' => 18, 'avg_score' => 94, 'streak' => 10, 'engagement' => 91],
                                ['name' => 'Lucas Bernard', 'avatar' => 'LB', 'courses' => 8, 'hours' => 62, 'quizzes' => 16, 'avg_score' => 85, 'streak' => 8, 'engagement' => 87],
                                ['name' => 'Emma Petit', 'avatar' => 'EP', 'courses' => 7, 'hours' => 54, 'quizzes' => 14, 'avg_score' => 91, 'streak' => 7, 'engagement' => 84],
                                ['name' => 'Hugo Moreau', 'avatar' => 'HM', 'courses' => 6, 'hours' => 48, 'quizzes' => 12, 'avg_score' => 87, 'streak' => 6, 'engagement' => 79],
                                ['name' => 'Chloé Roux', 'avatar' => 'CR', 'courses' => 6, 'hours' => 45, 'quizzes' => 11, 'avg_score' => 90, 'streak' => 5, 'engagement' => 76],
                                ['name' => 'Antoine Girard', 'avatar' => 'AG', 'courses' => 5, 'hours' => 42, 'quizzes' => 10, 'avg_score' => 84, 'streak' => 4, 'engagement' => 72],
                                ['name' => 'Léa Fournier', 'avatar' => 'LF', 'courses' => 5, 'hours' => 38, 'quizzes' => 9, 'avg_score' => 89, 'streak' => 3, 'engagement' => 68],
                                ['name' => 'Nathan Mercier', 'avatar' => 'NM', 'courses' => 4, 'hours' => 35, 'quizzes' => 8, 'avg_score' => 86, 'streak' => 2, 'engagement' => 64],
                            ];
                        @endphp
                        
                        @foreach($topUsers as $index => $user)
                            <tr class="table-row-hover animate-slide-in" style="animation-delay: {{ $index * 30 }}ms">
                                <td class="px-6 py-4 text-sm">
                                    @if($index < 3)
                                        <span class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold
                                                   {{ $index === 0 ? 'bg-yellow-100 text-yellow-700' : 
                                                      ($index === 1 ? 'bg-gray-200 text-gray-700' : 'bg-orange-100 text-orange-700') }}">
                                            {{ $index + 1 }}
                                        </span>
                                    @else
                                        <span class="text-gray-500">{{ $index + 1 }}</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 bg-indigo-100 rounded-full flex items-center justify-center mr-3">
                                            <span class="text-xs font-medium text-indigo-600">{{ $user['avatar'] }}</span>
                                        </div>
                                        <span class="font-medium text-gray-900">{{ $user['name'] }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-gray-600">{{ $user['courses'] }}</td>
                                <td class="px-6 py-4 text-gray-600">{{ $user['hours'] }}h</td>
                                <td class="px-6 py-4 text-gray-600">{{ $user['quizzes'] }}</td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-700 rounded-full">
                                        {{ $user['avg_score'] }}%
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <i class="fas fa-fire text-orange-500 mr-1"></i>
                                        <span class="font-medium">{{ $user['streak'] }} jours</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div class="w-16 bg-gray-200 rounded-full h-2 mr-2">
                                            <div class="h-2 rounded-full {{ $user['engagement'] >= 90 ? 'bg-green-600' : ($user['engagement'] >= 75 ? 'bg-yellow-600' : 'bg-orange-600') }}"
                                                 style="width: {{ $user['engagement'] }}%"></div>
                                        </div>
                                        <span class="text-sm text-gray-600">{{ $user['engagement'] }}%</span>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Modal détail heatmap -->
    <div x-show="heatmapModalOpen" 
         class="fixed inset-0 z-50 overflow-y-auto"
         x-transition
         style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-gray-900 bg-opacity-50" @click="heatmapModalOpen = false"></div>
            <div class="relative bg-white rounded-xl max-w-sm w-full shadow-2xl p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Détail d'activité</h3>
                <p class="text-gray-600" x-html="heatmapDetail"></p>
                <button @click="heatmapModalOpen = false" 
                        class="mt-4 w-full px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                    Fermer
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
{{-- Charger Chart.js --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<script>
    // Variables globales pour les graphiques
    let retentionChart = null;
    let dailyTimeChart = null;
    let devicesChart = null;
    
    function analyticsManager() {
        return {
            dateRange: '30',
            heatmapModalOpen: false,
            heatmapDetail: '',
            
            updateAllCharts() {
                // Mettre à jour les données selon la période
                let retentionData, dailyTimeData, devicesData;
                
                switch(this.dateRange) {
                    case '7':
                        retentionData = [100, 92, 88, 85];
                        dailyTimeData = [48, 52, 45, 50, 42, 38, 35];
                        devicesData = [55, 38, 7];
                        break;
                    case '30':
                        retentionData = [100, 85, 74, 68];
                        dailyTimeData = [52, 48, 55, 47, 42, 38, 35];
                        devicesData = [58, 35, 7];
                        break;
                    case '90':
                        retentionData = [100, 82, 70, 62];
                        dailyTimeData = [55, 50, 58, 49, 45, 40, 38];
                        devicesData = [60, 33, 7];
                        break;
                    case '365':
                        retentionData = [100, 78, 65, 55];
                        dailyTimeData = [58, 53, 60, 52, 48, 42, 40];
                        devicesData = [62, 31, 7];
                        break;
                }
                
                // Mettre à jour le graphique de rétention
                if (retentionChart) {
                    retentionChart.data.datasets[0].data = retentionData;
                    retentionChart.update();
                }
                
                // Mettre à jour le graphique de temps quotidien
                if (dailyTimeChart) {
                    dailyTimeChart.data.datasets[0].data = dailyTimeData;
                    dailyTimeChart.update();
                }
                
                // Mettre à jour le graphique des appareils
                if (devicesChart) {
                    devicesChart.data.datasets[0].data = devicesData;
                    devicesChart.update();
                }
            },
            
            showHeatmapDetail(event) {
                const hour = event.target.dataset.hour;
                const day = event.target.dataset.day;
                const intensity = event.target.dataset.intensity;
                const days = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche'];
                
                this.heatmapDetail = `
                    <strong>${days[day]}</strong> à <strong>${hour}:00</strong><br>
                    <span class="text-2xl font-bold text-indigo-600">${intensity * 10}</span> activités<br>
                    Niveau d'activité : ${intensity > 7 ? 'Élevé' : (intensity > 4 ? 'Moyen' : 'Faible')}
                `;
                this.heatmapModalOpen = true;
            },
            
            exportData() {
                // Créer un rapport complet
                const data = {
                    dateRange: this.dateRange,
                    metrics: {
                        activeUsers: 1847,
                        engagementRate: 64,
                        sessionsPerDay: 342,
                        pageViews: 12400
                    },
                    retention: retentionChart ? retentionChart.data.datasets[0].data : [],
                    dailyTime: dailyTimeChart ? dailyTimeChart.data.datasets[0].data : [],
                    devices: devicesChart ? devicesChart.data.datasets[0].data : []
                };
                
                // Télécharger en JSON
                const blob = new Blob([JSON.stringify(data, null, 2)], { type: 'application/json' });
                const link = document.createElement('a');
                const url = URL.createObjectURL(blob);
                link.setAttribute('href', url);
                link.setAttribute('download', `analytics_${this.dateRange}_${new Date().toISOString().split('T')[0]}.json`);
                link.style.visibility = 'hidden';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            }
        }
    }
    
    // Initialisation des graphiques
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof Chart === 'undefined') {
            console.error('Chart.js non chargé !');
            return;
        }
        
        // Graphique de rétention
        const canvas1 = document.getElementById('retentionChart');
        if (canvas1) {
            const ctx1 = canvas1.getContext('2d');
            if (retentionChart) retentionChart.destroy();
            
            retentionChart = new Chart(ctx1, {
                type: 'line',
                data: {
                    labels: ['Jour 1', 'Jour 7', 'Jour 14', 'Jour 30'],
                    datasets: [{
                        label: 'Rétention',
                        data: [100, 85, 74, 68],
                        borderColor: 'rgb(79, 70, 229)',
                        backgroundColor: 'rgba(79, 70, 229, 0.1)',
                        tension: 0.4,
                        fill: true,
                        pointBackgroundColor: 'rgb(79, 70, 229)',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 5,
                        pointHoverRadius: 7
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
                                label: (context) => `${context.parsed.y}%`
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 100,
                            grid: { color: '#e5e7eb' },
                            ticks: {
                                callback: (value) => value + '%'
                            }
                        },
                        x: {
                            grid: { display: false }
                        }
                    }
                }
            });
        }
        
        // Graphique temps quotidien
        const canvas2 = document.getElementById('dailyTimeChart');
        if (canvas2) {
            const ctx2 = canvas2.getContext('2d');
            if (dailyTimeChart) dailyTimeChart.destroy();
            
            dailyTimeChart = new Chart(ctx2, {
                type: 'bar',
                data: {
                    labels: ['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim'],
                    datasets: [{
                        label: 'Minutes',
                        data: [52, 48, 55, 47, 42, 38, 35],
                        backgroundColor: [
                            'rgba(16, 185, 129, 0.8)',
                            'rgba(16, 185, 129, 0.7)',
                            'rgba(16, 185, 129, 0.8)',
                            'rgba(16, 185, 129, 0.7)',
                            'rgba(16, 185, 129, 0.6)',
                            'rgba(245, 158, 11, 0.7)',
                            'rgba(245, 158, 11, 0.7)'
                        ],
                        borderRadius: 8
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
                                label: (context) => `${context.parsed.y} minutes`
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: '#e5e7eb' },
                            ticks: {
                                callback: (value) => value + ' min'
                            }
                        },
                        x: {
                            grid: { display: false }
                        }
                    }
                }
            });
        }
        
        // Graphique appareils
        const canvas3 = document.getElementById('devicesChart');
        if (canvas3) {
            const ctx3 = canvas3.getContext('2d');
            if (devicesChart) devicesChart.destroy();
            
            devicesChart = new Chart(ctx3, {
                type: 'doughnut',
                data: {
                    labels: ['Desktop', 'Mobile', 'Tablette'],
                    datasets: [{
                        data: [58, 35, 7],
                        backgroundColor: [
                            'rgb(79, 70, 229)',
                            'rgb(16, 185, 129)',
                            'rgb(245, 158, 11)'
                        ],
                        borderWidth: 0,
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 15,
                                usePointStyle: true,
                                font: { size: 12 }
                            }
                        },
                        tooltip: {
                            backgroundColor: '#1f2937',
                            titleColor: '#f3f4f6',
                            bodyColor: '#d1d5db',
                            padding: 12,
                            cornerRadius: 8,
                            callbacks: {
                                label: (context) => {
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = ((context.raw / total) * 100).toFixed(1);
                                    return `${context.label}: ${context.raw}% (${percentage}%)`;
                                }
                            }
                        }
                    },
                    cutout: '65%'
                }
            });
        }
        
        // Tooltips pour la heatmap
        document.querySelectorAll('[data-hour]').forEach(el => {
            el.addEventListener('mouseenter', function(e) {
                const hour = this.dataset.hour;
                const intensity = this.dataset.intensity;
                const dayIndex = this.dataset.day;
                const days = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche'];
                
                // Créer un tooltip personnalisé
                const tooltip = document.createElement('div');
                tooltip.className = 'fixed bg-gray-900 text-white text-xs rounded-lg px-3 py-2 z-50 pointer-events-none';
                tooltip.style.left = (e.clientX + 10) + 'px';
                tooltip.style.top = (e.clientY - 30) + 'px';
                tooltip.innerHTML = `<strong>${days[dayIndex]}</strong> ${hour}:00<br>${intensity * 10} activités`;
                tooltip.id = 'heatmap-tooltip';
                document.body.appendChild(tooltip);
            });
            
            el.addEventListener('mouseleave', function() {
                const tooltip = document.getElementById('heatmap-tooltip');
                if (tooltip) tooltip.remove();
            });
        });
    });
</script>

<style>
    .tooltip {
        position: relative;
    }
</style>
@endpush