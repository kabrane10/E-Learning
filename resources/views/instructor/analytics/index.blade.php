@extends('layouts.instructor')

@section('title', 'Analyses')
@section('page-title', 'Analyses')

@section('breadcrumb')
<nav class="flex" aria-label="Breadcrumb">
    <ol class="flex items-center space-x-2">
        <li><a href="{{ route('instructor.dashboard') }}" class="text-gray-400 hover:text-gray-500"><i class="fas fa-home"></i></a></li>
        <li><i class="fas fa-chevron-right text-gray-300 text-xs"></i></li>
        <li class="text-sm font-medium text-gray-700">Analyses</li>
    </ol>
</nav>
@endsection

@section('content')
<div x-data="analyticsOverview()" x-init="init()">
    
    @php
        $periods = ['7' => '7 jours', '30' => '30 jours', '90' => '90 jours', '365' => '12 mois'];
        $currentPeriod = request('period', '30');
    @endphp

    <!-- Filtres de période -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <h2 class="text-xl font-semibold text-gray-900">Vue d'ensemble</h2>
        <div class="flex items-center gap-2 bg-white p-1.5 rounded-xl shadow-sm border border-gray-200">
            @foreach($periods as $value => $label)
                <button @click="setPeriod('{{ $value }}')" 
                        class="px-4 py-2 rounded-lg text-sm font-medium transition-colors"
                        :class="period === '{{ $value }}' ? 'bg-indigo-600 text-white' : 'text-gray-600 hover:bg-gray-100'">
                    {{ $label }}
                </button>
            @endforeach
        </div>
    </div>

    <!-- KPIs Principaux -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-5 mb-8">
        <!-- Total Étudiants -->
        <div class="stat-card bg-white rounded-xl shadow-sm border border-gray-200 p-5 hover:shadow-md transition-all">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 uppercase">Total Étudiants</p>
                    <p class="text-3xl font-bold text-gray-900">{{ number_format($stats['total_students']) }}</p>
                    @if($stats['new_students'] > 0)
                        <p class="text-xs text-green-600 mt-1"><i class="fas fa-arrow-up"></i> +{{ $stats['new_students'] }} ce mois</p>
                    @else
                        <p class="text-xs text-gray-500 mt-1">Aucun nouveau</p>
                    @endif
                </div>
                <div class="w-12 h-12 bg-indigo-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-users text-indigo-600 text-xl"></i>
                </div>
            </div>
        </div>
        
        <!-- Taux de Complétion -->
        <div class="stat-card bg-white rounded-xl shadow-sm border border-gray-200 p-5 hover:shadow-md transition-all">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 uppercase">Taux de Complétion</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $stats['completion_rate'] }}%</p>
                    @if($stats['completion_rate'] >= 50)
                        <p class="text-xs text-green-600 mt-1"><i class="fas fa-arrow-up"></i> Bon</p>
                    @else
                        <p class="text-xs text-amber-600 mt-1"><i class="fas fa-minus"></i> À améliorer</p>
                    @endif
                </div>
                <div class="w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-trophy text-emerald-600 text-xl"></i>
                </div>
            </div>
        </div>
        
        <!-- Note Moyenne -->
        <div class="stat-card bg-white rounded-xl shadow-sm border border-gray-200 p-5 hover:shadow-md transition-all">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 uppercase">Note Moyenne</p>
                    <p class="text-3xl font-bold text-gray-900">{{ number_format($stats['average_rating'], 1) }}/5</p>
                    <p class="text-xs text-gray-500 mt-1">{{ $stats['total_reviews'] }} avis</p>
                </div>
                <div class="w-12 h-12 bg-yellow-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-star text-yellow-600 text-xl"></i>
                </div>
            </div>
        </div>
        
        <!-- Revenus Totaux -->
        <div class="stat-card bg-white rounded-xl shadow-sm border border-gray-200 p-5 hover:shadow-md transition-all">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 uppercase">Revenus Totaux</p>
                    <p class="text-3xl font-bold text-gray-900">{{ number_format($stats['total_revenue'], 0) }} FCFA</p>
                    @if($stats['revenue_this_month'] > 0)
                        <p class="text-xs text-green-600 mt-1"><i class="fas fa-arrow-up"></i> +{{ number_format($stats['revenue_this_month'], 0) }} FCFA ce mois</p>
                    @else
                        <p class="text-xs text-gray-500 mt-1">Aucun revenu ce mois</p>
                    @endif
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-wallet text-green-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Graphiques principaux -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Évolution des inscriptions -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                <h3 class="font-semibold text-gray-900">Évolution des inscriptions</h3>
            </div>
            <div class="p-6">
                <div style="position: relative; height: 250px;">
                    <canvas id="enrollmentsTrendChart"></canvas>
                </div>
            </div>
        </div>
        
        <!-- Répartition par cours -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                <h3 class="font-semibold text-gray-900">Répartition des étudiants par cours</h3>
            </div>
            <div class="p-6">
                <div style="position: relative; height: 250px;">
                    <canvas id="coursesDistributionChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Cours par performance -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-8">
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200 flex items-center justify-between">
            <h3 class="font-semibold text-gray-900">Top Cours par performance</h3>
            <a href="{{ route('instructor.courses.index') }}" class="text-sm text-indigo-600 hover:text-indigo-700">Voir tout</a>
        </div>
        
        @if($topCourses->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cours</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Étudiants</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Revenus</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Complétion</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Note</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Statut</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($topCourses as $course)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <img src="{{ $course->thumbnail_url ?? 'https://images.unsplash.com/photo-1587620962725-abab7fe55159?w=60' }}" 
                                             class="w-10 h-10 rounded-lg object-cover">
                                        <div>
                                            <a href="{{ route('instructor.courses.show', $course) }}" class="font-medium text-gray-900 hover:text-indigo-600">
                                                {{ $course->title }}
                                            </a>
                                            <p class="text-xs text-gray-500">{{ $course->category ?? 'Non catégorisé' }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center text-gray-900 font-medium">
                                    {{ number_format($course->students_count) }}
                                </td>
                                <td class="px-6 py-4 text-center text-gray-900 font-medium">
                                    {{ number_format($course->total_revenue ?? 0, 0) }} FCFA
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <div class="w-16 bg-gray-200 rounded-full h-2">
                                            <div class="bg-emerald-500 h-2 rounded-full" style="width:{{ $course->completion_rate ?? 0 }}%"></div>
                                        </div>
                                        <span class="text-sm text-gray-600">{{ $course->completion_rate ?? 0 }}%</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex items-center justify-center gap-1">
                                        <i class="fas fa-star text-yellow-400 text-xs"></i>
                                        <span class="text-gray-900 font-medium">{{ number_format($course->average_rating ?? 0, 1) }}</span>
                                        <span class="text-xs text-gray-400">({{ $course->reviews_count ?? 0 }})</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="px-2 py-1 text-xs font-medium rounded-full {{ $course->is_published ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                                        {{ $course->is_published ? 'Publié' : 'Brouillon' }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="p-8 text-center text-gray-500">
                <i class="fas fa-chart-bar text-4xl mb-3 opacity-30"></i>
                <p>Aucun cours avec des données pour le moment.</p>
                <a href="{{ route('instructor.courses.create') }}" class="mt-2 inline-block text-indigo-600 hover:text-indigo-700">Créer un cours</a>
            </div>
        @endif
    </div>

    <!-- Actions rapides -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <a href="{{ route('instructor.analytics.revenue') }}" 
           class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 hover:shadow-md transition-all group">
            <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                <i class="fas fa-chart-pie text-green-600 text-lg"></i>
            </div>
            <span class="font-medium text-gray-900">Analyse des revenus</span>
            <p class="text-xs text-gray-500 mt-1">Détail des ventes et commissions</p>
        </a>
        
        <a href="{{ route('instructor.analytics.engagement') }}" 
           class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 hover:shadow-md transition-all group">
            <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                <i class="fas fa-users text-blue-600 text-lg"></i>
            </div>
            <span class="font-medium text-gray-900">Engagement</span>
            <p class="text-xs text-gray-500 mt-1">Taux de complétion et rétention</p>
        </a>
        
        <a href="{{ route('instructor.courses.index') }}" 
           class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 hover:shadow-md transition-all group">
            <div class="w-10 h-10 bg-indigo-100 rounded-xl flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                <i class="fas fa-book-open text-indigo-600 text-lg"></i>
            </div>
            <span class="font-medium text-gray-900">Voir tous les cours</span>
            <p class="text-xs text-gray-500 mt-1">Gérer votre catalogue</p>
        </a>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    function analyticsOverview() {
        return {
            period: '{{ $currentPeriod }}',
            enrollmentsChart: null,
            distributionChart: null,
            
            setPeriod(p) {
                this.period = p;
                window.location.href = '{{ route("instructor.analytics") }}?period=' + p;
            },
            
            init() {
                this.initEnrollmentsChart();
                this.initDistributionChart();
            },
            
            initEnrollmentsChart() {
                const ctx = document.getElementById('enrollmentsTrendChart')?.getContext('2d');
                if (!ctx) return;
                
                if (this.enrollmentsChart) this.enrollmentsChart.destroy();
                
                const data = @json($enrollmentsChart);
                
                this.enrollmentsChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: data.labels,
                        datasets: [{
                            label: 'Inscriptions',
                            data: data.values,
                            borderColor: '#4f46e5',
                            backgroundColor: 'rgba(79, 70, 229, 0.1)',
                            borderWidth: 2,
                            tension: 0.4,
                            fill: true,
                            pointBackgroundColor: '#4f46e5',
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
            },
            
            initDistributionChart() {
                const ctx = document.getElementById('coursesDistributionChart')?.getContext('2d');
                if (!ctx) return;
                
                if (this.distributionChart) this.distributionChart.destroy();
                
                const data = @json($distributionChart);
                
                this.distributionChart = new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: data.labels,
                        datasets: [{
                            data: data.values,
                            backgroundColor: [
                                '#4f46e5', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899'
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
                                cornerRadius: 8
                            }
                        },
                        cutout: '60%'
                    }
                });
            }
        }
    }
</script>
@endpush