@extends('layouts.instructor')

@section('title', 'Tableau de bord')
@section('page-title', 'Tableau de bord')

@section('breadcrumb')
<nav class="flex" aria-label="Breadcrumb">
    <ol class="flex items-center space-x-2">
        <li><a href="{{ route('instructor.dashboard') }}" class="text-gray-400 hover:text-gray-500"><i class="fas fa-home"></i></a></li>
        <li><i class="fas fa-chevron-right text-gray-300 text-xs"></i></li>
        <li class="text-sm font-medium text-gray-700">Tableau de bord</li>
    </ol>
</nav>
@endsection

@section('content')
<div x-data="instructorDashboard({{ Js::from([
    'enrollmentsData' => $enrollmentsChart['data'] ?? [0,0,0,0,0,0,0],
    'enrollmentsLabels' => $enrollmentsChart['labels'] ?? ['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim'],
    'distributionData' => $distributionChart['data'] ?? [0, 0, 0, 0],
    'distributionLabels' => $distributionChart['labels'] ?? ['Aucun cours'],
    'availableBalance' => $availableBalance ?? 0,
]) }})" x-init="init">
    
    <!-- Message de bienvenue -->
    <div class="mb-6">
        <div class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-2xl p-6 text-white shadow-lg">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-bold mb-1">Bonjour, {{ Auth::user()->name }} ! 👋</h2>
                    <p class="text-indigo-100">Voici un aperçu de vos performances</p>
                </div>
                <div class="md:text-right">
                    <p class="text-indigo-100 text-sm">Solde disponible</p>
                    <p class="text-3xl font-bold" x-text="formatCurrency(availableBalance) + ' FCFA'"></p>
                    <a href="{{ route('instructor.earnings') }}" class="text-indigo-200 text-sm hover:text-white transition-colors">
                        Gérer mes revenus <i class="fas fa-arrow-right ml-1 text-xs"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- KPIs Principaux -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
        <!-- Total Étudiants -->
        <div class="stat-card bg-white rounded-xl shadow-sm border border-gray-200 p-5 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wider">Total Étudiants</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1">{{ number_format($stats['total_students'] ?? 0) }}</p>
                    @if(($stats['new_students_this_month'] ?? 0) > 0)
                        <div class="flex items-center mt-2 text-green-600">
                            <i class="fas fa-arrow-up text-xs mr-1"></i>
                            <span class="text-sm font-medium">+{{ $stats['new_students_this_month'] }}</span>
                            <span class="text-xs text-gray-400 ml-1">ce mois</span>
                        </div>
                    @else
                        <div class="flex items-center mt-2 text-gray-400">
                            <i class="fas fa-minus text-xs mr-1"></i>
                            <span class="text-sm">0 ce mois</span>
                        </div>
                    @endif
                </div>
                <div class="w-12 h-12 bg-indigo-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-users text-indigo-600 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Cours Publiés -->
        <div class="stat-card bg-white rounded-xl shadow-sm border border-gray-200 p-5 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wider">Cours Publiés</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1">{{ $stats['published_courses'] ?? 0 }}</p>
                    <div class="flex items-center mt-2 text-gray-500">
                        <span class="text-sm">{{ $stats['total_courses'] ?? 0 }} au total</span>
                        @if(($stats['draft_courses'] ?? 0) > 0)
                            <span class="text-xs text-gray-400 ml-2">{{ $stats['draft_courses'] }} brouillon(s)</span>
                        @endif
                    </div>
                </div>
                <div class="w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-book-open text-emerald-600 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Taux de Complétion -->
        <div class="stat-card bg-white rounded-xl shadow-sm border border-gray-200 p-5 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wider">Taux de Complétion</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1">{{ $stats['completion_rate'] ?? 0 }}%</p>
                    <div class="flex items-center mt-2">
                        @if(($stats['completion_trend'] ?? 0) > 0)
                            <span class="text-green-600"><i class="fas fa-arrow-up text-xs mr-1"></i>+{{ $stats['completion_trend'] }}%</span>
                        @elseif(($stats['completion_trend'] ?? 0) < 0)
                            <span class="text-red-600"><i class="fas fa-arrow-down text-xs mr-1"></i>{{ $stats['completion_trend'] }}%</span>
                        @else
                            <span class="text-amber-600"><i class="fas fa-minus text-xs mr-1"></i>Stable</span>
                        @endif
                    </div>
                </div>
                <div class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-trophy text-amber-600 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Note Moyenne -->
        <div class="stat-card bg-white rounded-xl shadow-sm border border-gray-200 p-5 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wider">Note Moyenne</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1">{{ number_format($stats['average_rating'] ?? 0, 1) }}</p>
                    <div class="flex items-center mt-2">
                        <div class="flex text-yellow-400">
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= round($stats['average_rating'] ?? 0))
                                    <i class="fas fa-star text-xs"></i>
                                @else
                                    <i class="far fa-star text-xs"></i>
                                @endif
                            @endfor
                        </div>
                        <span class="text-xs text-gray-400 ml-1">({{ $stats['total_reviews'] ?? 0 }} avis)</span>
                    </div>
                </div>
                <div class="w-12 h-12 bg-yellow-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-star text-yellow-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Graphiques -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- Inscriptions -->
        <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200 flex items-center justify-between">
                <h3 class="font-semibold text-gray-900">Inscriptions (7 derniers jours)</h3>
                <select x-model="chartPeriod" @change="updateCharts" class="text-sm border-gray-200 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="7">7 jours</option>
                    <option value="30">30 jours</option>
                    <option value="90">90 jours</option>
                </select>
            </div>
            <div class="p-6">
                <div style="position: relative; height: 250px;">
                    <canvas id="enrollmentsChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Répartition par cours -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                <h3 class="font-semibold text-gray-900">Répartition des étudiants</h3>
            </div>
            <div class="p-6">
                <div style="position: relative; height: 250px;">
                    <canvas id="distributionChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Cours Récents et Activité -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Mes Cours Récents -->
        <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200 flex items-center justify-between">
                <h3 class="font-semibold text-gray-900">Mes Cours Récents</h3>
                <a href="{{ route('instructor.courses.index') }}" class="text-sm text-indigo-600 hover:text-indigo-700">
                    Voir tout <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
            <div class="overflow-x-auto">
                @if(count($recentCourses ?? []) > 0)
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cours</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Étudiants</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Revenus</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Note</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase"></th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($recentCourses as $course)
                                <tr class="table-row-hover">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <img src="{{ $course->thumbnail_url ?? 'https://images.unsplash.com/photo-1587620962725-abab7fe55159?w=100' }}" 
                                                 class="w-10 h-10 rounded-lg object-cover">
                                            <span class="font-medium text-gray-900">{{ Str::limit($course->title, 30) }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-gray-600">{{ $course->students_count ?? 0 }}</td>
                                    <td class="px-6 py-4 text-gray-900 font-medium">
                                        {{ number_format($course->revenue ?? 0) }} FCFA
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            <i class="fas fa-star text-yellow-400 text-xs mr-1"></i>
                                            <span>{{ number_format($course->average_rating ?? 0, 1) }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="px-2 py-1 text-xs font-medium rounded-full {{ $course->is_published ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                                            {{ $course->is_published ? 'Publié' : 'Brouillon' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <a href="{{ route('instructor.courses.show', $course) }}" class="text-indigo-600 hover:text-indigo-900">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="p-8 text-center text-gray-500">
                        <i class="fas fa-book-open text-3xl mb-3 opacity-50"></i>
                        <p>Aucun cours pour le moment</p>
                        <a href="{{ route('instructor.courses.create') }}" class="mt-3 inline-block text-indigo-600 hover:text-indigo-700">
                            Créer votre premier cours
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Activité Récente et Actions Rapides -->
        <div class="space-y-6">
            <!-- Activité Récente -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                    <h3 class="font-semibold text-gray-900">Activité Récente</h3>
                </div>
                <div class="p-4">
                    @if(count($recentActivities ?? []) > 0)
                        <div class="space-y-4">
                            @foreach($recentActivities as $activity)
                                <div class="flex items-start gap-3">
                                    <div class="w-8 h-8 bg-{{ $activity['color'] }}-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <i class="fas {{ $activity['icon'] }} text-{{ $activity['color'] }}-600 text-sm"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm text-gray-800">{{ $activity['message'] }}</p>
                                        <p class="text-xs text-gray-500">{{ $activity['time'] }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-center text-gray-500 py-4">Aucune activité récente</p>
                    @endif
                </div>
            </div>

            <!-- Actions Rapides -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                    <h3 class="font-semibold text-gray-900">Actions Rapides</h3>
                </div>
                <div class="p-4">
                    <div class="grid grid-cols-2 gap-3">
                        <a href="{{ route('instructor.courses.create') }}" 
                           class="flex flex-col items-center p-4 bg-indigo-50 rounded-xl hover:bg-indigo-100 transition-colors group">
                            <div class="w-10 h-10 bg-indigo-600 rounded-lg flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                                <i class="fas fa-plus text-white"></i>
                            </div>
                            <span class="text-sm font-medium text-indigo-700">Nouveau cours</span>
                        </a>
                        
                        <a href="{{ route('instructor.quizzes.index') }}" 
                           class="flex flex-col items-center p-4 bg-purple-50 rounded-xl hover:bg-purple-100 transition-colors group">
                            <div class="w-10 h-10 bg-purple-600 rounded-lg flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                                <i class="fas fa-puzzle-piece text-white"></i>
                            </div>
                            <span class="text-sm font-medium text-purple-700">Mes quiz</span>
                        </a>
                        
                        <a href="{{ route('instructor.analytics') }}" 
                           class="flex flex-col items-center p-4 bg-emerald-50 rounded-xl hover:bg-emerald-100 transition-colors group">
                            <div class="w-10 h-10 bg-emerald-600 rounded-lg flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                                <i class="fas fa-chart-bar text-white"></i>
                            </div>
                            <span class="text-sm font-medium text-emerald-700">Voir analyses</span>
                        </a>
                        
                        <a href="{{ route('instructor.earnings') }}" 
                           class="flex flex-col items-center p-4 bg-amber-50 rounded-xl hover:bg-amber-100 transition-colors group">
                            <div class="w-10 h-10 bg-amber-600 rounded-lg flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                                <i class="fas fa-wallet text-white"></i>
                            </div>
                            <span class="text-sm font-medium text-amber-700">Mes revenus</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    let enrollmentsChart = null;
    let distributionChart = null;
    
    function instructorDashboard(config) {
        return {
            chartPeriod: '7',
            availableBalance: config.availableBalance || 0,
            
            init() {
                this.initCharts();
            },
            
            formatCurrency(amount) {
                return new Intl.NumberFormat('fr-FR').format(amount || 0);
            },
            
            updateCharts() {
                // Recharger les données selon la période
                fetch(`/api/instructor/dashboard-stats?period=${this.chartPeriod}`)
                    .then(r => r.json())
                    .then(data => {
                        if (enrollmentsChart) {
                            enrollmentsChart.data.labels = data.enrollments.labels;
                            enrollmentsChart.data.datasets[0].data = data.enrollments.data;
                            enrollmentsChart.update();
                        }
                    })
                    .catch(err => console.error('Erreur:', err));
            },
            
            initCharts() {
                // Graphique des inscriptions
                const enrollmentsCtx = document.getElementById('enrollmentsChart')?.getContext('2d');
                if (enrollmentsCtx) {
                    if (enrollmentsChart) enrollmentsChart.destroy();
                    
                    enrollmentsChart = new Chart(enrollmentsCtx, {
                        type: 'line',
                        data: {
                            labels: config.enrollmentsLabels || ['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim'],
                            datasets: [{
                                label: 'Inscriptions',
                                data: config.enrollmentsData || [0, 0, 0, 0, 0, 0, 0],
                                borderColor: 'rgb(79, 70, 229)',
                                backgroundColor: 'rgba(79, 70, 229, 0.1)',
                                borderWidth: 2,
                                tension: 0.4,
                                fill: true,
                                pointBackgroundColor: 'rgb(79, 70, 229)',
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
                }
                
                // Graphique de répartition
                const distributionCtx = document.getElementById('distributionChart')?.getContext('2d');
                if (distributionCtx) {
                    if (distributionChart) distributionChart.destroy();
                    
                    const colors = ['#4f46e5', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899'];
                    
                    distributionChart = new Chart(distributionCtx, {
                        type: 'doughnut',
                        data: {
                            labels: config.distributionLabels || ['Aucun cours'],
                            datasets: [{
                                data: config.distributionData || [1],
                                backgroundColor: colors.slice(0, config.distributionLabels?.length || 1),
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
                                    cornerRadius: 8
                                }
                            },
                            cutout: '65%'
                        }
                    });
                }
            }
        }
    }
</script>
@endpush