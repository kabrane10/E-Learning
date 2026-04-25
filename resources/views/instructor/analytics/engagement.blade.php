@extends('layouts.instructor')

@section('title', 'Engagement des Étudiants')
@section('page-title', 'Engagement des Étudiants')

@section('breadcrumb')
<nav class="flex" aria-label="Breadcrumb">
    <ol class="flex items-center space-x-2">
        <li><a href="{{ route('instructor.dashboard') }}" class="text-gray-400 hover:text-gray-500"><i class="fas fa-home"></i></a></li>
        <li><i class="fas fa-chevron-right text-gray-300 text-xs"></i></li>
        <li><a href="{{ route('instructor.analytics') }}" class="text-gray-400 hover:text-gray-500">Analyses</a></li>
        <li><i class="fas fa-chevron-right text-gray-300 text-xs"></i></li>
        <li class="text-sm font-medium text-gray-700">Engagement</li>
    </ol>
</nav>
@endsection

@section('content')
<div x-data="engagementAnalytics()" x-init="init()">
    
    <!-- Retour -->
    <div class="mb-6">
        <a href="{{ route('instructor.analytics') }}" class="text-indigo-600 hover:text-indigo-700 font-medium">
            <i class="fas fa-arrow-left mr-2"></i>Retour aux analyses
        </a>
    </div>

    <!-- Filtre Période -->
    <div class="mb-6 flex items-center justify-between">
        <h2 class="text-xl font-semibold text-gray-900">Engagement en temps réel</h2>
        <select x-model="selectedPeriod" @change="loadData()" 
                class="px-4 py-2 border border-gray-300 rounded-lg text-sm">
            <option value="30">30 derniers jours</option>
            <option value="90">90 derniers jours</option>
            <option value="180">6 derniers mois</option>
            <option value="365">12 derniers mois</option>
        </select>
    </div>

    <!-- KPIs Principaux -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
        <!-- Taux de complétion moyen -->
        <div class="stat-card bg-white rounded-xl shadow-sm border border-gray-200 p-5 hover:shadow-md transition-all">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wider">Taux complétion moyen</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1">
                        <span x-text="stats.avg_completion"></span>%
                    </p>
                    <div class="mt-2" :class="stats.completion_trend >= 0 ? 'text-green-600' : 'text-red-600'">
                        <i class="fas text-xs mr-1" :class="stats.completion_trend >= 0 ? 'fa-arrow-up' : 'fa-arrow-down'"></i>
                        <span class="text-sm font-medium" x-text="Math.abs(stats.completion_trend) + '%'"></span>
                        <span class="text-xs text-gray-400 ml-1">vs période précédente</span>
                    </div>
                </div>
                <div class="w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-trophy text-emerald-600 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Temps de visionnage moyen -->
        <div class="stat-card bg-white rounded-xl shadow-sm border border-gray-200 p-5 hover:shadow-md transition-all">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wider">Temps moyen passé</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1">
                        <span x-text="stats.avg_watch_time"></span>h
                    </p>
                    <p class="text-xs text-gray-500 mt-1">par étudiant</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-clock text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Étudiants actifs -->
        <div class="stat-card bg-white rounded-xl shadow-sm border border-gray-200 p-5 hover:shadow-md transition-all">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wider">Étudiants actifs</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1" x-text="stats.active_students"></p>
                    <p class="text-xs text-gray-500 mt-1">ce mois</p>
                </div>
                <div class="w-12 h-12 bg-indigo-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-users text-indigo-600 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Taux d'abandon -->
        <div class="stat-card bg-white rounded-xl shadow-sm border border-gray-200 p-5 hover:shadow-md transition-all">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wider">Taux d'abandon</p>
                    <p class="text-3xl font-bold mt-1" 
                       :class="stats.dropout_rate > 20 ? 'text-red-600' : 'text-amber-600'">
                        <span x-text="stats.dropout_rate"></span>%
                    </p>
                    <p class="text-xs text-gray-500 mt-1">
                        <span x-show="stats.dropout_rate <= 10">✅ Excellent</span>
                        <span x-show="stats.dropout_rate > 10 && stats.dropout_rate <= 20">⚠️ Moyen</span>
                        <span x-show="stats.dropout_rate > 20">🔴 Élevé</span>
                    </p>
                </div>
                <div class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-user-slash text-amber-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Graphiques -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Rétention -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                <h3 class="font-semibold text-gray-900 flex items-center">
                    <i class="fas fa-chart-line text-indigo-500 mr-2"></i>
                    Courbe de rétention
                </h3>
                <p class="text-xs text-gray-500 mt-0.5">% d'étudiants qui continuent après inscription</p>
            </div>
            <div class="p-6">
                <div style="position: relative; height: 250px;">
                    <canvas id="retentionChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Taux de complétion par cours -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                <h3 class="font-semibold text-gray-900 flex items-center">
                    <i class="fas fa-chart-bar text-emerald-500 mr-2"></i>
                    Taux de complétion par cours
                </h3>
                <p class="text-xs text-gray-500 mt-0.5">% d'étudiants ayant terminé chaque cours</p>
            </div>
            <div class="p-6">
                <div style="position: relative; height: 250px;">
                    <canvas id="completionChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Activité quotidienne -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-8">
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
            <h3 class="font-semibold text-gray-900 flex items-center">
                <i class="fas fa-calendar-alt text-purple-500 mr-2"></i>
                Activité quotidienne (30 derniers jours)
            </h3>
        </div>
        <div class="p-6">
            <div style="position: relative; height: 250px;">
                <canvas id="dailyActivityChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Détail par cours -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200 flex items-center justify-between">
            <h3 class="font-semibold text-gray-900 flex items-center">
                <i class="fas fa-list-ul text-orange-500 mr-2"></i>
                Détail par cours
            </h3>
            <span class="text-xs text-gray-500" x-text="courses.length + ' cours'"></span>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cours</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Inscrits</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Taux de complétion</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Temps moyen</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Dernière activité</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <template x-for="course in courses" :key="course.id">
                        <tr class="table-row-hover">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <img :src="course.thumbnail || 'https://images.unsplash.com/photo-1587620962725-abab7fe55159?w=60'" 
                                         class="w-10 h-10 rounded-lg object-cover">
                                    <div>
                                        <p class="font-medium text-gray-900" x-text="course.title"></p>
                                        <p class="text-xs text-gray-500" x-text="course.category"></p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="font-medium text-gray-900" x-text="course.students_count"></span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    <div class="w-32 bg-gray-200 rounded-full h-2">
                                        <div class="h-2 rounded-full transition-all duration-500"
                                             :class="course.completion_rate >= 60 ? 'bg-emerald-500' : (course.completion_rate >= 30 ? 'bg-amber-500' : 'bg-red-500')"
                                             :style="'width: ' + course.completion_rate + '%'"></div>
                                    </div>
                                    <span class="text-sm font-medium" x-text="course.completion_rate + '%'"></span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center text-gray-600">
                                <span x-text="course.avg_watch_time + 'h'"></span>
                            </td>
                            <td class="px-6 py-4 text-center text-sm text-gray-500">
                                <span x-text="course.last_activity"></span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="px-2 py-1 text-xs font-medium rounded-full"
                                      :class="course.completion_rate >= 60 ? 'bg-green-100 text-green-700' : (course.completion_rate >= 30 ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700')">
                                    <span x-show="course.completion_rate >= 60">✅ Bon</span>
                                    <span x-show="course.completion_rate >= 30 && course.completion_rate < 60">⚠️ Moyen</span>
                                    <span x-show="course.completion_rate < 30">🔴 Faible</span>
                                </span>
                            </td>
                        </tr>
                    </template>
                    <tr x-show="courses.length === 0">
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                            <i class="fas fa-chart-bar text-4xl mb-3 opacity-30"></i>
                            <p>Aucune donnée d'engagement disponible</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    // Variables globales pour les graphiques
    let retentionChartInstance = null;
    let completionChartInstance = null;
    let dailyActivityChartInstance = null;
    
    function engagementAnalytics() {
        return {
            selectedPeriod: '30',
            stats: {
                avg_completion: 0,
                avg_watch_time: 0,
                active_students: 0,
                dropout_rate: 0,
                completion_trend: 0
            },
            courses: [],
            retentionData: { labels: [], data: [] },
            completionByCourseData: { labels: [], data: [] },
            dailyActivityData: { labels: [], data: [] },
            
            async init() {
                await this.loadData();
            },
            
            async loadData() {
                try {
                    const response = await fetch(`/api/instructor/analytics/engagement?period=${this.selectedPeriod}`, {
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    });
                    
                    if (response.ok) {
                        const data = await response.json();
                        
                        // Mettre à jour les statistiques
                        this.stats = data.stats;
                        
                        // Mettre à jour les cours
                        this.courses = data.courses;
                        
                        // Mettre à jour les données des graphiques
                        this.retentionData = data.charts.retention;
                        this.completionByCourseData = data.charts.completion_by_course;
                        this.dailyActivityData = data.charts.daily_activity;
                        
                        // Redessiner les graphiques
                        this.$nextTick(() => {
                            this.renderCharts();
                        });
                    }
                } catch (error) {
                    console.error('Erreur lors du chargement des données:', error);
                }
            },
            
            renderCharts() {
                this.renderRetentionChart();
                this.renderCompletionChart();
                this.renderDailyActivityChart();
            },
            
            renderRetentionChart() {
                const canvas = document.getElementById('retentionChart');
                if (!canvas) return;
                
                const ctx = canvas.getContext('2d');
                if (retentionChartInstance) retentionChartInstance.destroy();
                
                retentionChartInstance = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: this.retentionData.labels.length > 0 ? this.retentionData.labels : ['Sem 1', 'Sem 2', 'Sem 3', 'Sem 4'],
                        datasets: [{
                            label: 'Rétention %',
                            data: this.retentionData.data.length > 0 ? this.retentionData.data : [100, 85, 74, 68],
                            borderColor: '#4f46e5',
                            backgroundColor: 'rgba(79, 70, 229, 0.1)',
                            borderWidth: 2,
                            tension: 0.4,
                            fill: true,
                            pointBackgroundColor: '#4f46e5',
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
                                callbacks: {
                                    label: (ctx) => `${ctx.parsed.y}% de rétention`
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                max: 100,
                                ticks: { callback: (v) => v + '%' }
                            }
                        }
                    }
                });
            },
            
            renderCompletionChart() {
                const canvas = document.getElementById('completionChart');
                if (!canvas) return;
                
                const ctx = canvas.getContext('2d');
                if (completionChartInstance) completionChartInstance.destroy();
                
                const labels = this.completionByCourseData.labels.length > 0 ? this.completionByCourseData.labels : ['Aucun cours'];
                const data = this.completionByCourseData.data.length > 0 ? this.completionByCourseData.data : [0];
                
                completionChartInstance = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Complétion %',
                            data: data,
                            backgroundColor: data.map(v => v >= 60 ? 'rgba(16, 185, 129, 0.8)' : (v >= 30 ? 'rgba(245, 158, 11, 0.8)' : 'rgba(239, 68, 68, 0.8)')),
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
                                callbacks: {
                                    label: (ctx) => `${ctx.parsed.y}% complété`
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                max: 100,
                                ticks: { callback: (v) => v + '%' }
                            }
                        }
                    }
                });
            },
            
            renderDailyActivityChart() {
                const canvas = document.getElementById('dailyActivityChart');
                if (!canvas) return;
                
                const ctx = canvas.getContext('2d');
                if (dailyActivityChartInstance) dailyActivityChartInstance.destroy();
                
                const labels = this.dailyActivityData.labels.length > 0 ? this.dailyActivityData.labels : [];
                const data = this.dailyActivityData.data.length > 0 ? this.dailyActivityData.data : [];
                
                dailyActivityChartInstance = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Activités',
                            data: data,
                            backgroundColor: 'rgba(139, 92, 246, 0.7)',
                            borderRadius: 6,
                            borderSkipped: false
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: { stepSize: 1 }
                            }
                        }
                    }
                });
            }
        }
    }
</script>
@endpush