@extends('layouts.instructor')

@section('title', 'Analyse des Revenus')
@section('page-title', 'Analyse des Revenus')

@section('breadcrumb')
<nav class="flex" aria-label="Breadcrumb">
    <ol class="flex items-center space-x-2">
        <li><a href="{{ route('instructor.dashboard') }}" class="text-gray-400 hover:text-gray-500"><i class="fas fa-home"></i></a></li>
        <li><i class="fas fa-chevron-right text-gray-300 text-xs"></i></li>
        <li><a href="{{ route('instructor.analytics') }}" class="text-gray-400 hover:text-gray-500">Analyses</a></li>
        <li><i class="fas fa-chevron-right text-gray-300 text-xs"></i></li>
        <li class="text-sm font-medium text-gray-700">Revenus</li>
    </ol>
</nav>
@endsection

@section('content')
<div x-data="revenueAnalytics()" x-init="init()">
    
    @php
        $currency = Auth::user()->settings['preferences']['currency'] ?? 'XOF';
        $currencySymbol = $currency === 'XOF' ? 'FCFA' : ($currency === 'EUR' ? '€' : '$');
    @endphp

    <div class="mb-6">
        <a href="{{ route('instructor.analytics') }}" class="text-indigo-600 hover:text-indigo-700">
            <i class="fas fa-arrow-left mr-2"></i>Retour aux analyses
        </a>
    </div>

    <!-- KPIs -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-5 mb-8">
        <!-- Total revenus -->
        <div class="stat-card bg-white rounded-xl shadow-sm border border-gray-200 p-5 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wider">Revenus totaux</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1">
                        {{ number_format($stats['total_revenue'] ?? 0) }} {{ $currencySymbol }}
                    </p>
                    <p class="text-xs text-gray-400 mt-1">Tous les temps</p>
                </div>
                <div class="w-12 h-12 bg-indigo-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-coins text-indigo-600 text-xl"></i>
                </div>
            </div>
        </div>
        
        <!-- Ce mois -->
        <div class="stat-card bg-white rounded-xl shadow-sm border border-gray-200 p-5 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wider">Ce mois</p>
                    <p class="text-3xl font-bold text-green-600 mt-1">
                        {{ number_format($stats['this_month'] ?? 0) }} {{ $currencySymbol }}
                    </p>
                    @php
                        $lastMonth = $stats['last_month'] ?? 0;
                        $thisMonth = $stats['this_month'] ?? 0;
                        $percentChange = $lastMonth > 0 ? round((($thisMonth - $lastMonth) / $lastMonth) * 100) : 0;
                    @endphp
                    <p class="text-xs {{ $percentChange >= 0 ? 'text-green-600' : 'text-red-600' }} mt-1">
                        <i class="fas fa-arrow-{{ $percentChange >= 0 ? 'up' : 'down' }} text-xs mr-1"></i>
                        {{ abs($percentChange) }}% vs mois dernier
                    </p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-chart-line text-green-600 text-xl"></i>
                </div>
            </div>
        </div>
        
        <!-- Commissions -->
        <div class="stat-card bg-white rounded-xl shadow-sm border border-gray-200 p-5 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wider">Commissions (20%)</p>
                    <p class="text-3xl font-bold text-amber-600 mt-1">
                        {{ number_format(($stats['total_revenue'] ?? 0) * 0.2) }} {{ $currencySymbol }}
                    </p>
                    <p class="text-xs text-gray-400 mt-1">Reversé à la plateforme</p>
                </div>
                <div class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-hand-holding-usd text-amber-600 text-xl"></i>
                </div>
            </div>
        </div>
        
        <!-- Projection -->
        <div class="stat-card bg-white rounded-xl shadow-sm border border-gray-200 p-5 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wider">Projection</p>
                    @php
                        $projected = $stats['projected'] ?? 0;
                        $daysPassed = now()->day;
                        $daysInMonth = now()->daysInMonth;
                    @endphp
                    <p class="text-3xl font-bold text-purple-600 mt-1">
                        {{ number_format($projected) }} {{ $currencySymbol }}
                    </p>
                    <p class="text-xs text-gray-400 mt-1">Estimation fin du mois</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-chart-pie text-purple-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Graphiques -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Évolution mensuelle -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200 flex items-center justify-between">
                <h3 class="font-semibold text-gray-900">Évolution des revenus (6 derniers mois)</h3>
                <span class="text-xs text-gray-500">{{ $currencySymbol }}</span>
            </div>
            <div class="p-6">
                <div style="position: relative; height: 280px;">
                    <canvas id="monthlyChart"></canvas>
                </div>
            </div>
        </div>
        
        <!-- Répartition par cours -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                <h3 class="font-semibold text-gray-900">Répartition par cours</h3>
            </div>
            <div class="p-6">
                <div style="position: relative; height: 280px;">
                    <canvas id="byCourseChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Tableau détaillé par cours -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200 flex items-center justify-between">
            <h3 class="font-semibold text-gray-900">Détail par cours</h3>
            <span class="text-xs text-gray-500">Commission plateforme : 20%</span>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cours</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Étudiants</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Prix unitaire</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Revenu brut</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Commission</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Revenu net</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($byCourse as $course)
                        <tr class="table-row-hover">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    @if($course['thumbnail'])
                                        <img src="{{ $course['thumbnail'] }}" class="w-10 h-10 rounded-lg object-cover">
                                    @else
                                        <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center">
                                            <i class="fas fa-book text-indigo-600"></i>
                                        </div>
                                    @endif
                                    <div>
                                        <p class="font-medium text-gray-900">{{ $course['title'] }}</p>
                                        <p class="text-xs text-gray-500">
                                            @if($course['is_free'])
                                                <span class="text-green-600">Gratuit</span>
                                            @else
                                                <span class="text-amber-600">{{ number_format($course['price'], 2) }} {{ $currencySymbol }}</span>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center text-gray-900 font-medium">
                                {{ $course['students_count'] }}
                            </td>
                            <td class="px-6 py-4 text-center text-gray-600">
                                {{ $course['is_free'] ? 'Gratuit' : number_format($course['price']) . ' ' . $currencySymbol }}
                            </td>
                            <td class="px-6 py-4 text-center text-gray-900">
                                {{ number_format($course['revenue']) }} {{ $currencySymbol }}
                            </td>
                            <td class="px-6 py-4 text-center text-amber-600">
                                {{ number_format($course['revenue'] * 0.2) }} {{ $currencySymbol }}
                            </td>
                            <td class="px-6 py-4 text-center font-semibold text-green-600">
                                {{ number_format($course['revenue'] * 0.8) }} {{ $currencySymbol }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                <i class="fas fa-chart-bar text-4xl mb-3 opacity-30"></i>
                                <p class="text-lg font-medium">Aucun revenu pour le moment</p>
                                <p class="text-sm mt-1">Les revenus de vos cours payants apparaîtront ici</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot class="bg-gray-50">
                    <tr>
                        <td class="px-6 py-4 font-medium text-gray-900">Total</td>
                        <td class="px-6 py-4 text-center font-medium text-gray-900">
                            {{ collect($byCourse)->sum('students_count') }}
                        </td>
                        <td></td>
                        <td class="px-6 py-4 text-center font-bold text-gray-900">
                            {{ number_format(collect($byCourse)->sum('revenue')) }} {{ $currencySymbol }}
                        </td>
                        <td class="px-6 py-4 text-center font-bold text-amber-600">
                            {{ number_format(collect($byCourse)->sum('revenue') * 0.2) }} {{ $currencySymbol }}
                        </td>
                        <td class="px-6 py-4 text-center font-bold text-green-600">
                            {{ number_format(collect($byCourse)->sum('revenue') * 0.8) }} {{ $currencySymbol }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    function revenueAnalytics() {
        return {
            monthlyLabels: @json($monthlyLabels ?? []),
            monthlyData: @json($monthlyData ?? []),
            courseLabels: @json($courseLabels ?? []),
            courseData: @json($courseData ?? []),
            currencySymbol: '{{ $currencySymbol }}',
            
            monthlyChart: null,
            byCourseChart: null,
            
            init() {
                this.initMonthlyChart();
                this.initByCourseChart();
            },
            
            initMonthlyChart() {
                const ctx = document.getElementById('monthlyChart')?.getContext('2d');
                if (!ctx) return;
                
                if (this.monthlyChart) this.monthlyChart.destroy();
                
                const labels = this.monthlyLabels.length > 0 
                    ? this.monthlyLabels 
                    : ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin'];
                    
                const data = this.monthlyData.length > 0 
                    ? this.monthlyData 
                    : [0, 0, 0, 0, 0, 0];
                
                this.monthlyChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Revenus (' + this.currencySymbol + ')',
                            data: data,
                            backgroundColor: [
                                'rgba(16, 185, 129, 0.8)',
                                'rgba(16, 185, 129, 0.7)',
                                'rgba(16, 185, 129, 0.6)',
                                'rgba(16, 185, 129, 0.8)',
                                'rgba(16, 185, 129, 0.7)',
                                'rgba(16, 185, 129, 0.6)',
                            ],
                            borderColor: 'rgb(16, 185, 129)',
                            borderWidth: 1,
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
                                cornerRadius: 8,
                                callbacks: {
                                    label: (context) => context.parsed.y.toLocaleString() + ' ' + this.currencySymbol
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: { color: '#e5e7eb' },
                                ticks: {
                                    callback: (value) => value.toLocaleString() + ' ' + this.currencySymbol
                                }
                            },
                            x: {
                                grid: { display: false }
                            }
                        }
                    }
                });
            },
            
            initByCourseChart() {
                const ctx = document.getElementById('byCourseChart')?.getContext('2d');
                if (!ctx) return;
                
                if (this.byCourseChart) this.byCourseChart.destroy();
                
                const labels = this.courseLabels.length > 0 
                    ? this.courseLabels 
                    : ['Aucun cours'];
                    
                const data = this.courseData.length > 0 
                    ? this.courseData 
                    : [1];
                
                const backgroundColors = [
                    'rgb(79, 70, 229)',
                    'rgb(16, 185, 129)',
                    'rgb(245, 158, 11)',
                    'rgb(239, 68, 68)',
                    'rgb(139, 92, 246)',
                    'rgb(236, 72, 153)',
                    'rgb(6, 182, 212)',
                    'rgb(251, 146, 60)',
                ];
                
                this.byCourseChart = new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: labels,
                        datasets: [{
                            data: data,
                            backgroundColor: backgroundColors.slice(0, labels.length),
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
                                    padding: 20,
                                    usePointStyle: true,
                                    font: { size: 13 },
                                    generateLabels: (chart) => {
                                        const dataset = chart.data.datasets[0];
                                        const total = dataset.data.reduce((a, b) => a + b, 0);
                                        return chart.data.labels.map((label, i) => ({
                                            text: label + ' (' + Math.round((dataset.data[i] / total) * 100) + '%)',
                                            fillStyle: dataset.backgroundColor[i],
                                            hidden: false,
                                            index: i
                                        }));
                                    }
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
                                        const value = context.raw;
                                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                        const percentage = total > 0 ? Math.round((value / total) * 100) : 0;
                                        return value.toLocaleString() + ' ' + this.currencySymbol + ' (' + percentage + '%)';
                                    }
                                }
                            }
                        },
                        cutout: '65%'
                    }
                });
            }
        }
    }
    
    // Nettoyage
    window.addEventListener('beforeunload', function() {
        if (revenueAnalytics().monthlyChart) revenueAnalytics().monthlyChart.destroy();
        if (revenueAnalytics().byCourseChart) revenueAnalytics().byCourseChart.destroy();
    });
</script>
@endpush